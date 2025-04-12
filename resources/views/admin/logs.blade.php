@extends('layouts.app')

@section('title', 'سجلات النظام')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">سجلات النظام</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">سجلات النظام</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-header">ملفات السجلات</div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($logFiles as $logFile)
                                    <a href="{{ route('admin.system.logs', ['log' => basename($logFile)]) }}" 
                                       class="list-group-item list-group-item-action {{ $selectedLog == basename($logFile) ? 'active' : '' }}">
                                        {{ basename($logFile) }}
                                    </a>
                                @empty
                                    <div class="list-group-item">لا توجد ملفات سجلات</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            @if($selectedLog)
                                محتوى ملف: {{ $selectedLog }}
                            @else
                                محتوى السجل
                            @endif
                        </div>
                        <div class="card-body">
                            @if($logContent)
                                <pre class="bg-light p-3" style="max-height: 500px; overflow-y: auto; direction: ltr; text-align: left;">{{ $logContent }}</pre>
                            @else
                                <div class="alert alert-info">
                                    الرجاء اختيار ملف سجل من القائمة على اليمين لعرض محتواه.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection