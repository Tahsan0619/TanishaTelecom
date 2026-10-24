<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@gmail.com')],
            [
                'name' => 'Admin',
                'password' => env('ADMIN_PASSWORD', 'admin000'),
                'phone' => '01754471654',
            ]
        )->forceFill(['is_admin' => true])->save();

        User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer',
                'password' => 'customer000',
            ]
        );

        $categories = [];
        foreach ([
            ['Smartphones', 'smartphones', 'Latest 5G flagships, mid-range and budget smartphones with official warranty.', 'smartphones.jpg'],
            ['Laptops & Tablets', 'laptops-tablets', 'Ultrabooks, gaming laptops and tablets for work, study and play.', 'laptops.jpg'],
            ['Smartwatches', 'smartwatches', 'Smartwatches and fitness bands to track your health and stay connected.', 'smartwatches.jpg'],
            ['Audio', 'audio', 'True wireless earbuds, headphones and Bluetooth speakers.', 'audio.jpg'],
            ['Power & Charging', 'power-charging', 'Power banks, fast chargers and wireless chargers.', 'power.jpg'],
            ['Accessories', 'accessories', 'Cases, screen protectors, cables, mice and more.', 'accessories.jpg'],
        ] as $i => [$name, $slug, $description, $image]) {
            $categories[$slug] = Category::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $description,
                'image' => 'assets/images/categories/'.$image,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        foreach ($this->products() as $row) {
            [$name, $slug, $cat, $price, $sale, $variant, $image, $featured, $stock, $short, $specs] = $row;
            Product::updateOrCreate(['slug' => $slug], [
                'category_id' => $categories[$cat]->id,
                'name' => $name,
                'short_description' => $short,
                'description' => '<p>'.$short.'</p>'
                    .'<h3>Key specifications</h3><ul>'.implode('', array_map(fn ($s) => '<li>'.e($s).'</li>', $specs)).'</ul>'
                    .'<h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',
                'price' => $price,
                'sale_price' => $sale,
                'stock' => $stock,
                'unit' => $variant,
                'image' => 'assets/images/products/'.$image,
                'is_featured' => $featured,
                'is_active' => true,
                'free_shipping' => $row[11] ?? false,
            ]);
        }

        if (Slider::count() === 0) {
            $img = fn (string $file) => 'assets/images/products/'.$file;
            $now = now();
            Slider::insert([
                ['title' => 'Next-Gen Gadgets Are Here', 'subtitle' => 'Smartphones, earbuds & smartwatches: up to 15% off', 'image' => 'assets/images/slides/slide-1.jpg', 'link' => 'shop.html?category=smartphones', 'position' => 'main', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['title' => 'Work & Play Without Limits', 'subtitle' => 'Laptops, headphones and power banks for every need', 'image' => 'assets/images/slides/slide-2.jpg', 'link' => 'shop.html?category=laptops-tablets', 'position' => 'main', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['title' => 'Wireless Earbuds', 'subtitle' => null, 'image' => $img('earbuds.jpg'), 'link' => 'shop.html?category=audio', 'position' => 'side', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['title' => 'Smartwatches', 'subtitle' => null, 'image' => $img('watch-round.jpg'), 'link' => 'shop.html?category=smartwatches', 'position' => 'side', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['title' => 'Power Banks', 'subtitle' => null, 'image' => $img('power-bank.jpg'), 'link' => 'shop.html?category=power-charging', 'position' => 'side', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        foreach ($this->pages() as $slug => [$title, $content]) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'content' => $content, 'is_active' => true]);
        }
    }

    /** [name, slug, category, price, sale price, variant, image, featured, stock, short description, specs] */
    private function products(): array
    {
        return [
            ['X15 Pro 5G Smartphone - Titanium Grey', 'x15-pro-5g-smartphone', 'smartphones', 149999, 139999, '12GB / 256GB', 'phone-flagship.jpg', true, 12,
                'Flagship 5G smartphone with a 6.7" 120Hz AMOLED display, pro-grade triple camera and all-day battery.',
                ['6.7" LTPO AMOLED, 120Hz, 2500 nits', 'Octa-core 3nm flagship processor', '50MP + 48MP ultra-wide + 12MP 5x telephoto', '4,800mAh battery, 45W fast charging', 'Titanium frame, IP68 water resistance']],
            ['A55 5G Smartphone - Ocean Blue', 'a55-5g-smartphone', 'smartphones', 42999, 39499, '8GB / 128GB', 'phone-midrange.jpg', true, 25,
                'Powerful mid-range 5G phone with a smooth 120Hz display, 50MP OIS camera and 5,000mAh battery.',
                ['6.6" FHD+ Super AMOLED, 120Hz', 'Octa-core 5G processor', '50MP OIS main + 12MP ultra-wide camera', '5,000mAh battery, 25W fast charging', 'Expandable storage up to 1TB']],
            ['C12 Smartphone - Mint Green', 'c12-smartphone', 'smartphones', 14999, 13499, '4GB / 64GB', 'phone-budget.jpg', false, 40,
                'Budget-friendly smartphone with a big 6.6" display, dual camera and long-lasting 5,000mAh battery.',
                ['6.6" HD+ 90Hz display', 'Octa-core processor', '13MP dual rear camera, 5MP selfie', '5,000mAh battery', 'Dual SIM, side fingerprint sensor']],

            ['AirBook 14 Ultrabook - Core i5', 'airbook-14-ultrabook', 'laptops-tablets', 129000, 118500, '16GB RAM / 512GB SSD', 'laptop-ultrabook.jpg', true, 8,
                'Ultra-thin 1.2kg aluminium laptop with a 14" 2.8K display and up to 15 hours of battery life.',
                ['14" 2.8K IPS display, 100% sRGB', 'Intel Core i5 (13th Gen)', '16GB LPDDR5 RAM, 512GB NVMe SSD', 'Backlit keyboard, fingerprint reader', 'USB-C 65W charging, Wi-Fi 6E']],
            ['Raptor 16 Gaming Laptop - RTX 4060', 'raptor-16-gaming-laptop', 'laptops-tablets', 185000, 172000, '16GB RAM / 1TB SSD', 'laptop-gaming.jpg', true, 5,
                'High-performance gaming laptop with RTX 4060 graphics, 165Hz display and RGB keyboard.',
                ['16" QHD 165Hz display', 'Intel Core i7 (13th Gen)', 'NVIDIA GeForce RTX 4060 8GB', '16GB DDR5 RAM, 1TB NVMe SSD', 'Per-key RGB keyboard, advanced cooling']],
            ['Pad 11 Tablet with Stylus - Space Grey', 'pad-11-tablet', 'laptops-tablets', 38500, 34990, '8GB / 256GB', 'tablet.jpg', false, 15,
                '11" 2K tablet with stylus support, quad speakers and a big battery for study, work and entertainment.',
                ['11" 2K 120Hz display', 'Octa-core processor', 'Stylus pen included', 'Quad speakers with Dolby Atmos', '8,000mAh battery, 33W charging']],

            ['Active Pro AMOLED Smartwatch - Black', 'active-pro-amoled-smartwatch', 'smartwatches', 8990, 6990, '46mm, Black', 'watch-round.jpg', true, 30,
                'Premium round smartwatch with a 1.43" AMOLED display, GPS, heart-rate and SpO2 monitoring.',
                ['1.43" AMOLED always-on display', 'Built-in GPS, 150+ sports modes', 'Heart rate, SpO2, sleep & stress tracking', 'Bluetooth calling', 'Up to 12 days battery, 5ATM water resistance']],
            ['Fit Square Smartwatch with Calling - Navy', 'fit-square-smartwatch', 'smartwatches', 4500, 3490, '44mm, Silver / Navy', 'watch-square.jpg', false, 45,
                'Stylish square smartwatch with Bluetooth calling, health tracking and customisable watch faces.',
                ['1.96" HD display', 'Bluetooth calling with mic & speaker', 'Heart rate & SpO2 monitoring', '100+ sports modes', 'IP67 water resistance']],
            ['Band 8 Fitness Tracker', 'band-8-fitness-tracker', 'smartwatches', 3200, 2650, 'Black', 'fitness-band.jpg', false, 60,
                'Slim fitness band with AMOLED display that tracks steps, heart rate, sleep and workouts.',
                ['1.62" AMOLED display', '24/7 heart rate & SpO2', '150+ workout modes', 'Up to 16 days battery', '5ATM water resistance']],

            ['Buds Pro ANC True Wireless Earbuds', 'buds-pro-anc-earbuds', 'audio', 4990, 3790, 'Black', 'earbuds.jpg', true, 50,
                'Active noise cancelling earbuds with rich bass, clear calls and up to 36 hours of total playtime.',
                ['Hybrid ANC up to 45dB', '11mm dynamic drivers', '4-mic AI call noise reduction', '36 hours with charging case', 'Bluetooth 5.3, IPX5']],
            ['SoundMax ANC Wireless Headphones', 'soundmax-anc-headphones', 'audio', 9500, 7990, 'Silver White', 'headphones.jpg', true, 20,
                'Over-ear wireless headphones with adaptive noise cancelling, Hi-Res audio and 60-hour battery.',
                ['Adaptive active noise cancelling', '40mm Hi-Res drivers', 'Up to 60 hours battery', 'Multipoint Bluetooth 5.3', 'Foldable design with carry case']],
            ['Boom Mini Waterproof Bluetooth Speaker', 'boom-mini-bluetooth-speaker', 'audio', 3800, 2990, 'Navy Blue', 'speaker.jpg', false, 35,
                'Portable 20W speaker with deep bass, IPX7 waterproofing and 12 hours of playtime.',
                ['20W stereo sound with passive bass radiator', 'IPX7 waterproof', '12 hours playtime', 'TWS pairing for stereo', 'Built-in mic for calls']],

            ['20000mAh 22.5W Fast Charging Power Bank', '20000mah-power-bank', 'power-charging', 2800, 2290, '20000mAh, Black', 'power-bank.jpg', true, 70,
                'High-capacity power bank with digital display that charges your phone up to 4 times.',
                ['20,000mAh capacity', '22.5W fast charging (PD & QC)', 'USB-C + 2× USB-A outputs', 'Digital battery percentage display', 'Charge 3 devices at once']],
            ['65W GaN 3-Port Fast Charger', '65w-gan-fast-charger', 'power-charging', 3200, 2590, '65W, White', 'charger.jpg', false, 55,
                'Compact GaN charger that powers laptops, tablets and phones. USB-C cable included.',
                ['65W total output (GaN technology)', '2× USB-C + 1× USB-A', 'Charges laptops, tablets and phones', 'Over-heat & over-current protection', 'USB-C to USB-C cable included']],
            ['15W Magnetic Wireless Charging Pad', '15w-wireless-charger', 'power-charging', 1800, 1350, '15W, Black', 'wireless-charger.jpg', false, 40,
                'Slim wireless charger with magnetic alignment and LED ring for Qi-enabled phones and earbuds.',
                ['Up to 15W fast wireless charging', 'Magnetic alignment', 'Works through cases up to 5mm', 'Soft LED indicator ring', 'Foreign object detection']],

            ['Rugged Shockproof Case + Tempered Glass Combo', 'rugged-case-glass-combo', 'accessories', 850, 650, 'Black', 'phone-case.jpg', false, 120,
                'Military-grade drop protection case with raised camera guard, plus 9H tempered glass screen protector.',
                ['Shock-absorbing TPU + PC construction', 'Raised bezel for screen & camera', '9H hardness tempered glass included', 'Precise cut-outs for ports & buttons', 'Available for popular models']],
            ['100W Braided USB-C to USB-C Cable (2m)', '100w-braided-usb-c-cable', 'accessories', 990, 690, '2 metres', 'cable.jpg', false, 150,
                'Durable nylon braided cable supporting up to 100W fast charging and 480Mbps data transfer.',
                ['Up to 100W PD fast charging', 'Nylon braided, 30,000+ bend lifespan', 'Aluminium connector heads', '480Mbps data transfer', '2 metre length']],
            ['Wireless RGB Gaming Mouse', 'wireless-rgb-gaming-mouse', 'accessories', 2200, 1750, 'Black', 'mouse.jpg', false, 45,
                'Ergonomic wireless gaming mouse with adjustable DPI, RGB lighting and rechargeable battery.',
                ['2.4GHz wireless + USB-C wired mode', 'Adjustable DPI up to 12,000', '6 programmable buttons', 'RGB lighting', 'Rechargeable, up to 70 hours']],
            ['Silicone Mobile Back Cover - Navy Blue', 'silicone-mobile-cover-navy', 'accessories', 1, null, 'Navy Blue', 'mobile-cover.jpg', true, 500,
                'Soft-touch liquid silicone back cover with microfiber lining and raised camera protection.',
                ['Liquid silicone, soft-touch finish', 'Microfiber inner lining', 'Raised lip for camera & screen', 'Precise cut-outs for ports & buttons', 'Slim, lightweight design'], true],
        ];
    }

    private function pages(): array
    {
        return [
            'about-us' => ['About Us', <<<'HTML'
<h2>Welcome to Tanisha Telecom</h2>
<p><strong>Tanisha Telecom</strong> is your trusted destination for original smartphones, laptops, smartwatches, audio gear and mobile accessories in Bangladesh. We believe everyone deserves genuine technology at a fair price, backed by real warranty and friendly support.</p>
<hr>
<h3><i class="fa-solid fa-boxes-stacked"></i> What we offer</h3>
<ul class="icon-list">
<li><i class="fa-solid fa-mobile-screen-button"></i><span><strong>Smartphones:</strong> the latest flagship, mid-range and budget phones.</span></li>
<li><i class="fa-solid fa-laptop"></i><span><strong>Laptops &amp; Tablets:</strong> ultrabooks, gaming laptops and tablets for work and study.</span></li>
<li><i class="fa-solid fa-clock"></i><span><strong>Smartwatches:</strong> smartwatches and fitness bands to keep you healthy and connected.</span></li>
<li><i class="fa-solid fa-headphones"></i><span><strong>Audio:</strong> earbuds, headphones and Bluetooth speakers.</span></li>
<li><i class="fa-solid fa-battery-full"></i><span><strong>Power &amp; Charging:</strong> power banks, fast chargers and wireless chargers.</span></li>
<li><i class="fa-solid fa-plug"></i><span><strong>Accessories:</strong> cases, screen protectors, cables, mice and much more.</span></li>
</ul>
<hr>
<h3><i class="fa-solid fa-award"></i> Why shop with us?</h3>
<ul class="icon-list">
<li><i class="fa-solid fa-circle-check"></i><span>100% original products with official / brand warranty</span></li>
<li><i class="fa-solid fa-circle-check"></i><span>Competitive prices and regular offers</span></li>
<li><i class="fa-solid fa-circle-check"></i><span>Cash on Delivery and secure online payment via shurjoPay (bKash, Nagad, Rocket, Visa, Mastercard)</span></li>
<li><i class="fa-solid fa-circle-check"></i><span>Fast home delivery all over Bangladesh</span></li>
<li><i class="fa-solid fa-circle-check"></i><span>Expert advice before you buy and support after you buy</span></li>
</ul>
<hr>
<h3><i class="fa-solid fa-headset"></i> Contact us</h3>
<ul class="icon-list">
<li><i class="fa-solid fa-phone"></i><span>Phone: <a href="tel:01754471654">01754471654</a></span></li>
<li><i class="fa-brands fa-whatsapp"></i><span>WhatsApp: <a href="https://wa.me/8801754471654" target="_blank" rel="noopener">01754471654</a></span></li>
<li><i class="fa-solid fa-envelope"></i><span>Email: <a href="mailto:info@tanishatelecom.com">info@tanishatelecom.com</a></span></li>
</ul>
HTML],
            'refund-returns' => ['Refund and Returns Policy', <<<'HTML'
<h2>Refund &amp; Returns Policy</h2>
<p>At <strong>Tanisha Telecom</strong> we want you to be completely happy with your purchase. If something isn't right, here is how our return and refund process works.</p>
<h3><i class="fa-solid fa-circle-check"></i> Eligible for return / replacement</h3>
<ul class="icon-list">
<li><i class="fa-solid fa-circle-check"></i><span><strong>Wrong product delivered:</strong> the item, model or variant is different from your order.</span></li>
<li><i class="fa-solid fa-circle-check"></i><span><strong>Damaged or defective on arrival:</strong> physical damage or a manufacturing fault found at delivery.</span></li>
<li><i class="fa-solid fa-circle-check"></i><span><strong>Missing items:</strong> accessories or parts listed in the box are missing.</span></li>
</ul>
<p>Please report the issue within <strong>3 days of delivery</strong> with photos or a short video of the product and packaging.</p>
<h3><i class="fa-solid fa-circle-xmark"></i> Not eligible</h3>
<ul class="icon-list">
<li><i class="fa-solid fa-circle-xmark"></i><span>Products with physical damage, liquid damage or signs of misuse after delivery</span></li>
<li><i class="fa-solid fa-circle-xmark"></i><span>Products without the original box, accessories, invoice or with broken warranty seals</span></li>
<li><i class="fa-solid fa-circle-xmark"></i><span>Change of mind after the product has been unsealed or activated</span></li>
<li><i class="fa-solid fa-circle-xmark"></i><span>Software issues that can be fixed by an update or reset</span></li>
</ul>
<h3><i class="fa-solid fa-screwdriver-wrench"></i> Warranty claims</h3>
<p>After the return window, faults are handled under the brand / official warranty shown on the product page. Bring or send the product with its invoice and we will help you with the claim.</p>
<h3><i class="fa-solid fa-rotate-left"></i> Refund process</h3>
<ul>
<li>Once we receive and inspect the returned product, we will confirm your refund or replacement.</li>
<li>Approved refunds are processed within <strong>5 to 7 business days</strong>.</li>
<li>Online payments made through shurjoPay are refunded to the original payment method; Cash on Delivery orders are refunded via bKash, Nagad or bank transfer.</li>
<li>Delivery charges are non-refundable unless the return is due to our mistake.</li>
</ul>
<h3><i class="fa-solid fa-headset"></i> Contact us</h3>
<p>For any return or refund question, please contact our support team.</p>
<ul class="icon-list">
<li><i class="fa-solid fa-phone"></i><span>Phone: <a href="tel:01754471654">01754471654</a></span></li>
<li><i class="fa-brands fa-whatsapp"></i><span>WhatsApp: <a href="https://wa.me/8801754471654" target="_blank" rel="noopener">01754471654</a></span></li>
</ul>
HTML],
            'terms-conditions' => ['Terms & Conditions', <<<'HTML'
<h2>Terms &amp; Conditions</h2>
<p>By using the Tanisha Telecom website or placing an order, you agree to the following terms.</p>
<h3>1. Products &amp; pricing</h3>
<ul>
<li>All prices are in Bangladeshi Taka (৳) and include applicable VAT unless stated otherwise.</li>
<li>Product images are for illustration; colours and packaging may vary slightly.</li>
<li>Prices, offers and availability may change without prior notice.</li>
</ul>
<h3>2. Orders</h3>
<ul>
<li>Our team may call you to confirm your order before it is shipped.</li>
<li>We may cancel an order if a product is out of stock, the price was listed incorrectly, or the order details cannot be verified. Any payment made will be refunded in full.</li>
</ul>
<h3>3. Payment</h3>
<ul>
<li>We accept Cash on Delivery and online payment through <strong>shurjoPay</strong> (bKash, Nagad, Rocket, Visa, Mastercard and other supported methods).</li>
<li>Online payments are processed securely by shurjoPay; we never see or store your card or wallet PIN.</li>
</ul>
<h3>4. Delivery</h3>
<ul>
<li>Inside Dhaka: usually within 24 to 48 hours. Outside Dhaka: usually within 2 to 5 business days.</li>
<li>Please check the product in front of the delivery person before accepting it.</li>
<li>If delivery fails because of a wrong address or unreachable phone, a re-delivery charge may apply.</li>
</ul>
<h3>5. Warranty</h3>
<p>Warranty terms are set by the brand or as stated on the product page. Physical damage, liquid damage and unauthorised repairs void the warranty.</p>
<h3>6. Privacy</h3>
<p>Your name, phone number and address are used only to process and deliver your order and are never sold to third parties.</p>
<h3>7. Returns</h3>
<p>See our <a href="refund-returns.html">Refund &amp; Returns Policy</a> for details.</p>
<h3>8. Contact</h3>
<p>Questions? We are happy to help.</p>
<ul class="icon-list">
<li><i class="fa-solid fa-phone"></i><span>Phone: <a href="tel:01754471654">01754471654</a></span></li>
<li><i class="fa-brands fa-whatsapp"></i><span>WhatsApp: <a href="https://wa.me/8801754471654" target="_blank" rel="noopener">01754471654</a></span></li>
</ul>
HTML],
        ];
    }
}
