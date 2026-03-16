<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('satinalma.tedarikci', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'accreditation' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Pasif',
            'performance_score' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success','Tedarikçi başarıyla eklendi.');
    }

    public function show(Supplier $supplier)
    {
        // Tedarikçi ile ilişkili tüm değerlendirmeleri çekiyoruz
        $evaluations = $supplier->evaluations()->with('user')->latest()->get();

        return view('satinalma.tedarikci_detay', compact('supplier','evaluations'));
    }
    public function edit(Supplier $supplier)
    {
        return view('satinalma.tedarikci_edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success','Tedarikçi güncellendi.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success','Tedarikçi silindi.');
    }

    public function exportTemplate($id)
    {
        $supplier = Supplier::with('evaluations.user')->findOrFail($id);




        $template = new \PhpOffice\PhpWord\TemplateProcessor(
            storage_path('app/templates/supplier_template.docx')
        );

        // Ana bilgiler
        $template->setValue('supplier_name', $supplier->name);
        $template->setValue('supplier_contact', $supplier->contact);
        $template->setValue('supplier_address', $supplier->address);
        $template->setValue('supplier_web', $supplier->web ?? '');
        $template->setValue('supplier_accreditation', $supplier->accreditation);
        $template->setValue('supplier_status', $supplier->status);
        $template->setValue('supplier_score', $supplier->performance_score);
        $template->setValue('supplier_notes', $supplier->notes ?? '');

        // Değerlendirmeler
        foreach ($supplier->evaluations as $index => $eval) {
            $i = $index + 1;
            $template->setValue("evaluation_date#{$i}", $eval->evaluation_date);
            $template->setValue("evaluation_user#{$i}", $eval->user->name);
            $template->setValue("evaluation_title#{$i}", $eval->title);
            $template->setValue("evaluation_certification#{$i}", $eval->certification);
            $template->setValue("evaluation_traceability#{$i}", $eval->traceability);
            $template->setValue("evaluation_complaint#{$i}", $eval->complaint_response);
            $template->setValue("evaluation_pricing#{$i}", $eval->pricing_policy);
            $template->setValue("evaluation_support#{$i}", $eval->technical_support);
            $template->setValue("evaluation_score#{$i}", $eval->score);
            $template->setValue("evaluation_status#{$i}", $eval->status);
        
        }

        // Özet bilgileri
        $template->setValue('evaluation_count', $supplier->evaluations->count());
        $template->setValue('evaluation_average', round($supplier->evaluations->avg('score'), 2));
        $template->setValue('evaluation_summary', $this->generateSummary($supplier->evaluations->avg('score')));

        $fileName = 'supplier_' . $supplier->id . '.docx';
        $template->saveAs($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    private function generateSummary($averageScore)
    {
        if ($averageScore >= 80) {
            return 'Uzun dönem çalışılır (Listeye eklenir)';
        } elseif ($averageScore >= 60) {
            return 'Üç yıl içinde iyileştirme istenir (Listeye eklenir)';
        } else {
            return 'Çalışılmaz (İyileştirme istenir)';
        }
    }
}