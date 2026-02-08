<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حالة ملاعب سانتياجو</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .field-card { border-radius: 25px; border: none; cursor: pointer; transition: 0.3s; padding: 30px; min-height: 200px; }
        .field-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .field-card h5 { font-size: 2.5rem; margin-bottom: 15px; }
        .status-header { background: #198754; color: white; border-radius: 20px; padding: 50px; margin-bottom: 50px; }
        .status-header h2 { font-size: 3rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="status-header text-center shadow">
        <h2 class="fw-bold"><i class="bi bi-stadium"></i> جدول حجز الملاعب اليومي</h2>
        <p class="mb-0">تابع المواعيد المتاحة والمحجوزة لحظة بلحظة</p>
    </div>

    <div class="row mb-5 justify-content-center">
        @foreach($fields as $field)
        <div class="col-md-6 mb-4">
            <div class="card h-100 field-card shadow-sm" onclick="showSchedule({{ $field->id }}, '{{ $field->name }}')">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="fw-bold text-success">{{ $field->name }}</h5>
                    <small class="text-muted fs-5">اضغط لعرض المواعيد</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="schedule-section" class="d-none bg-white p-5 rounded-4 shadow border-0">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h4 id="field-title" class="fw-bold text-dark m-0 display-6"></h4>
            <input type="date" id="search-date" class="form-control form-control-lg w-auto shadow-sm" value="{{ date('Y-m-d') }}" onchange="refreshSlots()">
        </div>
        <div id="slots-view">
            </div>
    </div>
</div>

<script>
    let activeFieldId = null;

    function showSchedule(id, name) {
        activeFieldId = id;
        document.getElementById('schedule-section').classList.remove('d-none');
        document.getElementById('field-title').innerText = "مواعيد " + name;
        refreshSlots();
        document.getElementById('schedule-section').scrollIntoView({ behavior: 'smooth' });
    }

    // جوه ملف الـ View الأساسي لليوزر
function refreshSlots() {
    if(!activeFieldId) return;
    const date = document.getElementById('search-date').value;
    const view = document.getElementById('slots-view');
    view.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div></div>';
    
    // تأكد إنك كريت روت في web.php بيشاور على ميثود publicSlots
    fetch(`/public-slots?field_id=${activeFieldId}&date=${date}`)
        .then(res => res.text())
        .then(html => view.innerHTML = html);
}
</script>
</body>
</html>