<head>
    <!-- Other head content -->
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
</head>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <div class="logo">💰</div>
            ExpenseTracker
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('personal.*') ? 'active' : '' }}"
                        href="{{ route('personal.index') }}">Personal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('groups.*') ? 'active' : '' }}"
                        href="{{ route('groups.index') }}">Groups</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('analytics.*') ? 'active' : '' }}"
                        href="{{ route('analytics.index') }}">Analytics</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
