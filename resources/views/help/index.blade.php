@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>الدليل</h5>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        <form action="{{ route('help.search') }}" method="GET">
                            <div class="input-group mb-0">
                                <input type="text" name="q" class="form-control" placeholder="بحث في الدليل...">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="list-group list-group-flush" id="help-sections">
                        <!-- سيتم تعبئتها بواسطة JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5>{{ $title }}</h5>
                    <button class="btn btn-sm btn-light" onclick="window.print()">
                        <i class="fas fa-print"></i> طباعة
                    </button>
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

<script>
// استخراج عناوين الأقسام من محتوى المساعدة
document.addEventListener('DOMContentLoaded', function() {
    const helpContent = document.querySelector('.help-content');
    const headings = helpContent.querySelectorAll('h2');
    const sectionsList = document.getElementById('help-sections');
    
    headings.forEach(function(heading) {
        const sectionTitle = heading.textContent;
        const sectionId = heading.id || sectionTitle.toLowerCase().replace(/\s+/g, '-');
        heading.id = sectionId;
        
        const listItem = document.createElement('a');
        listItem.href = '#' + sectionId;
        listItem.className = 'list-group-item list-group-item-action';
        listItem.textContent = sectionTitle;
        
        listItem.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById(sectionId).scrollIntoView({
                behavior: 'smooth'
            });
        });
        
        sectionsList.appendChild(listItem);
    });
});
</script>

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
    .col-md-3, .card-header button {
        display: none !important;
    }
    .col-md-9 {
        width: 100% !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
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
