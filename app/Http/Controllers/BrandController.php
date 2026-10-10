<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('brands', 'name'),
            ],

            'template_file' => [
                'nullable',
                'string',
                'max:255',
                'regex:/\.docx$/i',
            ],
        ]);

        Brand::create([
            'name' => mb_strtoupper(trim($data['name'])),
            'template_file' => !empty($data['template_file'])
                ? trim($data['template_file'])
                : null,
        ]);

        return back()->with(
            'success',
            'Marka başarıyla eklendi.'
        );
    }

    public function destroy(Brand $brand)
    {
        if ($brand->tdsDocuments()->exists()) {
            return back()->with(
                'error',
                'Bu marka TDS kayıtlarında kullanıldığı için silinemez.'
            );
        }

        $brand->delete();

        return back()->with(
            'success',
            'Marka başarıyla silindi.'
        );
    }
}