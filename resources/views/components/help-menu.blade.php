<div class="help-menu">
    <div class="dropdown">
        <button class="btn btn-link text-decoration-none dropdown-toggle" id="helpMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-question-circle"></i>
            <span>المساعدة</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="helpMenuDropdown">
            <li>
                <h6 class="dropdown-header">دليل المستخدم</h6>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('help.index') }}">
                    <i class="fas fa-book fa-fw me-2"></i>
                    دليل المستخدم الكامل
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('onboarding.tips') }}">
                    <i class="fas fa-lightbulb fa-fw me-2"></i>
                    نصائح سريعة
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <h6 class="dropdown-header">مساعدة حسب الدور</h6>
            </li>
            @if(auth()->check())
                @if(auth()->user()->is_admin)
                    <li>
                        <a class="dropdown-item" href="{{ route('help.index') }}?section=admin-guide">
                            <i class="fas fa-users-cog fa-fw me-2"></i>
                            دليل الأدمن
                        </a>
                    </li>
                @elseif(auth()->user()->role === 'agency')
                    <li>
                        <a class="dropdown-item" href="{{ route('help.index') }}?section=agency-guide">
                            <i class="fas fa-building fa-fw me-2"></i>
                            دليل الوكالة
                        </a>
                    </li>
                @elseif(auth()->user()->role === 'subagent')
                    <li>
                        <a class="dropdown-item" href="{{ route('help.index') }}?section=subagent-guide">
                            <i class="fas fa-user-tie fa-fw me-2"></i>
                            دليل السبوكيل
                        </a>
                    </li>
                @elseif(auth()->user()->role === 'customer')
                    <li>
                        <a class="dropdown-item" href="{{ route('help.index') }}?section=customer-guide">
                            <i class="fas fa-user fa-fw me-2"></i>
                            دليل العميل
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </div>
</div>
