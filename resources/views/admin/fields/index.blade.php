@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 px-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">إدارة الملاعب</h4>
        <a href="{{ route('admin.fields.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus me-1"></i> إضافة ملعب
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 small mb-4">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">
        @foreach($fields as $field)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary text-primary rounded-circle p-2 me-3 text-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-futbol fa-lg mt-1"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $field->name }}</h6>
                                <span class="badge bg-soft-info text-info rounded-pill" style="font-size: 0.7rem;">
                                    {{ $field->prices->count() }} فترات تسعير
                                </span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.fields.edit', $field->id) }}">
                                        <i class="fas fa-edit me-2 text-warning"></i> تعديل البيانات
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الملعب وكل حجوزاته؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="fas fa-trash-alt me-2"></i> حذف الملعب
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-2">
                        <p class="text-muted mb-1 fw-bold" style="font-size: 0.75rem;">نظرة على الفترات:</p>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($field->prices->take(3) as $price)
                                <span class="badge bg-white text-dark border fw-normal" style="font-size: 0.65rem;">
                                    {{ $price->label }}: {{ $price->price }} ج
                                </span>
                            @empty
                                <span class="text-muted" style="font-size: 0.7rem;">لا توجد أسعار مضافة</span>
                            @endforelse
                            @if($field->prices->count() > 3)
                                <span class="text-muted small">+{{ $field->prices->count() - 3 }} المزيد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    /* ألوان خفيفة للتصميم */
    .bg-soft-primary { background-color: #e0e7ff; }
    .bg-soft-info { background-color: #e0f2fe; }
    .bg-soft-danger { background-color: #fee2e2; }
    
    .dropdown-item:active {
        background-color: #f8f9fa;
        color: inherit;
    }
</style>
@endsection