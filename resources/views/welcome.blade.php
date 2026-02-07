@extends('layouts.app')

@section('content')
<style>
    .welcome-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 20px;
    }
    .hero-icon {
        font-size: 5rem;
        color: #198754;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }
    .choice-card {
        width: 100%;
        max-width: 350px;
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .choice-card:active {
        transform: scale(0.95);
    }
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
        40% {transform: translateY(-20px);}
        60% {transform: translateY(-10px);}
    }
</style>

<div class="welcome-container">
    <div class="hero-icon">
        <i class="fas fa-futbol"></i>
    </div>
    
    <h2 class="fw-bold mb-2">أهلاً بك في كابتن حجز</h2>
    <p class="text-muted mb-5 px-3">من فضلك اختر كيف تود المتابعة اليوم؟</p>

    <a href="{{ route('booking') }}" class="choice-card shadow-sm border-0">
        <div class="icon-circle bg-primary text-white">
            <i class="fas fa-calendar-check"></i>
        </div>
        <h5 class="fw-bold text-dark">أنا لاعب / مشجع</h5>
        <p class="small text-muted mb-0">الدخول لصفحة حجز المواعيد ومعاينة الملاعب المتاحة</p>
    </a>

    <a href="{{ route('login') }}" class="choice-card shadow-sm border-0">
        <div class="icon-circle bg-dark text-white">
            <i class="fas fa-user-shield"></i>
        </div>
        <h5 class="fw-bold text-dark">لوحة الإدارة</h5>
        <p class="small text-muted mb-0">تسجيل الدخول للموظفين والآدمن لإدارة الحجوزات والتقارير</p>
    </a>

    <div class="mt-4">
        <small class="text-muted">© {{ date('Y') }} نظام كابتن حجز المتكامل</small>
    </div>
</div>
@endsection