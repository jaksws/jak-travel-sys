<!-- ...existing code... -->

@auth
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="helpDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-question-circle"></i> المساعدة
        </a>
        <ul class="dropdown-menu" aria-labelledby="helpDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('help.index') }}">
                    <i class="fas fa-book"></i> دليل المستخدم
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('onboarding.tips') }}">
                    <i class="fas fa-lightbulb"></i> نصائح سريعة
                </a>
            </li>
        </ul>
    </li>
@endauth

<!-- ...existing code... -->