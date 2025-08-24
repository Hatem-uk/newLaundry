<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Customer;
use App\Models\Laundry;
use App\Models\Order;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على العملاء والمغاسل والطلبات
        $customers = Customer::all();
        $laundries = Laundry::all();
        $orders = Order::where('target_type', 'service')->get();

        if ($customers->isEmpty() || $laundries->isEmpty()) {
            $this->command->info('No customers or laundries found. Please run CustomerSeeder and LaundrySeeder first.');
            return;
        }

        // إنشاء تقييمات متنوعة
        $this->createSampleRatings($customers, $laundries, $orders);

        $this->command->info('Ratings seeded successfully!');
    }

    /**
     * إنشاء تقييمات عينة متنوعة
     */
    private function createSampleRatings($customers, $laundries, $orders): void
    {
        $sampleRatings = [
            [
                'rating' => 5,
                'comment' => 'خدمة ممتازة وسريعة، الملابس نظيفة تماماً',
                'service_type' => 'washing'
            ],
            [
                'rating' => 4,
                'comment' => 'جودة جيدة، لكن التوصيل تأخر قليلاً',
                'service_type' => 'washing'
            ],
            [
                'rating' => 5,
                'comment' => 'كي ممتاز، الملابس تبدو جديدة',
                'service_type' => 'ironing'
            ],
            [
                'rating' => 3,
                'comment' => 'خدمة مقبولة، لكن السعر مرتفع',
                'service_type' => 'cleaning'
            ],
            [
                'rating' => 4,
                'comment' => 'تنظيف جيد، سأستخدمهم مرة أخرى',
                'service_type' => 'cleaning'
            ],
            [
                'rating' => 2,
                'comment' => 'الخدمة بطيئة والجودة متوسطة',
                'service_type' => 'washing'
            ],
            [
                'rating' => 5,
                'comment' => 'أفضل مغسلة في المنطقة، أنصح الجميع',
                'service_type' => 'washing'
            ],
            [
                'rating' => 4,
                'comment' => 'موظفين محترفين وخدمة سريعة',
                'service_type' => 'agent_supply'
            ],
            [
                'rating' => 3,
                'comment' => 'الخدمة جيدة لكن يمكن تحسينها',
                'service_type' => 'ironing'
            ],
            [
                'rating' => 5,
                'comment' => 'أسعار معقولة وجودة عالية',
                'service_type' => 'washing'
            ]
        ];

        foreach ($sampleRatings as $index => $ratingData) {
            $customer = $customers->random();
            $laundry = $laundries->random();
            
            // Only create rating if there are orders, otherwise skip
            if ($orders->isNotEmpty()) {
                $order = $orders->random();
                
                // التحقق من عدم وجود تقييم مكرر
                if (!Rating::hasCustomerRated($customer->id, $laundry->id, $order->id)) {
                    Rating::create([
                        'customer_id' => $customer->id,
                        'laundry_id' => $laundry->id,
                        'order_id' => $order->id,
                        'rating' => $ratingData['rating'],
                        'comment' => $ratingData['comment'],
                        'service_type' => $ratingData['service_type']
                    ]);
                }
            } else {
                // Create rating without order reference
                Rating::create([
                    'customer_id' => $customer->id,
                    'laundry_id' => $laundry->id,
                    'order_id' => null,
                    'rating' => $ratingData['rating'],
                    'comment' => $ratingData['comment'],
                    'service_type' => $ratingData['service_type']
                ]);
            }
        }
    }
}
