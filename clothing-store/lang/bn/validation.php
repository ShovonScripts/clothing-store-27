<?php

declare(strict_types=1);

return [
    'required' => ':attribute আবশ্যক।',
    'string' => ':attribute অবশ্যই টেক্সট হতে হবে।',
    'integer' => ':attribute অবশ্যই সংখ্যা হতে হবে।',
    'min' => [
        'numeric' => ':attribute কমপক্ষে :min হতে হবে।',
        'string' => ':attribute কমপক্ষে :min অক্ষর হতে হবে।',
    ],
    'max' => [
        'numeric' => ':attribute সর্বোচ্চ :max হতে পারে।',
        'string' => ':attribute সর্বোচ্চ :max অক্ষর হতে পারে।',
    ],
    'email' => ':attribute অবশ্যই একটি সঠিক ইমেইল ঠিকানা হতে হবে।',
    'unique' => ':attribute ইতিমধ্যে ব্যবহার করা হয়েছে।',
    'exists' => 'নির্বাচিত :attribute অবৈধ।',
    'in' => 'নির্বাচিত :attribute অবৈধ।',
    'regex' => ':attribute ফরম্যাট সঠিক নয়।',
    'confirmed' => ':attribute নিশ্চিতকরণ মিলছে না।',
    'numeric' => ':attribute অবশ্যই সংখ্যা হতে হবে।',
    'digits' => ':attribute অবশ্যই :digits অঙ্কের হতে হবে।',
    'digits_between' => ':attribute অবশ্যই :min থেকে :max অঙ্কের মধ্যে হতে হবে।',
    'size' => [
        'numeric' => ':attribute অবশ্যই :size হতে হবে।',
        'string' => ':attribute অবশ্যই :size অক্ষরের হতে হবে।',
    ],
    'between' => [
        'numeric' => ':attribute অবশ্যই :min এবং :max এর মধ্যে হতে হবে।',
        'string' => ':attribute অবশ্যই :min এবং :max অক্ষরের মধ্যে হতে হবে।',
    ],
    'attributes' => [
        'name' => 'নাম',
        'email' => 'ইমেইল',
        'password' => 'পাসওয়ার্ড',
        'phone' => 'ফোন নম্বর',
        'address' => 'ঠিকানা',
        'district_id' => 'জেলা',
        'area' => 'এরিয়া',
        'variant_id' => 'ভেরিয়েন্ট',
        'quantity' => 'পরিমাণ',
        'order_number' => 'অর্ডার নম্বর',
        'bkash_trx_id' => 'bKash TrxID',
        'bkash_sender_number' => 'প্রেরকের নম্বর',
        'bkash_amount_sent' => 'পাঠানো রকম',
        'coupon_code' => 'কাপন কোড',
    ],
];
