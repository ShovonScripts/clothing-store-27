<?php

declare(strict_types=1);

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'integer' => 'The :attribute field must be an integer.',
    'min' => [
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'max' => [
        'numeric' => 'The :attribute field may not be greater than :max.',
        'string' => 'The :attribute field may not be greater than :max characters.',
    ],
    'email' => 'The :attribute field must be a valid email address.',
    'unique' => 'The :attribute has already been taken.',
    'exists' => 'The selected :attribute is invalid.',
    'in' => 'The selected :attribute is invalid.',
    'regex' => 'The :attribute format is invalid.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'numeric' => 'The :attribute field must be numeric.',
    'digits' => 'The :attribute field must be :digits digits.',
    'digits_between' => 'The :attribute field must be between :min and :max digits.',
    'size' => [
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'between' => [
        'numeric' => 'The :attribute field must be between :min and :max.',
        'string' => 'The :attribute field must be between :min and :max characters.',
    ],
    'attributes' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'phone' => 'Phone Number',
        'address' => 'Address',
        'district_id' => 'District',
        'area' => 'Area',
        'variant_id' => 'Variant',
        'quantity' => 'Quantity',
        'order_number' => 'Order Number',
        'bkash_trx_id' => 'bKash TrxID',
        'bkash_sender_number' => 'Sender Number',
        'bkash_amount_sent' => 'Amount Sent',
        'coupon_code' => 'Coupon Code',
    ],
];
