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
