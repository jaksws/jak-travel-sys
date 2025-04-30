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
        <ul class="nav justify-content-center mt-3">
            @foreach(config('ui.footer.social') as $social)
                <li class="nav-item">
                    <a class="nav-link" href="{{ $social['url'] }}" target="_blank">
                        <i class="fab fa-{{ $social['icon'] ?? 'globe' }}"></i> {{ $social['name'] }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-3">
            <p class="mb-1"><i class="fas fa-phone"></i> {{ config('ui.footer.contact.phone', 'غير متوفر') }}</p>
            <p class="mb-1"><i class="fas fa-envelope"></i> {{ config('ui.footer.contact.email', 'غير متوفر') }}</p>
            <p class="mb-0"><i class="fas fa-map-marker-alt"></i> {{ config('ui.footer.contact.address', 'غير متوفر') }}</p>
        </div>
    </div>
</footer>
