<?php

return [

    'categories' => [
        'midi' => 'Midi',
        'maxi' => 'Maxi',
        'coord' => 'Co-ord',
    ],

    // Which pixel-field mood each category steers the background toward.
    'moods' => [
        'midi' => 'bell',
        'maxi' => 'drape',
        'coord' => 'pair',
    ],

    // Home page banner slides: full-width images, no text on top. Paths are relative to public/.
    // Later this list will come from the admin dashboard; ShopController::banners() is the only reader.
    //   image         wide image for laptop and desktop (about 1920 x 820)
    //   mobile_image  optional portrait image for phones (about 1080 x 1350); falls back to image
    //   link          where a click goes (optional)
    //   alt           describe the picture for screen readers
    //   focus         which part of the image to keep when it is cropped (CSS object-position)
    'banners' => [
        [
            'image' => 'brand/hero.jpg',
            'mobile_image' => null,
            'link' => '/#shop',
            'alt' => 'Two women in a red and a green Thiraa dress walking through a sunlit courtyard',
            'focus' => 'center 40%',
        ],
    ],

    // One line about each shape, and the colour its pixel dress is drawn in on the home page.
    'category_notes' => [
        'midi' => ['Knee to calf. Easy to wear to work, lunch or a wedding.', '#9d0b1b'],
        'maxi' => ['Floor length, with a hem that moves when you do.', '#2c3a7a'],
        'coord' => ['Two pieces cut together. Wear them as a set or apart.', '#3c6b63'],
    ],

    // Pieces per page on the shop page.
    'per_page' => 8,

    'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],

    // Inches: size, bust, waist, hip. Sample values. Replace with the brand's real measurements.
    'size_chart' => [
        ['XS', 32, 26, 35],
        ['S', 34, 28, 37],
        ['M', 36, 30, 39],
        ['L', 38, 32, 41],
        ['XL', 40, 34, 43],
        ['XXL', 42, 36, 45],
    ],

    'max_quantity' => 5,

    'admin_password' => env('ADMIN_PASSWORD'),

    'razorpay' => [
        'key' => env('RAZORPAY_KEY_ID'),
        'secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'contact' => [
        'whatsapp' => env('SHOP_WHATSAPP'),   // digits with country code, e.g. 919876543210
        'instagram' => env('SHOP_INSTAGRAM'), // handle without @
        'email' => env('SHOP_EMAIL'),
    ],

    'states' => [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat',
        'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh',
        'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
        'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand',
        'West Bengal', 'Andaman and Nicobar Islands', 'Chandigarh',
        'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 'Jammu and Kashmir', 'Ladakh',
        'Lakshadweep', 'Puducherry',
    ],

];
