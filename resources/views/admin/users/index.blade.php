@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    
    /* تصميم كروت المستخدمين */
    .user-card {
        background: #fff;
        border-radius: 20px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #edf2f7;
        position: relative;
    }
    .user-avatar {
        width: 50px;
        height: 50px;
        background-color: #f0f4f8;
        color: #4a5568;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .role-badge {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 10px;
        font-weight: 700;
    }
    
    /* ستايل الأكشنز السفلي في الكارت */
    .user-actions-footer {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 12px;
        margin-top: 15px;
    }

    /* تحسين المودال للموبايل */
    @media (max-width: 576px) {
        .modal.fade .modal-dialog {
            transform: translate(0, 100%);
            transition: transform 0.3s ease-out;
            margin: 0;
            max-width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .modal.show .modal-dialog { transform: translate(0, 0); }
        .modal-content { border-radius: 25px 25px 0 0 !important; border: none; }
    }
</style>

<div class="container-fluid py-3 px-3">
    <div class="d-flex justify-content-between align-items-center mb-4 px-1">
        <h4 class="fw-bold mb-0 text-dark">إدارة المستخدمين</h4>
        <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> إضافة
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 small mb-4">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 small mb-4">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div id="users_list">
        @foreach($users as $user)
        <div class="user-card shadow-sm border-0">
            <div class="d-flex align-items-center mb-2">
                <div class="user-avatar me-3 shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark">{{ $user->name }}</h6>
                    <small class="text-muted d-block mb-1">{{ $user->email }}</small>
                    <span class="badge role-badge {{ $user->role == 'admin' ? 'bg-danger text-white' : 'bg-info text-dark' }}">
                        {{ strtoupper($user->role) }}
                    </span>
                </div>
            </div>

            <div class="user-actions-footer">
                <div class="row align-items-end g-2">
                    <div class="col-8 border-end">
                        <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label class="small fw-bold text-muted mb-1 d-block">تعديل الصلاحية:</label>
                            <div class="d-flex gap-2">
                                <select name="role" class="form-select form-select-sm border-0 shadow-sm rounded-2">
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm rounded-2 shadow-sm">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-4 text-center">
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm border-0 fw-bold">
                                    <i class="fas fa-trash-alt d-block mb-1"></i> حذف
                                </button>
                            </form>
                        @else
                            <small class="text-muted fw-bold">حسابك</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header border-0 py-3 bg-light">
                <h6 class="modal-title fw-bold text-primary"><i class="fas fa-user-plus me-2"></i>إضافة عضو جديد</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold mb-1 text-muted">الاسم الكامل</label>
                    <input type="text" name="name" class="form-control form-control-lg border-2" placeholder="ادخل الاسم" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold mb-1 text-muted">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control form-control-lg border-2" placeholder="name@company.com" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold mb-1 text-muted">كلمة المرور</label>
                    <input type="password" name="password" class="form-control form-control-lg border-2" placeholder="********" required>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold mb-1 text-muted">تحديد الصلاحية</label>
                    <select name="role" class="form-select form-select-lg border-2" required>
                        <option value="user">User (موظف)</option>
                        <option value="admin">Admin (مدير نظام)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 pb-4 bg-light">
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow">تأكيد الحفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection