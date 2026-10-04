<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use Database\Seeders\SampleImagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config([
            'shop.admin_password' => 'secret-for-tests',
            'shop.razorpay' => ['key' => 'rzp_test_key', 'secret' => 'rzp_test_secret', 'webhook_secret' => 'hook_secret'],
        ]);
    }

    private function dress(array $attributes = []): Product
    {
        return Product::create($attributes + [
            'name' => 'Kavya', 'category' => 'midi', 'price' => 1799,
            'sizes' => ['S', 'M', 'L'], 'images' => [], 'color' => '#9d0b1b',
        ]);
    }

    private function address(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Asha Rao', 'phone' => '+91 98765 43210', 'email' => 'asha@example.com',
            'address' => '12 Lake View Road', 'pincode' => '560001', 'city' => 'Bengaluru',
            'state' => 'Karnataka', 'agree' => '1',
        ];
    }

    public function test_home_lists_active_pieces_and_filters_by_category(): void
    {
        $this->dress(['name' => 'Kavya']);
        $this->dress(['name' => 'Anvi', 'category' => 'maxi']);
        $this->dress(['name' => 'Hidden', 'is_active' => false]);

        $this->get('/')->assertOk()->assertSee('Kavya')->assertSee('Anvi')->assertDontSee('Hidden');

        // server-side filter hides the other categories (they render with the hidden attribute)
        $html = $this->get('/?c=maxi')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/data-category="midi"\s+hidden/', $html);
        $this->assertDoesNotMatchRegularExpression('/data-category="maxi"\s+hidden/', $html);
    }

    public function test_search_finds_pieces_by_name_or_description(): void
    {
        $this->dress(['name' => 'Kavya', 'description' => 'Smocked back midi']);
        $this->dress(['name' => 'Anvi', 'category' => 'maxi', 'description' => 'Floor length']);

        $this->get('/?q=anvi')->assertOk()->assertSee('Anvi')->assertDontSee('Kavya');
        $this->get('/?q=smocked')->assertOk()->assertSee('Kavya')->assertDontSee('Anvi');
        $this->get('/?q=maxi')->assertOk()->assertSee('Anvi')->assertDontSee('Kavya');
        $this->get('/?q=velvet')->assertOk()->assertSee('Nothing matches that yet.');
    }

    public function test_home_banner_shows_configured_images_and_skips_missing_ones(): void
    {
        config(['shop.banners' => [
            ['image' => 'brand/hero.jpg', 'link' => '/?c=midi#shop', 'alt' => 'Festive midis'],
            ['image' => 'brand/does-not-exist.jpg', 'alt' => 'Missing'],
        ]]);

        $this->get('/')->assertOk()
            ->assertSee(asset('brand/hero.jpg'), false)
            ->assertSee('href="/?c=midi#shop"', false)
            ->assertSee('Festive midis')
            ->assertDontSee('does-not-exist.jpg')
            ->assertDontSee('data-dot', false); // a single banner needs no dots
    }

    public function test_sample_photos_fill_in_only_pieces_without_photos(): void
    {
        Storage::fake('public');
        $bare = $this->dress(['name' => 'Kavya', 'images' => []]);
        $edited = $this->dress(['name' => 'Meera', 'images' => ['products/uploaded-in-admin.jpg']]);
        $unknown = $this->dress(['name' => 'Brand New Piece', 'images' => []]);

        $this->seed(SampleImagesSeeder::class);

        $this->assertSame(['products/kavya-1.jpg', 'products/kavya-2.jpg'], $bare->fresh()->images);
        Storage::disk('public')->assertExists('products/kavya-1.jpg');
        $this->assertSame(['products/uploaded-in-admin.jpg'], $edited->fresh()->images);
        $this->assertSame([], $unknown->fresh()->images);
    }

    public function test_a_size_is_required_and_sold_out_sizes_are_refused(): void
    {
        $product = $this->dress(['sizes' => ['M']]);

        $this->post('/bag', ['product' => $product->id])->assertSessionHasErrors('size');
        $this->post('/bag', ['product' => $product->id, 'size' => 'XL'])->assertSessionHasErrors('size');
        $this->assertSame(0, app(Cart::class)->count());
    }

    public function test_buy_now_goes_straight_to_checkout(): void
    {
        $product = $this->dress();

        $this->post('/bag', ['product' => $product->id, 'size' => 'M', 'buy' => 1])->assertRedirect('/checkout');
        $this->get('/checkout')->assertOk()->assertSee('Kavya')->assertSee('₹1,799');
    }

    public function test_checkout_prices_the_order_from_the_database_and_ships_free(): void
    {
        $product = $this->dress(['price' => 2000]);
        $this->post('/bag', ['product' => $product->id, 'size' => 'M']);
        $this->post('/bag', ['product' => $product->id, 'size' => 'M']);

        // The customer cannot influence price or shipping through the form.
        $response = $this->post('/checkout', $this->address(['total' => 1, 'price' => 1, 'shipping' => 500]));

        $order = Order::firstOrFail();
        $response->assertRedirect(route('pay', $order));

        $this->assertSame('pending', $order->status);
        $this->assertSame(4000, $order->total);
        $this->assertSame(0, $order->shipping);
        $this->assertSame('9876543210', $order->phone); // +91 and spaces stripped
        $this->assertSame(2, $order->items->first()->quantity);
    }

    public function test_checkout_rejects_bad_details_and_requires_the_no_returns_confirmation(): void
    {
        $product = $this->dress();
        $this->post('/bag', ['product' => $product->id, 'size' => 'M']);

        $this->post('/checkout', $this->address(['phone' => '12345', 'pincode' => '0123', 'agree' => null]))
            ->assertSessionHasErrors(['phone', 'pincode', 'agree']);

        $this->assertSame(0, Order::count());
    }

    public function test_a_valid_razorpay_signature_marks_the_order_paid_and_empties_the_bag(): void
    {
        $product = $this->dress();
        $this->post('/bag', ['product' => $product->id, 'size' => 'M']);
        $this->post('/checkout', $this->address());
        $order = Order::firstOrFail();
        $order->update(['razorpay_order_id' => 'order_ABC']);

        $signature = hash_hmac('sha256', 'order_ABC|pay_XYZ', 'rzp_test_secret');

        $this->post("/pay/{$order->number}/verify", [
            'razorpay_order_id' => 'order_ABC', 'razorpay_payment_id' => 'pay_XYZ', 'razorpay_signature' => $signature,
        ])->assertRedirect();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('pay_XYZ', $order->fresh()->razorpay_payment_id);
        $this->assertSame(0, app(Cart::class)->count());
    }

    public function test_a_forged_signature_does_not_mark_the_order_paid(): void
    {
        $product = $this->dress();
        $this->post('/bag', ['product' => $product->id, 'size' => 'M']);
        $this->post('/checkout', $this->address());
        $order = Order::firstOrFail();
        $order->update(['razorpay_order_id' => 'order_ABC']);

        $this->post("/pay/{$order->number}/verify", [
            'razorpay_order_id' => 'order_ABC', 'razorpay_payment_id' => 'pay_XYZ', 'razorpay_signature' => 'forged',
        ])->assertSessionHasErrors('payment');

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(1, app(Cart::class)->count()); // bag kept so they can retry
    }

    public function test_the_webhook_marks_paid_only_with_a_valid_signature(): void
    {
        $order = Order::create([
            'number' => 'HT-TEST01', 'name' => 'A', 'phone' => '9876543210', 'email' => 'a@b.co', 'address' => 'x',
            'city' => 'c', 'state' => 's', 'pincode' => '560001', 'subtotal' => 100, 'total' => 100, 'razorpay_order_id' => 'order_W',
        ]);
        $body = json_encode(['event' => 'payment.captured', 'payload' => ['payment' => ['entity' => ['id' => 'pay_W', 'order_id' => 'order_W']]]]);

        $this->call('POST', '/razorpay/webhook', [], [], [], ['HTTP_X_RAZORPAY_SIGNATURE' => 'nope', 'CONTENT_TYPE' => 'application/json'], $body)->assertStatus(400);
        $this->assertSame('pending', $order->fresh()->status);

        $good = hash_hmac('sha256', $body, 'hook_secret');
        $this->call('POST', '/razorpay/webhook', [], [], [], ['HTTP_X_RAZORPAY_SIGNATURE' => $good, 'CONTENT_TYPE' => 'application/json'], $body)->assertNoContent();
        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_the_confirmation_page_needs_a_signed_link_and_a_paid_order(): void
    {
        $order = Order::create([
            'number' => 'HT-TEST02', 'name' => 'Asha Rao', 'phone' => '9876543210', 'email' => 'a@b.co', 'address' => 'x',
            'city' => 'c', 'state' => 's', 'pincode' => '560001', 'subtotal' => 100, 'total' => 100,
        ]);

        $this->get("/order/{$order->number}")->assertForbidden();
        $this->get(URL::signedRoute('order.show', $order))->assertNotFound(); // signed but unpaid

        $order->markPaid('pay_1');
        $this->get(URL::signedRoute('order.show', $order))->assertOk()->assertSee('Thank you, Asha');
    }

    public function test_admin_needs_the_password_and_tracking_marks_an_order_shipped(): void
    {
        $this->get('/admin/orders')->assertRedirect('/admin/login');
        $this->post('/admin/login', ['password' => 'wrong'])->assertSessionHasErrors('password');

        $order = Order::create([
            'number' => 'HT-TEST03', 'name' => 'A', 'phone' => '9876543210', 'email' => 'a@b.co', 'address' => 'x',
            'city' => 'c', 'state' => 's', 'pincode' => '560001', 'subtotal' => 100, 'total' => 100,
        ]);
        $order->markPaid('pay_1');

        $this->post('/admin/login', ['password' => 'secret-for-tests'])->assertRedirect('/admin/orders');
        $this->get('/admin/orders')->assertOk()->assertSee('HT-TEST03');

        $this->patch("/admin/orders/{$order->number}", ['courier' => 'Delhivery', 'tracking_number' => 'DL123'])->assertRedirect();
        $this->assertSame('shipped', $order->fresh()->status);
    }

    public function test_admin_can_add_a_product(): void
    {
        $this->withSession(['admin' => true]);

        $this->post('/admin/products', [
            'name' => 'Noor', 'category' => 'maxi', 'price' => 2699, 'color' => '#5a1730',
            'sizes' => ['S', 'M'], 'is_active' => 1,
        ])->assertRedirect('/admin/products');

        $product = Product::where('name', 'Noor')->firstOrFail();
        $this->assertSame('noor', $product->slug);
        $this->assertSame(['S', 'M'], $product->sizes);
        $this->get('/p/noor')->assertOk()->assertSee('₹2,699');
    }

    public function test_uploaded_photos_replace_the_pixel_preview_and_can_be_removed(): void
    {
        Storage::fake('public');
        $this->withSession(['admin' => true]);
        $product = $this->dress();

        $this->put("/admin/products/{$product->slug}", [
            'name' => 'Kavya', 'category' => 'midi', 'price' => 1799, 'color' => '#9d0b1b', 'sizes' => ['M'],
            'is_active' => 1, 'photos' => [UploadedFile::fake()->image('front.jpg', 600, 800)],
        ])->assertRedirect('/admin/products');

        $path = $product->fresh()->images[0];
        Storage::disk('public')->assertExists($path);
        $this->get('/p/kavya')->assertSee('/storage/'.$path, false)->assertDontSee('data-dress', false);

        $this->put("/admin/products/{$product->slug}", [
            'name' => 'Kavya', 'category' => 'midi', 'price' => 1799, 'color' => '#9d0b1b', 'sizes' => ['M'],
            'is_active' => 1, 'remove' => [$path],
        ]);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame([], $product->fresh()->images);
    }

    public function test_about_and_contact_pages_are_linked_from_the_footer(): void
    {
        $this->get('/')->assertSee(route('about'), false)->assertSee(route('contact'), false);
        $this->get('/about')->assertOk()->assertSee("women's clothing label", false)->assertSee('Ordering, in three steps');

        $this->get('/contact')->assertOk()->assertSee('Contact details will be added here soon.');

        config(['shop.contact' => ['whatsapp' => '919876543210', 'instagram' => 'houseofthiraa', 'email' => 'hello@example.com']]);
        $this->get('/contact')->assertOk()
            ->assertSee('https://wa.me/919876543210', false)
            ->assertSee('https://instagram.com/houseofthiraa', false)
            ->assertSee('mailto:hello@example.com', false)
            ->assertSee('+91 98765 43210')
            ->assertSee('@houseofthiraa')
            ->assertSee('Where is my order?');
    }
}
