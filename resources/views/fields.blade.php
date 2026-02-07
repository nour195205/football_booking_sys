<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حجز الملاعب - نظام الحجز</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .field-card { transition: 0.3s; border: none; border-radius: 15px; overflow: hidden; cursor: pointer; }
        .field-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .price-tag { font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; background: #e8f5e9; color: #2e7d32; font-weight: 600; display: inline-block; margin: 2px; border: 1px solid #c8e6c9; }
        .slot { cursor: pointer; transition: 0.3s; border: 1px solid #dee2e6; border-radius: 10px; text-align: center; }
        .slot strong { font-size: 1rem; display: block; }
        .occupied { background-color: #e74c3c !important; color: white; cursor: not-allowed; border-color: #e74c3c; }
        .available { background-color: #2ecc71 !important; color: white; border-color: #2ecc71; }
        .available:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold text-success"><i class="bi bi-stadium"></i> ملاعبنا المتاحة</h1>

    <div class="row mb-5">
        @foreach($fields as $field)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm field-card" onclick="selectField({{ $field->id }}, '{{ $field->name }}')">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">{{ $field->name }}</h5>
                    
                    <div class="mb-3">
                        <h6 class="small fw-bold text-success mb-2"><i class="bi bi-tags-fill"></i> فترات الأسعار:</h6>
                        @if($field->prices && $field->prices->count() > 0)
                            @foreach($field->prices as $price)
                                <div class="price-tag">
                                    {{ $price->label }}: 
                                    {{ \Carbon\Carbon::parse($price->from_time)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($price->to_time)->format('g:i A') }} 
                                    | {{ number_format($price->price, 0) }} ج.م
                                </div>
                            @endforeach
                        @else
                            <span class="text-muted small">لا توجد أسعار مخصصة حالياً</span>
                        @endif
                    </div>

                    <button class="btn btn-success w-100 rounded-pill mt-2">عرض المواعيد</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="booking-section" class="d-none p-4 bg-white shadow rounded border-top border-success border-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-7">
                <h3 id="selected-field-name" class="m-0 text-success fw-bold"></h3>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="input-group">
                    <span class="input-group-text bg-success text-white"><i class="bi bi-calendar3"></i></span>
                    <input type="date" id="booking-date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>
        </div>

        <div id="slots-display" class="row g-3">
            <div class="text-center text-muted py-5">جاري التحميل...</div>
        </div>
    </div>
</div>

<script>
    let currentFieldId = null;

    function selectField(id, name) {
        currentFieldId = id;
        document.getElementById('booking-section').classList.remove('d-none');
        document.getElementById('selected-field-name').innerText = "مواعيد ملعب: " + name;
        loadSlots();
        document.getElementById('booking-section').scrollIntoView({ behavior: 'smooth' });
    }

    async function loadSlots() {
    if (!currentFieldId) return;
    const date = document.getElementById('booking-date').value;
    const display = document.getElementById('slots-display');
    
    display.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div><p class="mt-2">جاري تحميل الساعات...</p></div>';
    
    try {
        // نستخدم الروت المخصص لليوزر (تأكد من وجوده في web.php)
        const response = await fetch(`/get-slots-html?field_id=${currentFieldId}&date=${date}`);
        const html = await response.text();
        display.innerHTML = html;
    } catch (e) {
        display.innerHTML = '<div class="alert alert-danger">خطأ في الاتصال بالسيرفر</div>';
    }
}

    document.getElementById('booking-date').onchange = loadSlots;

    function bookSlot(hour) {
        alert("فتح نافذة الحجز للساعة " + hour + ":00");
    }
</script>

</body>
</html>