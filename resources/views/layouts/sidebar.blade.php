<!-- ...existing code... -->

{{-- إضافة قسم المساعدة لجميع أنواع المستخدمين --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('help.*') ? 'active' : '' }}" href="{{ route('help.index') }}">
        <i class="fas fa-question-circle"></i>
        <span>دليل المستخدم</span>
    </a>
</li>

<!-- ...existing code... -->