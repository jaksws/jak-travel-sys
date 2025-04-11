<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Agency;
use App\Models\Currency;
use App\Helpers\ServiceTypeHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $serviceTypes = [
            ServiceTypeHelper::HAJJ => [
                'names' => ['باقة الحج الاقتصادية', 'باقة الحج المميزة', 'باقة الحج VIP'],
                'descriptions' => ['باقة حج شاملة تتضمن السكن والتنقلات والإعاشة', 'تجربة حج متكاملة مع خدمات إضافية']
            ],
            ServiceTypeHelper::UMRAH => [
                'names' => ['عمرة شهر رمضان', 'عمرة الإجازة', 'العمرة السريعة'],
                'descriptions' => ['باقة كاملة للعمرة تشمل الإقامة والتنقلات', 'خدمة عمرة ميسرة للجميع']
            ],
            ServiceTypeHelper::VISA => [
                'names' => ['تأشيرة زيارة', 'تأشيرة عمل', 'تأشيرة سياحة'],
                'descriptions' => ['خدمة استخراج التأشيرات بسرعة وسهولة', 'تسهيل إجراءات التأشيرات لجميع الدول']
            ],
            ServiceTypeHelper::FLIGHT_TICKET => [
                'names' => ['تذاكر طيران اقتصادية', 'حجز رحلات دولية', 'تذاكر درجة رجال الأعمال'],
                'descriptions' => ['خدمة حجز تذاكر الطيران بأفضل الأسعار', 'رحلات مريحة إلى جميع الوجهات']
            ],
            ServiceTypeHelper::TRANSPORT => [
                'names' => ['نقل من وإلى المطار', 'خدمة النقل الجماعي', 'سيارات فاخرة للتنقل'],
                'descriptions' => ['خدمة نقل مريحة وآمنة', 'سيارات حديثة مع سائقين محترفين']
            ]
        ];

        $type = $this->faker->randomElement(array_keys($serviceTypes));
        $typeInfo = $serviceTypes[$type];
        
        return [
            'agency_id' => Agency::factory(),
            'name' => $this->faker->randomElement($typeInfo['names']),
            'type' => $type,
            'description' => $this->faker->randomElement($typeInfo['descriptions']),
            'price' => $this->faker->numberBetween(1000, 10000),
            'currency_id' => Currency::factory(),
            'image' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create an active service.
     *
     * @return $this
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Configure the factory to create a specific service type.
     *
     * @param string $type
     * @return $this
     */
    public function ofType($type)
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type,
            ];
        });
    }
}