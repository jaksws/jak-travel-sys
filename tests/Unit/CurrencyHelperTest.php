<?php

namespace Tests\Unit;

use App\Helpers\CurrencyHelper;
use App\Models\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CurrencyHelperTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_convert_between_currencies()
    {
        // إنشاء عملات للاختبار
        $sar = Currency::factory()->create([
            'code' => 'SAR',
            'name' => 'ريال سعودي',
            'symbol' => '﷼',
            'exchange_rate' => 1.0, // العملة الأساسية
        ]);
        
        $usd = Currency::factory()->create([
            'code' => 'USD',
            'name' => 'دولار أمريكي',
            'symbol' => '$',
            'exchange_rate' => 0.27, // 1 ريال = 0.27 دولار
        ]);
        
        $egp = Currency::factory()->create([
            'code' => 'EGP',
            'name' => 'جنيه مصري',
            'symbol' => 'ج.م',
            'exchange_rate' => 8.3, // 1 ريال = 8.3 جنيه
        ]);
        
        // اختبار التحويل من ريال سعودي إلى دولار أمريكي
        $amountInSAR = 1000;
        $convertedToUSD = CurrencyHelper::convert($amountInSAR, $sar->id, $usd->id);
        $this->assertEquals(270, $convertedToUSD); // 1000 ريال = 270 دولار
        
        // اختبار التحويل من دولار أمريكي إلى جنيه مصري
        $amountInUSD = 100;
        $convertedToEGP = CurrencyHelper::convert($amountInUSD, $usd->id, $egp->id);
        $expectedEGP = 100 / 0.27 * 8.3; // تحويل الدولار إلى ريال ثم إلى جنيه
        $this->assertEquals($expectedEGP, $convertedToEGP);
    }
    
    #[Test]
    public function it_formats_currency_with_symbol()
    {
        $sar = Currency::factory()->create([
            'code' => 'SAR',
            'name' => 'ريال سعودي',
            'symbol' => '﷼',
        ]);
        
        $usd = Currency::factory()->create([
            'code' => 'USD',
            'name' => 'دولار أمريكي',
            'symbol' => '$',
        ]);

        // اختبار التنسيق مع الرمز في البداية (مثل الدولار)
        $formattedUSD = CurrencyHelper::format(1000, $usd->id);
        $this->assertEquals('$1,000.00', $formattedUSD);
        
        // اختبار التنسيق مع الرمز في النهاية (مثل الريال)
        $formattedSAR = CurrencyHelper::format(1000, $sar->id);
        $this->assertEquals('1,000.00﷼', $formattedSAR);
    }
    
    #[Test]
    public function it_gets_default_currency()
    {
        $sar = Currency::factory()->create([
            'code' => 'SAR',
            'is_default' => true,
        ]);
        
        Currency::factory()->create([
            'code' => 'USD',
            'is_default' => false,
        ]);

        $defaultCurrency = CurrencyHelper::getDefaultCurrency();
        
        $this->assertEquals($sar->id, $defaultCurrency->id);
        $this->assertEquals('SAR', $defaultCurrency->code);
    }

    #[Test]
    public function it_checks_currency_conversion()
    {
        $sar = Currency::factory()->create([
            'code' => 'SAR',
            'exchange_rate' => 1.0,
        ]);

        $usd = Currency::factory()->create([
            'code' => 'USD',
            'exchange_rate' => 0.27,
        ]);

        $amountInSAR = 1000;
        $convertedToUSD = CurrencyHelper::convertPrice($amountInSAR, 'SAR', 'USD');
        $this->assertEquals(270, $convertedToUSD);

        $amountInUSD = 100;
        $convertedToSAR = CurrencyHelper::convertPrice($amountInUSD, 'USD', 'SAR');
        $this->assertEquals(370.37, round($convertedToSAR, 2)); // P452f
    }

    #[Test]
    public function it_checks_currency_formatting()
    {
        $sar = Currency::factory()->create([
            'code' => 'SAR',
            'symbol' => '﷼',
        ]);

        $usd = Currency::factory()->create([
            'code' => 'USD',
            'symbol' => '$',
        ]);

        $formattedSAR = CurrencyHelper::formatPrice(1000, 'SAR');
        $this->assertEquals('﷼ 1,000.00', $formattedSAR); // P676d

        $formattedUSD = CurrencyHelper::formatPrice(1000, 'USD');
        $this->assertEquals('$1,000.00', $formattedUSD);
    }
}
