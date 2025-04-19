@extends('layouts.app')

@section('title', 'تفاصيل عرض السعر')

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
        <li class="breadcrumb-item active">عرض السعر #{{ $quote->id }}</li>
    </ol>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تفاصيل عرض السعر #{{ $quote->id }}</h5>
                    <span class="badge badge-{{ $quote->status_badge }}">{{ $quote->status_text }}</span>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">معلومات عرض السعر</h6>
                                <hr>
                                <p><strong>رقم الطلب:</strong> #{{ $quote->request_id }}</p>
                                <p><strong>السعر:</strong> {{ $quote->price }} {{ $quote->currency_code ?? 'SAR' }}</p>
                                <p><strong>العمولة:</strong> {{ $quote->commission_amount ?? 0 }} {{ $quote->currency_code ?? 'SAR' }}</p>
                                <p><strong>صالح حتى:</strong> {{ $quote->valid_until ? $quote->valid_until->format('Y-m-d') : 'غير محدد' }}</p>
                                <p><strong>الحالة:</strong> {{ $quote->status_text }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">معلومات مقدم العرض</h6>
                                <hr>
                                <p><strong>المقدم:</strong> {{ $quote->user ? $quote->user->name : 'غير محدد' }}</p>
                                <p><strong>الوكيل الفرعي:</strong> {{ $quote->subagent ? $quote->subagent->name : 'غير محدد' }}</p>
                                <p><strong>تاريخ الإنشاء:</strong> {{ $quote->created_at->format('Y-m-d H:i') }}</p>
                                <p><strong>آخر تحديث:</strong> {{ $quote->updated_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold">وصف عرض السعر</h6>
                        <hr>
                        <p>{{ $quote->description ?? 'لا يوجد وصف' }}</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold">التفاصيل</h6>
                        <hr>
                        <p>{{ $quote->details ?? 'لا توجد تفاصيل إضافية' }}</p>
                    </div>

                    @if(isset($quote->rejection_reason))
                    <div class="mb-4">
                        <h6 class="font-weight-bold">سبب الرفض</h6>
                        <hr>
                        <p>{{ $quote->rejection_reason }}</p>
                    </div>
                    @endif

                    @if($quote->attachments && $quote->attachments->count() > 0)
                    <div class="mb-4">
                        <h6 class="font-weight-bold">المرفقات</h6>
                        <hr>
                        <ul class="list-group">
                            @foreach($quote->attachments as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $attachment->name }}
                                <a href="{{ url('attachments/download/' . $attachment->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mt-4">
                        @if($quote->status === 'pending' && auth()->user()->id === $quote->request->user_id)
                            <div class="d-flex justify-content-between">
                                <form action="{{ route('quotes.accept', $quote->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">قبول العرض</button>
                                </form>
                                
                                <form action="{{ route('quotes.reject', $quote->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger">رفض العرض</button>
                                </form>
                            </div>
                        @endif

                        @if($quote->status === 'accepted')
                            <a href="{{ url('payments/create?quote_id=' . $quote->id) }}" class="btn btn-primary">
                                إتمام الدفع
                            </a>
                        @endif

                        <a href="{{ url('dashboard') }}" class="btn btn-secondary mt-2">
                            العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection