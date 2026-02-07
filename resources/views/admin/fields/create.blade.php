@extends('layouts.app')

@section('content')
<style>
    /* تحسين شكل المدخلات للموبايل */
    .form-control-lg {
        border-radius: 12px;
        font-size: 1rem;
        padding: 12px;
        border: 2px solid #eee;
    }
    .price-card {
        background: #fff;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e0e0e0;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .remove-row-btn {
        position: absolute;
        top: 10px;
        left: 10px; /* للاتجاه RTL */
        background: #fee2e2;
        color: #ef4444;
        border: none;
        border-radius: 8px;
        padding: 5px 12px;
        font-weight: bold;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    .sticky-bottom-actions {
        position: sticky;
        bottom: 0;
        background: #f8f9fa;
        padding: 15px;
        border-top: 1px solid #dee2e6;
        margin: 0 -15px -25px -15px;
        z-index: 100;
    }
</style>

<div class="container-fluid py-3 px-3 mb-5">
    <form action="{{ route('admin.fields.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="section-title"><i class="fas fa-futbol me-2 text-primary"></i>اسم الملعب الجديد</label>
            <input type="text" name="name" class="form-control form-control-lg shadow-sm" placeholder="مثلاً: ملعب النجوم" required>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <label class="section-title mb-0"><i class="fas fa-tags me-2 text-primary"></i>فترات التسعير</label>
            <button type="button" id="add-price-row" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> إضافة فترة
            </button>
        </div>

        <div id="prices-container">
            <div class="price-card shadow-sm border-0">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold">من (وقت)</label>
                        <input type="time" name="prices[0][from]" class="form-control form-control-lg" required>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold">إلى (وقت)</label>
                        <input type="time" name="prices[0][to]" class="form-control form-control-lg" required>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold">سعر الساعة (ج.م)</label>
                        <input type="number" name="prices[0][price]" class="form-control form-control-lg" required placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold">اسم الفترة (اختياري)</label>
                        <input type="text" name="prices[0][label]" class="form-control form-control-lg" placeholder="مثلاً: الفترة الصباحية">
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-bottom-actions d-flex gap-2">
            <button type="submit" class="btn btn-success btn-lg w-100 rounded-3 fw-bold shadow">حفظ الملعب</button>
            <a href="{{ route('admin.fields.index') }}" class="btn btn-outline-secondary btn-lg w-50 rounded-3 fw-bold text-center d-flex align-items-center justify-content-center">إلغاء</a>
        </div>
    </form>
</div>

<script>
    let rowIdx = 1;
    document.getElementById('add-price-row').addEventListener('click', function() {
        const container = document.getElementById('prices-container');
        const newCard = document.createElement('div');
        newCard.className = 'price-card shadow-sm border-0 animate__animated animate__fadeInUp';
        newCard.innerHTML = `
            <button type="button" class="remove-row-btn shadow-sm"><i class="fas fa-times me-1"></i> حذف</button>
            <div class="row g-3 mt-1">
                <div class="col-6">
                    <label class="small text-muted fw-bold">من (وقت)</label>
                    <input type="time" name="prices[${rowIdx}][from]" class="form-control form-control-lg" required>
                </div>
                <div class="col-6">
                    <label class="small text-muted fw-bold">إلى (وقت)</label>
                    <input type="time" name="prices[${rowIdx}][to]" class="form-control form-control-lg" required>
                </div>
                <div class="col-12">
                    <label class="small text-muted fw-bold">سعر الساعة (ج.م)</label>
                    <input type="number" name="prices[${rowIdx}][price]" class="form-control form-control-lg" required placeholder="0.00">
                </div>
                <div class="col-12">
                    <label class="small text-muted fw-bold">اسم الفترة (اختياري)</label>
                    <input type="text" name="prices[${rowIdx}][label]" class="form-control form-control-lg" placeholder="مثلاً: الفترة المسائية">
                </div>
            </div>
        `;
        container.prepend(newCard); // إضافة الفترة الجديدة في الأعلى لسهولة الوصول
        rowIdx++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row-btn') || e.target.closest('.remove-row-btn')) {
            const row = e.target.closest('.price-card');
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 200);
        }
    });
</script>
@endsection