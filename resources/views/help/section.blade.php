@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5>{{ $title }}</h5>
                    <div>
                        <a href="{{ route('help.index') }}" class="btn btn-sm btn-light me-2">
                            <i class="fas fa-arrow-right"></i> العودة للدليل
                        </a>
                        <button class="btn btn-sm btn-light" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="help-content">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.help-content h2 {
    margin-top: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}
.help-content h3 {
    margin-top: 1.2rem;
    color: #333;
}
.help-content ul {
    padding-right: 1.5rem;
}
@media print {
    .card-header button, .card-header a {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .card-header {
        background-color: #333 !important;
        color: #000 !important;
        border: none !important;
    }
}
</style>
@endsection
