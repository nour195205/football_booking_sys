<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    // عرض كل الملاعب في صفحة الـ Blade
    public function index()
    {
        $fields = Field::all();
        return view('admin.fields.index', compact('fields'));
    }

    // عرض صفحة إضافة ملعب جديد
    public function create()
    {
        return view('admin.fields.create');
    }

    // حفظ الملعب الجديد
    public function storeField(Request $request) {
    $request->validate([
        'name' => 'required|string',
        'prices' => 'required|array',
    ]);

    $field = Field::create(['name' => $request->name]);

    foreach ($request->prices as $p) {
        $field->prices()->create([
            'from_time' => $p['from'],
            'to_time'   => $p['to'],
            'price'     => $p['price'],
            'label'     => $p['label'] ?? null,
        ]);
    }

    return redirect()->route('admin.fields.index')->with('success', 'تم حفظ الملعب مع فترات التسعير بنجاح');
}

    // عرض صفحة تعديل ملعب موجود
    public function edit($id)
    {
        $field = Field::with('prices')->findOrFail($id);
        return view('admin.fields.edit', compact('field'));
    }

    // تحديث بيانات الملعب
    public function updateField(Request $request, $id) {
    $field = \App\Models\Field::findOrFail($id);
    $field->update(['name' => $request->name]);

    if ($request->has('prices')) {
        // حذف القديم وإضافة الجديد لضمان النظافة
        $field->prices()->delete();
        foreach ($request->prices as $priceData) {
            $field->prices()->create([
                'from_time' => $priceData['from'],
                'to_time'   => $priceData['to'],
                'price'     => $priceData['price'],
                'label'     => $priceData['label'] ?? null,
            ]);
        }
    }
    return redirect()->route('admin.fields.index')->with('success', 'تم التحديث بنجاح');
}

    // حذف ملعب
    public function destroy($id)
    {
        $field = Field::findOrFail($id);
        
        // الحذف هنا هيمسح كل الحجوزات والأسعار المرتبطة لو عامل Cascade في الداتابيز
        $field->delete();

        return redirect()->route('admin.fields.index')->with('success', 'تم حذف الملعب بنجاح');
    }
}