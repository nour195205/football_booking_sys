<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>santiago - نظام الملاعب</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .offcanvas {
            width: 280px !important;
            background-color: #1a1d20;
            color: white;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.3s;
            border-radius: 12px;
            margin: 5px 15px;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .sidebar-link.active {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .top-navbar {
            background: #fff;
            padding: 10px 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .menu-toggle-btn {
            border: none;
            background: #f0f2f5;
            padding: 8px 12px;
            border-radius: 10px;
        }

    </style>
    @yield('styles')
</head>
<body>

    <header class="top-navbar sticky-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                @auth
                <button class="menu-toggle-btn me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                    <i class="fas fa-bars"></i>
                </button>
                @endauth

                <a href="/" class="btn btn-light btn-sm rounded-circle shadow-sm me-2" title="الرئيسية">
                    <i class="fas fa-home text-primary"></i>
                </a>
            </div>

            <a class="navbar-brand fw-bold text-dark mb-0 fs-6" href="/">
                <i class="fas fa-futbol text-success me-1"></i> كابتن حجز
            </a>

            <div class="d-flex align-items-center">
                <button onclick="manualRefresh()" class="btn btn-light btn-sm rounded-circle shadow-sm me-3" title="تحديث البيانات">
                    <i class="fas fa-sync-alt text-success" id="refresh-icon"></i>
                </button>

                @guest
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill px-3">دخول</a>
                @else
                <i class="fas fa-user-circle fa-lg text-primary"></i>
                @endguest
            </div>
        </div>
    </header>

    @auth
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold text-white">القائمة الرئيسية</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 pt-3">
            <nav class="nav flex-column">

                <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-th-large me-2"></i> لوحة التحكم
                </a>

                <a class="sidebar-link {{ request()->routeIs('admin.fields.*') ? 'active' : '' }}" href="{{ route('admin.fields.index') }}">
                    <i class="fas fa-map-marker-alt me-2"></i> إدارة الملاعب
                </a>

                <a class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.daily') }}">
                    <i class="fas fa-file-invoice-dollar me-2"></i> التقارير المالية
                </a>

                @if(auth()->user()->role == 'admin')
                <div class="border-top border-secondary my-2 mx-4 opacity-25"></div>
                <a class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog me-2"></i> إدارة المستخدمين
                </a>
                @endif

                <div class="border-top border-secondary my-3 mx-4 opacity-25"></div>

                <form action="{{ route('logout') }}" method="POST" class="px-3">
                    @csrf
                    <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                    </button>
                </form>
            </nav>
        </div>
    </div>
    @endauth

    <main class="py-3">
        @if(session('success'))
        <div class="container-fluid px-3">
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 small">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function manualRefresh() {
            // 1. تدوير الأيقونة عشان اليوزر يحس إن فيه أكشن بيحصل
            const icon = document.getElementById('refresh-icon');
            icon.classList.add('fa-spin');

            // 2. التحقق من وجود دالة loadSlots (لو إحنا في صفحة الملاعب أو الداشبورد)
            if (typeof loadSlots === 'function') {
                loadSlots();
                console.log("تم التحديث اليدوي للمواعيد.");
            } else {
                // لو في صفحة تانية معندهاش أجاكس، يعمل ريفريش كامل للصفحة
                location.reload();
            }

            // 3. وقف التدوير بعد ثانية واحدة
            setTimeout(() => {
                icon.classList.remove('fa-spin');
            }, 1000);
        }

    </script>
    @stack('scripts')
</body>
</html>
