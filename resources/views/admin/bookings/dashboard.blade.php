@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8f9fa; font-family: 'Cairo', sans-serif; }
    
    /* منع التمرير الأفقي للصفحة بالكامل */
    .container-fluid { overflow-x: hidden; padding-left: 10px; padding-right: 10px; }

    /* تصميم كروت الملاعب بشكل أفقي للموبايل */
    .fields-scroller {
        display: flex;
        overflow-x: auto;
        gap: 12px;
        padding: 10px 5px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }
    .field-card {
        min-width: 85%; /* يظهر كارت واحد وجزء من الثاني لتحفيز السحب */
        scroll-snap-align: start;
        border-radius: 15px !important;
        transition: 0.3s;
        border: 2px solid transparent !important;
    }
    .selected-field {
        border-color: #0d6efd !important;
        background-color: #f0f7ff !important;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15) !important;
    }

    /* تحسين قائمة الأسعار داخل الكارت */
    .price-list { font-size: 0.85rem; max-height: 100px; overflow-y: auto; }

    /* تحسين شبكة المربعات (Slots) للموبايل */
    .slots-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* مربعين في كل صف للموبايل */
        gap: 10px;
    }
    
    /* جعل المودال يظهر من الأسفل في الموبايل */
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
        .modal-content { border-radius: 20px 20px 0 0 !important; }
    }
</style>

<div class="container-fluid py-3">
    <div class="row mb-3 align-items-center">
        <div class="col-7">
            <h4 class="fw-bold mb-0">لوحة التحكم</h4>
        </div>
        <div class="col-5">
            <input type="date" id="booking_date" class="form-control form-control-sm rounded-pill shadow-sm" value="{{ date('Y-m-d') }}" onchange="loadSlots()">
        </div>
    </div>

    <div class="mb-2">
        <label class="fw-bold small text-muted mb-2">1. اختر الملعب:</label>
        <div class="fields-scroller">
            @foreach($fields as $field)
            <div class="card shadow-sm field-card" id="card_{{ $field->id }}" onclick="selectField({{ $field->id }})">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-primary mb-0">{{ $field->name }}</h6>
                        <i class="fas fa-check-circle select-icon text-success d-none"></i>
                    </div>
                    <div class="price-list bg-light rounded p-2">
                        @forelse($field->prices as $price)
                            <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                <span>{{ $price->label ?? 'فترة' }}</span>
                                <span class="fw-bold">{{ $price->price }} ج</span>
                            </div>
                        @empty
                            <div class="text-center small text-muted">لا يوجد أسعار</div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <input type="hidden" id="field_id" value="">

    <div class="mt-3">
        <label class="fw-bold small text-muted mb-2">2. المواعيد المتاحة:</label>
        <div id="slots_container" class="bg-white rounded-4 shadow-sm p-3 min-vh-50">
            <div class="text-center py-5">
                <i class="fas fa-hand-pointer fa-2x text-muted mb-2 opacity-50"></i>
                <p class="text-muted small">اسحب الملاعب واختر واحداً لعرض المواعيد</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.bookings.store') }}" method="POST" class="modal-content border-0">
            @csrf
            <input type="hidden" name="field_id" id="hidden_field_id">
            <input type="hidden" name="booking_date" id="hidden_date">
            <input type="hidden" name="start_time" id="hidden_start_time">
            
            <div class="modal-header bg-success text-white py-3 border-0">
                <h6 class="modal-title fw-bold">حجز جديد - <span id="display_time"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold mb-1">اسم الكابتن</label>
                    <input type="text" name="user_name" class="form-control rounded-3" placeholder="مثلاً: احمد" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold mb-1">العربون</label>
                        <input type="number" name="deposit" class="form-control rounded-3" value="0">
                    </div>
                    <div class="col-6 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="is_constant" class="form-check-input" id="checkConst">
                            <label class="small fw-bold" for="checkConst">ثابت</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3">تأكيد الحجز</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <form id="updateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white py-3 border-0">
                    <h6 class="modal-title fw-bold">إدارة الحجز: <span id="edit_display_time_header"></span></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">اسم العميل</label>
                        <input type="text" name="user_name" id="edit_user_name" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">العربون</label>
                        <input type="number" name="deposit" id="edit_deposit" class="form-control rounded-3" required>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_constant" id="edit_is_constant">
                        <label class="small fw-bold" for="edit_is_constant">حجز ثابت</label>
                    </div>
                </div>
                <div class="modal-footer border-0 flex-column gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3">حفظ التعديلات</button>
                    <button type="button" class="btn btn-outline-danger w-100 py-2 small border-0" onclick="confirmDelete()">إلغاء الحجز</button>
                </div>
            </form>
            <form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
        </div>
    </div>
