@extends('layouts.app')

@section('title', 'إدارة الطلبات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الطلبات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة الطلبات</h1>
        <a href="{{ route('admin.requests.store') }}" class="btn btn-primary d-none">إضافة طلب جديد</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
            <span><i class="fas fa-clipboard-list me-2"></i> قائمة الطلبات</span>
            <form method="GET" class="d-flex gap-2 align-items-center" action="">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث بالعنوان..." value="{{ request('search') }}">
                <select name="status" class="form-select form-select-sm">
                    <option value="">كل الحالات</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>قيد الانتظار</option>
                    <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>مكتمل</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>ملغي</option>
                </select>
                <button class="btn btn-sm btn-light" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>العميل</th>
                            <th>الخدمة</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->title ?? '-' }}</td>
                            <td>{{ $request->user->name ?? 'غير محدد' }}</td>
                            <td>{{ $request->service->name ?? 'غير محدد' }}</td>
                            <td>
                                @switch($request->status)
                                    @case('pending')
                                        <span class="badge bg-secondary">قيد الانتظار</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-info">قيد التنفيذ</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">مكتمل</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">ملغي</span>
                                        @break
                                    @default
                                        <span class="badge bg-light">{{ $request->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ $request->created_at ? $request->created_at->format('Y-m-d') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">لا توجد طلبات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $requests->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
