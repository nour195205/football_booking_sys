@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    
    /* كروت الملخص المالي */
    .report-card {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .report-card:active { transform: scale(0.98); }
    
    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    /* تصميم قائمة الحجوزات ككروت للموبايل */
    .booking-item {
        background: #fff;
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 12px;
        border: 1px solid #edf2f7;
    }
    .field-badge {
        background: #e2e8f0;
        color: #4a5568;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: bold;
    }
    .amount-label { font-size: 0.8rem; color: #718096; }
    .amount-value { font-weight: 800; font-size: 1rem; }

    /* تحسين فورم اختيار التاريخ للموبايل */
    .date-filter {
        background: #fff;
        border-radius: 15px;
        padding: 10px 15px;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid py-3 px-3">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">التقرير المالي</h4>
        <p class="text-muted small">ليوم: <span class="text-primary fw-bold">{{ $date }}</span></p>
        
        <form action="{{ route('admin.reports.daily') }}" method="GET" class="date-filter shadow-sm d-flex align-items-center">
            <i class="fas fa-calendar-alt text-primary me-2"></i>
            <input type="date" name="date" class="form-control border-0 shadow-none p-0" value="{{ $date }}" onchange="this.form.submit()">
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card report-card shadow-sm bg-white h-100">
                <div class="card-body p-3">
                    <div class="icon-box bg-success text-white shadow-sm">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="amount-label text-success fw-bold">الخزنة (عربون)</div>
                    <div class="amount-value text-dark">{{ number_format($totalDeposit, 0) }} <small>ج</small></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card report-card shadow-sm bg-white h-100">
                <div class="card-body p-3">
                    <div class="icon-box bg-warning text-dark shadow-sm">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="amount-label text-warning fw-bold">المتبقي بره</div>
                    <div class="amount-value text-dark">{{ number_format($totalRemaining, 0) }} <small>ج</small></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card report-card shadow-sm bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="mb-0 fw-bold">إجمالي حجوزات اليوم</h6>
                        <small class="opacity-75">حسب التاريخ المختار</small>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $bookingsCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 d-flex justify-content-between align-items-center px-1">
        <label class="fw-bold text-dark">تفاصيل الحجوزات:</label>
        <span class="badge bg-light text-dark border">{{ $details->count() }} حجز</span>
    </div>

    <div id="booking_list">
        @forelse($details as $booking)
        <div class="booking-item shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="field-badge mb-2 d-inline-block">{{ $booking->field->name }}</span>
                    <h6 class="fw-bold mb-0 text-dark">{{ $booking->user_name }}</h6>
                </div>
                <div class="text-end">
                    <div class="badge bg-soft-primary text-primary rounded-pill small">
                        <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                    </div>
                </div>
            </div>
            
            <div class="row g-0 border-top pt-2">
                <div class="col-6 border-end">
                    <div class="amount-label">العربون</div>
                    <div class="amount-value text-success">+{{ number_format($booking->deposit, 0) }} ج</div>
                </div>
                <div class="col-6 ps-3">
                    <div class="amount-label">المتبقي</div>
                    <div class="amount-value text-danger">{{ number_format($booking->remaining, 0) }} ج</div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-20"></i>
            <p class="text-muted">لا توجد حجوزات مسجلة لهذا اليوم</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    .bg-soft-primary { background-color: #ebf4ff; }
</style>
@endsection