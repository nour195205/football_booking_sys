@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 mt-5">
                <div class="card-header bg-dark text-white text-center fw-bold">تسجيل الدخول</div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">دخول</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>ليس لديك حساب؟ <a href="{{ route('register') }}">سجل الآن</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection