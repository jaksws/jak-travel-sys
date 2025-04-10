@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5>نتائج البحث: {{ $query }}</h5>
                    <a href="{{ route('help.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-right"></i> العودة للدليل
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('help.search') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="بحث في الدليل..." value="{{ $query }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </form>

                    @if(count($results) > 0)
                        <div class="list-group">
                            @foreach($results as $result)
                                <a href="{{ $result['url'] }}" class="list-group-item list-group-item-action">
                                    <h5 class="mb-1">{{ $result['title'] }}</h5>
                                    <p class="mb-1">{{ $result['snippet'] }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> لا توجد نتائج للبحث عن "{{ $query }}"
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
