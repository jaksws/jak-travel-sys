<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ config('app.name', 'تطبيق وكالات السفر') }}</span>
        <ul class="nav justify-content-center">
            @foreach(config('ui.footer.links') as $link)
                <li class="nav-item">
                    <a class="nav-link" href="{{ $link['url'] }}">{{ $link['text'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</footer>
