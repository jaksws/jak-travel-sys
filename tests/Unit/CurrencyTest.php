<?php

namespace Tests\Unit;

use App\Models\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_currency()
    {
        $currency = Currency::factory()->create([
            'name' => 'دينار كويتي',
            'code' => 'KWD',
            'symbol' => 'د.ك',
            'exchange_rate' => 0.82,
            'is_default' => false
        ]);

        $this->assertDatabaseHas('currencies', [
            'name' => 'دينار كويتي',
            'code' => 'KWD',
            'symbol' => 'د.ك',
            'exchange_rate' => 0.82
        ]);
    }

    #[Test]
    public function it_enforces_only_one_default_currency()
    {
        // أولاً ، قم بإنشاء عملة افتراضية
        $defaultCurrency = Currency::factory()->create([
            'code' => 'SAR',
            'is_default' => true
        ]);
        
        // ثم قم بإنشاء عملة افتراضية أخرى
        $newDefaultCurrency = Currency::factory()->create([
            'code' => 'USD',
            'is_default' => true
        ]);
        
        // أعد تحميل العملات من قاعدة البيانات
        $defaultCurrency->refresh();
        $newDefaultCurrency->refresh();
        
        // تأكد من أن العملة الأولى لم تعد هي الافتراضية
        $this->assertFalse($defaultCurrency->is_default);
        $this->assertTrue($newDefaultCurrency->is_default);
    }
    
    #[Test]
    public function it_formats_amount_with_correct_symbol_position()
    {
        $currencyBefore = Currency::factory()->create([
            'code' => 'USD',
            'symbol' => '$',
        ]);
        
        $currencyAfter = Currency::factory()->create([
            'code' => 'SAR',
            'symbol' => 'ر.س',
        ]);
        
        $amount = 1234.56;
        
        // اختبار تنسيق العملة مع وضع الرمز
        $usdFormatted = $currencyBefore->format($amount);
        $sarFormatted = $currencyAfter->format($amount);
        
        $this->assertStringContainsString('$', $usdFormatted);
        $this->assertStringContainsString('1,234.56', $usdFormatted);
        $this->assertEquals('$1,234.56', $usdFormatted);
        
        $this->assertStringContainsString('ر.س', $sarFormatted);
        $this->assertStringContainsString('1,234.56', $sarFormatted);
        $this->assertEquals('1,234.56 ر.س', $sarFormatted);
    }
    
    #[Test]
    public function it_converts_between_currencies()
    {
        $sar = Currency::factory()->create([
            'code' => 'SAR',
            'exchange_rate' => 1.0, // العملة الأساسية
        ]);
        
        $usd = Currency::factory()->create([
            'code' => 'USD',
            'exchange_rate' => 0.27, // 1 ريال = 0.27 دولار
        ]);
        
        $amount = 1000; // 1000 ريال
        
        // تحويل 1000 ريال إلى دولار
        $result = $sar->convertTo($amount, $usd);
        $this->assertEquals(270, $result); // 1000 ريال = 270 دولار
        
        // والعكس
        $result = $usd->convertTo(270, $sar);
        $this->assertEquals(1000, $result); // 270 دولار = 1000 ريال
    }
    
    #[Test]
    public function it_can_get_default_currency()
    {
        // إنشاء عملتين، واحدة منها افتراضية
        Currency::factory()->create([
            'code' => 'USD',
            'is_default' => false
        ]);
        
        $defaultCurrency = Currency::factory()->create([
            'code' => 'SAR',
            'is_default' => true
        ]);
        
        // اختبار الحصول على العملة الافتراضية
        $result = Currency::getDefault();
        
        $this->assertNotNull($result);
        $this->assertEquals($defaultCurrency->id, $result->id);
        $this->assertEquals('SAR', $result->code);
        $this->assertTrue($result->is_default);
    }
    
    #[Test]
    public function it_sets_default_when_no_default_exists()
    {
        // إنشاء عملة غير افتراضية
        $currency = Currency::factory()->create([
            'code' => 'SAR',
            'is_default' => false
        ]);
        
        // محاولة الحصول على العملة الافتراضية
        $result = Currency::getDefault();
        
        // يجب أن تكون هذه العملة هي الافتراضية الآن
        $this->assertEquals($currency->id, $result->id);
        $this->assertTrue($result->is_default);
        
        // التحقق أيضًا من قاعدة البيانات
        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'is_default' => 1
        ]);
    }

    #[Test]
    public function it_can_update_a_currency()
    {
        $currency = Currency::factory()->create([
            'name' => 'دينار كويتي',
            'code' => 'KWD',
            'symbol' => 'د.ك',
            'exchange_rate' => 0.82,
            'is_default' => false
        ]);

        $currency->update([
            'name' => 'دينار كويتي محدث',
            'exchange_rate' => 0.85
        ]);

        $this->assertDatabaseHas('currencies', [
            'name' => 'دينار كويتي محدث',
            'code' => 'KWD',
            'symbol' => 'د.ك',
            'exchange_rate' => 0.85
        ]);
    }
}
