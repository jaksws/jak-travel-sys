<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    /**
     * عرض صفحة إدارة العملات
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('agency.settings.currencies', compact('currencies'));
    }

    /**
     * إضافة عملة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:50',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        Currency::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'status' => 'active',
            'is_default' => false,
        ]);

        return redirect()->route('agency.settings.currencies')
                        ->with('success', 'تمت إضافة العملة بنجاح');
    }

    /**
     * تحديث بيانات عملة
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        $currency->update([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
        ]);

        return redirect()->route('agency.settings.currencies')
                        ->with('success', 'تم تحديث بيانات العملة بنجاح');
    }

    /**
     * تغيير حالة تفعيل/تعطيل العملة
     */
    public function toggleStatus(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()->route('agency.settings.currencies')
                            ->with('error', 'لا يمكن تعطيل العملة الافتراضية');
        }

        $newStatus = $currency->isActive() ? 'inactive' : 'active';
        $currency->update([
            'status' => $newStatus
        ]);

        $statusMessage = $newStatus === 'active' ? 'تفعيل' : 'تعطيل';
        return redirect()->route('agency.settings.currencies')
                        ->with('success', "تم $statusMessage العملة بنجاح");
    }

    /**
     * تعيين العملة كافتراضية
     */
    public function setAsDefault(Currency $currency)
    {
        // التأكد من أن العملة مفعلة
        if (!$currency->isActive()) {
            $currency->update(['status' => 'active']);
        }

        // حفظ سعر الصرف الأصلي للعملة التي ستصبح افتراضية
        $originalExchangeRate = $currency->exchange_rate;

        // إلغاء تعيين العملة الافتراضية السابقة
        Currency::where('is_default', true)->update(['is_default' => false]);

        // تعيين العملة الجديدة كافتراضية وتحديث سعر صرفها إلى 1
        $currency->update(['is_default' => true, 'exchange_rate' => 1.0000]);

        // تحديث أسعار الصرف للعملات الأخرى نسبة للعملة الجديدة
        // التأكد من أن سعر الصرف الأصلي ليس صفراً لتجنب القسمة على صفر
        if ($originalExchangeRate != 0) {
            $this->recalculateExchangeRates($currency, $originalExchangeRate);
        } else {
            \Log::error("Attempted to set currency ID {$currency->id} as default with zero original exchange rate.");
        }

        return redirect()->route('agency.settings.currencies')
                        ->with('success', "تم تعيين {$currency->name} كعملة افتراضية بنجاح وتم تحديث أسعار الصرف الأخرى.");
    }

    /**
     * إعادة حساب أسعار الصرف للعملات عند تغيير العملة الافتراضية
     *
     * @param Currency $defaultCurrency العملة الافتراضية الجديدة
     * @param float $originalDefaultRate سعر الصرف الأصلي للعملة الافتراضية الجديدة (قبل أن يصبح 1)
     */
    private function recalculateExchangeRates(Currency $defaultCurrency, float $originalDefaultRate)
    {
        // جلب جميع العملات الأخرى (غير الافتراضية)
        $otherCurrencies = Currency::where('id', '!=', $defaultCurrency->id)->get();

        foreach ($otherCurrencies as $otherCurrency) {
            // حساب سعر الصرف الجديد نسبةً للعملة الافتراضية الجديدة
            // new_rate = old_rate / original_rate_of_new_default
            $newExchangeRate = $otherCurrency->exchange_rate / $originalDefaultRate;

            // تحديث سعر صرف العملة
            $otherCurrency->update(['exchange_rate' => $newExchangeRate]);
        }
    }

    /**
     * حذف عملة
     */
    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()->route('agency.settings.currencies')
                            ->with('error', 'لا يمكن حذف العملة الافتراضية');
        }

        // التحقق من عدم استخدام العملة في أي خدمات
        if ($currency->services()->exists()) {
            return redirect()->route('agency.settings.currencies')
                            ->with('error', 'لا يمكن حذف العملة لأنها مستخدمة في بعض الخدمات.');
        }

        // التحقق من عدم استخدام العملة في أي عروض أسعار
        if ($currency->quotes()->exists()) {
            return redirect()->route('agency.settings.currencies')
                            ->with('error', 'لا يمكن حذف العملة لأنها مستخدمة في بعض عروض الأسعار.');
        }

        // يمكنك إضافة المزيد من عمليات التحقق هنا إذا كانت العملة مرتبطة بنماذج أخرى

        $currency->delete();

        return redirect()->route('agency.settings.currencies')
                        ->with('success', 'تم حذف العملة بنجاح');
    }
}
