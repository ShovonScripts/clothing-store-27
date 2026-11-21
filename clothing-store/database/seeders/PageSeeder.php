<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'title_bn' => 'আমাদের সম্পর্কে',
                'body' => '<p>Welcome to {BRAND}, your destination for quality men\'s fashion in Bangladesh.</p><p>We believe every man deserves to look his best without breaking the bank. Our curated collection brings together the latest trends and timeless classics, all carefully selected for quality, comfort, and style.</p><p>From crisp formal shirts to comfortable t-shirts, from traditional panjabis to modern jeans - we have something for every occasion.</p><p>Shop with confidence knowing every order is personally approved and every delivery is tracked to your door.</p>',
                'body_bn' => '<p>{BRAND}-এ আপনাকে স্বাগতম, বাংলাদেশে গুণগর পুরুষ ফ্যাশনের জন্য আপনার গন্তব্য।</p><p>আমরা মনে করি, প্রতিটি পুরুষ haqqeeper ভালো দেখার অধিকারী, ব্যাংক ভাঙা ছাড়া। আমাদের কিউরেটেড সংকলন সর্বশেষ ট্রেন্ড এবং কালজয়ী ক্লাসিক একসাথে নিয়ে আসে, সবই গুণগত, आरাম এবং শৈলীর জন্য সতর্কতার সাথে নির্বাচিত।</p><p>তাজা ফরম্যাল শার্ট থেকে আরামদায়ক টি-শার্ট, ঐতিহ্যবাহী পঞ্জাবি থেকে আধুনিক জিনস - আমাদের কাছে প্রতিটি অনুষ্ঠানের জন্য কিছু আছে।</p><p>আত্মবিশ্বাসের সাথে কেনাকাটা করুন, কারণ প্রতিটি অর্ডার ব্যক্তিগতভাবে অনুমোদিত এবং প্রতিটি ডেলিভারি আপনার দরবाजে ট্র্যাক করা হয়।</p>',
                'is_active' => true,
                'meta_title' => 'About {BRAND}',
                'meta_description' => 'Learn about {BRAND} - Quality men\'s fashion store in Bangladesh',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'title_bn' => 'যোগাযোগ করুন',
                'body' => '<p>Have questions? We\'d love to hear from you.</p><p><strong>Phone:</strong> 01XXXXXXXXX<br><strong>WhatsApp:</strong> 8801XXXXXXXXX<br><strong>Email:</strong> hello@example.com</p><p>Business Hours: Saturday - Thursday, 9:00 AM - 6:00 PM</p>',
                'body_bn' => '<p>প্রশ্ন আছে? আমরা আপনার কথা শুনতে চাই।</p><p><strong>ফোন:</strong> 01XXXXXXXXX<br><strong>WhatsApp:</strong> 8801XXXXXXXXX<br><strong>ইমেইল:</strong> hello@example.com</p><p>ব্যবসায়িক সময়: শনিবার - বৃহস্পতিবার, সকাল ৯:০০ - বিকাল ৬:০০</p>',
                'is_active' => true,
                'meta_title' => 'Contact {BRAND}',
                'meta_description' => 'Contact {BRAND} for men\'s fashion inquiries',
            ],
            [
                'slug' => 'delivery-info',
                'title' => 'Delivery Information',
                'title_bn' => 'ডেলিভারি তথ্য',
                'body' => '<h3>Delivery Zones & Charges</h3><p><strong>Inside Dhaka:</strong> ৳70 (1-2 business days)</p><p><strong>Outside Dhaka:</strong> ৳130 (2-5 business days)</p><h3>Free Delivery</h3><p>Free delivery on orders over ৳0 (threshold configurable)</p><h3>Courier Partners</h3><p>We work with trusted courier services including Steadfast, Pathao, RedX, and Paperfly.</p><h3>Cash on Delivery</h3><p>Pay when you receive your order. Available everywhere we deliver.</p>',
                'body_bn' => '<h3>ডেলিভারি জোন এবং চার্জ</h3><p><strong>ঢাকা শহরের ভিতর:</strong> ৭০ টাকা (১-২ কর্মদিন)</p><p><strong>ঢাকা শহরের বাইরে:</strong> ১৩০ টাকা (২-৫ কর্মদিন)</p><h3>নিঃশুল্ক ডেলিভারি</h3><p>০ টাকার উপরের অর্ডারে বিনামূল্যে ডেলিভারি (থ্রেশহোল্ড কনফিগারেবল)</p><h3>কুরিয়ার পার্টনার</h3><p>আমরা stedfast, পাথাও, রেডএক্স, এবং পেপারফ্লাই এর মতো বিশ্বস্ত কুরিয়ার সার্ভিসের সাথে কাজ করি।</p><h3>ক্যাশ অন ডেলিভারি</h3><p>পণ্য হাতে পাওয়ার পর টাকা দিন। আমরা যেখানে ডেলিভারি দেই সেখানে উপলব্ধ।</p>',
                'is_active' => true,
                'meta_title' => 'Delivery Information - {BRAND}',
                'meta_description' => 'Delivery charges, zones, and courier information for {BRAND}',
            ],
            [
                'slug' => 'return-exchange',
                'title' => 'Returns & Exchanges',
                'title_bn' => 'রিটার্ন অ্যান্ড এক্সচেঞ্জ',
                'body' => '<h3>3-Day Exchange Policy</h3><p>We offer a 3-day exchange for size issues. The item must be unworn, unwashed, and with original tags attached.</p><h3>How to Exchange</h3><ol><li>Contact us via WhatsApp or phone within 3 days of delivery</li><li>Provide your order number and the size you need</li><li>Our courier will pick up the item and deliver the replacement</li></ol><h3>Returns</h3><p>Full returns are accepted for defective items only. Contact us within 24 hours with photos.</p><h3>Non-Returnable Items</h3><p>Innerwear, accessories, and sale items cannot be returned or exchanged.</p>',
                'body_bn' => '<h3>৩ দিনের এক্সচেঞ্জ পলিসি</h3><p>আমরা সাইজ সমস্যার জন্য ৩ দিনের এক্সচেঞ্জ অফার করি। পণ্যটি পরানattaque, ধোয়া Attack, এবং মূল ট্যাগ থাকতে হবে।</p><h3>কীভাবে এক্সচেঞ্জ করবেন</h3><ol><li>ডেলিভারির ৩ দিনের মধ্যে আমাদের সাথে WhatsApp বা ফোনে যোগাযোগ করুন</li><li>আপনার অর্ডার নম্বর এবং প্রয়োজনীয় সাইজ দিন</li><li>আমাদের কুরিয়ার পণ্যটি সংগ্রহ করে প্রতিস্থাপন পণ্য ডেলিভারি করবে</li></ol><h3>রিটার্ন</h3><p>সম্পূর্ণ রিটার্ন শুধু ত্রুটিপূর্ণ পণ্যের জন্য গ্রহণযোগ্য। ফটো সহ ২৪ ঘন্টার মধ্যে যোগাযোগ করুন।</p><h3>নন-রিটার্নেবল পণ্য</h3><p>ইনারওয়্যার, এক্সেসরিজ, এবং সেল পণ্য রিটার্ন বা এক্সচেঞ্জ করা যায় না।</p>',
                'is_active' => true,
                'meta_title' => 'Returns & Exchanges - {BRAND}',
                'meta_description' => 'Return and exchange policy for {BRAND}',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'title_bn' => 'প্রাইভেসি পলিসি',
                'body' => '<p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.</p><h3>Information We Collect</h3><ul><li>Name, phone number, address for order processing</li><li>Email (optional) for order updates</li><li>Payment details (bKash TrxID) for verification</li></ul><h3>How We Use Your Information</h3><ul><li>Process and deliver your orders</li><li>Send order updates via SMS/WhatsApp</li><li>Verify bKash payments</li><li>Improve our services</li></ul><h3>Data Protection</h3><p>We never sell your data. All information is stored securely and used only for order fulfillment.</p>',
                'body_bn' => '<p>আপনার গোপনীয়তা আমাদের জন্য গুরুত্বপূর্ণ। এই নীতি ব্যাখ্যা করে আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং রক্ষা করি।</p><h3>আমরা কী তথ্য সংগ্রহ করি</h3><ul><li>অর্ডার প্রসেসিং için নাম, ফোন নম্বর, ঠিকানা</li><li>অর্ডার আপডেটের জন্য ইমেইল (ঐচ্ছিক)</li><li>পেমেন্ট যাচাইয়ের জন্য বিকাশ TrxID</li></ul><h3>আমরা কীভাবে আপনার তথ্য ব্যবহার করি</h3><ul><li>আপনার অর্ডার প্রসেস এবং ডেলিভারি করুন</li><li>SMS/WhatsApp-এ অর্ডার আপডেট পাঠান</li><li>বিকাশ পেমেন্ট যাচাই করুন</li><li>আমাদের সার্ভিস উন্নত করুন</li></ul><h3>ডেটা সুরক্ষা</h3><p>আমরা কখনো আপনার ডেটা বিক্রি করি না। সকল তথ্য নিরাপদে সংরক্ষিত থাকে এবং শুধু অর্ডার পূরণের জন্য ব্যবহৃত হয়।</p>',
                'is_active' => true,
                'meta_title' => 'Privacy Policy - {BRAND}',
                'meta_description' => 'Privacy policy for {BRAND} men\'s fashion store',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'title_bn' => 'সার্ভিস শর্তাবলী',
                'body' => '<p>By placing an order on {BRAND}, you agree to these terms.</p><h3>Ordering</h3><p>All orders are subject to admin approval. We reserve the right to reject any order.</p><h3>Pricing</h3><p>Prices are in BDT (৳) and include VAT where applicable. Prices may change without notice.</p><h3>Payments</h3><p>Cash on Delivery: Pay upon delivery.<br>bKash Send Money: Send to our number, then enter TrxID for verification. Orders await admin verification.</p><h3>Delivery</h3><p>Delivery times are estimates. We are not liable for courier delays.</p><h3>Disputes</h3><p>Any disputes will be resolved under Bangladesh law.</p>',
                'body_bn' => '<p>{BRAND}-এ অর্ডার দেওয়া মানে আপনি এই শর্তাবলী স্বীকার করেছেন।</p><h3>অর্ডার</h3><p>সকল অর্ডার অ্যাডমিন অনুমোদনের আওতায়। আমরা যেকোনো অর্ডার প্রত্যাখ্যান করার অধিকার রাখি।</p><h3>মূল্য</h3><p>মূল্য BDT (৳) এ এবং প্রযোজ্য কর সহ। মূল্য tanpa পূর্ব सूचना পরিবর্তিত হতে পারে।</p><h3>পেমেন্ট</h3><p>ক্যাশ অন ডেলিভারি: ডেলিভারির সময় টাকা দিন।<br>বিকাশ সেন্ড মানি: আমাদের নম্বরে টাকা পাঠান, তারপর যাচাইয়ের জন্য TrxID দিন। অর্ডার অ্যাডমিন যাচাইয়ের জন্য অপেক্ষা করবে।</p><h3>ডেলিভারি</h3><p>ডেলিভারি সময় অনুমানিক। কুরিয়ার বিলম্বের জন্য আমরা দায়বদ্ধ নই।</p><h3>বিবাদ</h3><p>যেকোনো বিবাদ বাংলাদেশ আইনের অধীনে সমাধান করা হবে।</p>',
                'is_active' => true,
                'meta_title' => 'Terms of Service - {BRAND}',
                'meta_description' => 'Terms and conditions for {BRAND}',
            ],
            [
                'slug' => 'size-guide',
                'title' => 'Size Guide',
                'title_bn' => 'সাইজ গাইড',
                'body' => '<h3>How to Measure</h3><p>Use a measuring tape and measure over light clothing.</p><h3>Topwear (T-Shirts, Polos, Shirts)</h3><table><thead><tr><th>Size</th><th>Chest (inches)</th><th>Length (inches)</th><th>Shoulder (inches)</th></tr></thead><tbody><tr><td>S</td><td>36-38</td><td>26-27</td><td>16-17</td></tr><tr><td>M</td><td>38-40</td><td>27-28</td><td>17-18</td></tr><tr><td>L</td><td>40-42</td><td>28-29</td><td>18-19</td></tr><tr><td>XL</td><td>42-44</td><td>29-30</td><td>19-20</td></tr><tr><td>XXL</td><td>44-46</td><td>30-31</td><td>20-21</td></tr><tr><td>3XL</td><td>46-48</td><td>31-32</td><td>21-22</td></tr></tbody></table><h3>Bottomwear (Pants, Jeans, Trousers)</h3><table><thead><tr><th>Size</th><th>Waist (inches)</th><th>Hip (inches)</th><th>Inseam (inches)</th></tr></thead><tbody><tr><td>28</td><td>28-29</td><td>36-37</td><td>31-32</td></tr><tr><td>30</td><td>30-31</td><td>38-39</td><td>31-32</td></tr><tr><td>32</td><td>32-33</td><td>40-41</td><td>31-32</td></tr><tr><td>34</td><td>34-35</td><td>42-43</td><td>31-32</td></tr><tr><td>36</td><td>36-37</td><td>44-45</td><td>31-32</td></tr><tr><td>38</td><td>38-39</td><td>46-47</td><td>31-32</td></tr><tr><td>40</td><td>40-41</td><td>48-49</td><td>31-32</td></tr></tbody></table><h3>Panjabi Sizes</h3><table><thead><tr><th>Size</th><th>Chest (inches)</th><th>Length (inches)</th></tr></thead><tbody><tr><td>38</td><td>38</td><td>42</td></tr><tr><td>40</td><td>40</td><td>43</td></tr><tr><td>42</td><td>42</td><td>44</td></tr><tr><td>44</td><td>44</td><td>45</td></tr></tbody></table>',
                'body_bn' => '<h3>কীভাবে মাপবেন</h3><p>মাপনির ফीतা ব্যবহার করুন এবং হালকা পোশাকের উপর থেকে মাপুন।</p><h3>টপওয়্যার (টি-শার্ট, পোলো, শার্ট)</h3><table><thead><tr><th>সাইজ</th><th>চেস্ট (ইঞ্চি)</th><th>লেংথ (ইঞ্চি)</th><th>শোল্ডার (ইঞ্চি)</th></tr></thead><tbody><tr><td>S</td><td>36-38</td><td>26-27</td><td>16-17</td></tr><tr><td>M</td><td>38-40</td><td>27-28</td><td>17-18</td></tr><tr><td>L</td><td>40-42</td><td>28-29</td><td>18-19</td></tr><tr><td>XL</td><td>42-44</td><td>29-30</td><td>19-20</td></tr><tr><td>XXL</td><td>44-46</td><td>30-31</td><td>20-21</td></tr><tr><td>3XL</td><td>46-48</td><td>31-32</td><td>21-22</td></tr></tbody></table><h3>বটমওয়্যার (প্যান্ট, জিনস, ট্রাউজার্স)</h3><table><thead><tr><th>সাইজ</th><th>ওয়েষ্ট (ইঞ্চি)</th><th>হিপ (ইঞ্চি)</th><th>ইনসিম (ইঞ্চি)</th></tr></thead><tbody><tr><td>28</td><td>28-29</td><td>36-37</td><td>31-32</td></tr><tr><td>30</td><td>30-31</td><td>38-39</td><td>31-32</td></tr><tr><td>32</td><td>32-33</td><td>40-41</td><td>31-32</td></tr><tr><td>34</td><td>34-35</td><td>42-43</td><td>31-32</td></tr><tr><td>36</td><td>36-37</td><td>44-45</td><td>31-32</td></tr><tr><td>38</td><td>38-39</td><td>46-47</td><td>31-32</td></tr><tr><td>40</td><td>40-41</td><td>48-49</td><td>31-32</td></tr></tbody></table><h3>পঞ্জাবি সাইজ</h3><table><thead><tr><th>সাইজ</th><th>চেস্ট (ইঞ্চি)</th><th>লেংথ (ইঞ্চি)</th></tr></thead><tbody><tr><td>38</td><td>38</td><td>42</td></tr><tr><td>40</td><td>40</td><td>43</td></tr><tr><td>42</td><td>42</td><td>44</td></tr><tr><td>44</td><td>44</td><td>45</td></tr></tbody></table>',
                'is_active' => true,
                'meta_title' => 'Size Guide - {BRAND}',
                'meta_description' => 'Complete size guide for men\'s clothing at {BRAND}',
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrInsert(
                ['slug' => $page['slug']],
                $page
            );
        }

        $this->command?->info('Seeded '.count($pages).' static pages.');
    }
}