</div>

<script>
    // نفس دوال JavaScript الخاصة بك دون تغيير في المنطق لضمان عمل الـ AJAX
    function selectField(id) {
        document.querySelectorAll('.field-card').forEach(card => {
            card.classList.remove('selected-field');
            card.querySelector('.select-icon').classList.add('d-none');
        });
        const selectedCard = document.getElementById('card_' + id);
        selectedCard.classList.add('selected-field');
        selectedCard.querySelector('.select-icon').classList.remove('d-none');
        document.getElementById('field_id').value = id;
        loadSlots();
    }

    function loadSlots() {
        let fId = document.getElementById('field_id').value;
        let date = document.getElementById('booking_date').value;
        let container = document.getElementById('slots_container');
        if(!fId || !date) return;

        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><p class="small text-muted mt-2">جاري التحميل...</p></div>';

        fetch(`{{ route('admin.getSlots') }}?field_id=${fId}&date=${date}`)
            .then(res => res.text())
            .then(html => container.innerHTML = html);
    }

    function openBookingModal(time) {
        document.getElementById('hidden_field_id').value = document.getElementById('field_id').value;
        document.getElementById('hidden_date').value = document.getElementById('booking_date').value;
        document.getElementById('hidden_start_time').value = time;
        document.getElementById('display_time').innerText = formatAMPM(time);
        new bootstrap.Modal(document.getElementById('bookingModal')).show();
    }

    function openEditModal(bookingJson) {
        let booking = JSON.parse(bookingJson);
        document.getElementById('edit_user_name').value = booking.user_name;
        document.getElementById('edit_deposit').value = booking.deposit;
        document.getElementById('edit_is_constant').checked = booking.is_constant == 1;
        document.getElementById('edit_display_time_header').innerText = formatAMPM(booking.start_time);
        document.getElementById('updateForm').action = "/admin/bookings/" + booking.id;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function confirmDelete() {
        if (confirm('إلغاء الحجز؟')) {
            let form = document.getElementById('deleteForm');
            form.action = document.getElementById('updateForm').action;
            form.submit();
        }
    }

    function formatAMPM(time) {
        let [h, m] = time.split(':');
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return h + ':' + m + ' ' + ampm;
    }

    // تحديث البيانات تلقائياً كل دقيقة
setInterval(function() {
    // بنشيك لو فيه مودال مفتوح (سواء بتاع الحجز أو التعديل)
    const isBookingModalOpen = document.getElementById('bookingModal').classList.contains('show');
    const isEditModalOpen = document.getElementById('editModal').classList.contains('show');

    // لو مفيش أي مودال مفتوح، حدث البيانات براحتك
    if (!isBookingModalOpen && !isEditModalOpen) {
        console.log("جاري تحديث المواعيد تلقائياً...");
        loadSlots(); 
    } else {
        console.log("تم إيقاف التحديث التلقائي لأنك تقوم بإدخال بيانات حالياً.");
    }
}, 60000); // 60000 مللي ثانية = دقيقة واحدة
</script>
@endsection