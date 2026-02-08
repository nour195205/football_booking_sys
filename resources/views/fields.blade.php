<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مواعيد الملاعب</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; }
        .field-card { border: none; border-radius: 15px; cursor: pointer; transition: 0.3s; }
        .field-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .section-title { color: #1e293b; font-weight: 800; }
    </style>
</head>
<body>

<div class="container py-4">
    <h2 class="text-center mb-5 section-title">استعراض المواعيد اليومية</h2>

    {{-- قسم اختيار الملعب --}}
    <div class="row mb-5">
        @foreach($fields as $field)
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm field-card" onclick="selectField({{ $field->id }}, '{{ $field->name }}')">
                <div class="card-body text-center">
                    <i class="bi bi-stadium fa-2x text-success mb-2"></i>
                    <h5 class="fw-bold mb-0">{{ $field->name }}</h5>
                    <small class="text-muted">اضغط لعرض الجدول</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- قسم عرض المواعيد --}}
    <div id="display-section" class="d-none p-4 bg-white shadow-sm rounded-4 border-0">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 id="selected-field-name" class="fw-bold m-0 text-success"></h4>
            <div class="d-flex align-items-center gap-2">
                <label class="small fw-bold text-muted">التاريخ:</label>
                <input type="date" id="booking-date" class="form-control shadow-sm" value="{{ date('Y-m-d') }}" onchange="loadSlots()">
            </div>
        </div>

        <div id="slots-load-area">
            </div>
    </div>
</div>

<script>
    let currentFieldId = null;

    function selectField(id, name) {
        currentFieldId = id;
        document.getElementById('display-section').classList.remove('d-none');
        document.getElementById('selected-field-name').innerText = "جدول " + name;
        loadSlots();
        document.getElementById('display-section').scrollIntoView({ behavior: 'smooth' });
    }

    async function loadSlots() {
        if (!currentFieldId) return;
        const date = document.getElementById('booking-date').value;
        const area = document.getElementById('slots-load-area');
        
        area.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div><p class="mt-2 text-muted small">جاري جلب المواعيد...</p></div>';
        
        try {
            const response = await fetch(`/get-slots-html?field_id=${currentFieldId}&date=${date}`);
            const html = await response.text();
            area.innerHTML = html;
        } catch (e) {
            area.innerHTML = '<div class="alert alert-danger text-center">تعذر تحميل البيانات حالياً</div>';
        }
    }
</script>
</body>
</html>