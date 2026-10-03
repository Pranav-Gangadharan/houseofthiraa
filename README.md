# House of Thiraa

Online shop for midis, maxis and co-ords. Laravel 13, Blade, vanilla JS. No database server needed (SQLite).

Free shipping across India. Prepaid only. No returns or exchanges.

## Run it

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate   # skip if .env already exists
php artisan migrate --seed                          # --seed adds 9 sample pieces
php artisan storage:link                            # product photos
npm run build                                       # or `npm run dev` while editing

php artisan serve                                   # http://127.0.0.1:8000
```

Tests: `php artisan test`

## Day to day

| What | Where |
| --- | --- |
| Add pieces, upload photos, mark sizes sold out | `/admin/products` |
| See paid orders, add a tracking number | `/admin/orders` |

Log in to `/admin` with `ADMIN_PASSWORD` from `.env`. Adding a tracking number marks an order shipped, and customers see it on their order page.

Until photos are uploaded, each piece shows a pixel-drawn dress in its own colour. Upload a photo and it replaces the drawing.

## Taking real payments (Razorpay)

1. Put `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` in `.env`.
2. In the Razorpay dashboard add a webhook to `https://YOUR-DOMAIN/razorpay/webhook` for `payment.captured` and `order.paid`, and put its secret in `RAZORPAY_WEBHOOK_SECRET`. This catches customers who pay and then close the tab.

Without keys, a local-only "Complete test payment" button stands in so the whole flow can be tried. In production with no keys the pay page returns 503.

## Before launch

- `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, strong `ADMIN_PASSWORD`.
- Replace the sample size chart in `config/shop.php` with the brand's real measurements.
- Set `SHOP_WHATSAPP`, `SHOP_INSTAGRAM`, `SHOP_EMAIL` (shown in the footer when set).
- Configure mail if you want order emails. Nothing is sent yet.

## Where things live

- `resources/js/pixels.js`: the pixel background and the silhouette that assembles on the home page.
- `resources/js/dress.js`: the pixel dress previews.
- `resources/css/app.css`: all styling. Colours and radii are tokens at the top.
- `config/shop.php`: categories, sizes, size chart, states, contact details.
- `app/Support/Cart.php`: the bag. Prices always come from the database.
- `scripts/make-brand-assets.mjs`: rebuilds `public/brand/*` from `houseofthira.png` (`npm run brand`).
