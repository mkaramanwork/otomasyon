<?php



namespace App\Http\Controllers;

use App\Models\LabParameter;
use Illuminate\Http\Request;

class LabParameterController extends Controller
{
    /**
     * Parametre listesi
     */
    public function index()
    {
        $parameters = LabParameter::all();
        return view('parameters.index', compact('parameters'));
    }

    /**
     * Yeni parametre formu
     */
    public function create()
    {
        return view('parameters.create');
    }

    /**
     * Yeni parametre kaydet
     */
    public function store(Request $request)
    {
        $request->validate([
            'sample_type' => 'required|string',
            'parameter'   => 'required|string',
            'method_tr'   => 'required|string',
            'method_en'   => 'required|string',
            'method_ru'   => 'required|string',
            'unit'        => 'required|string',
            'scope'       => 'required|string',
            'uncertainty' => 'nullable|string', // ✅ text input uyumlu
        ]);

        LabParameter::create($request->all());

        return redirect()->route('parameters.index')->with('success', 'Parametre eklendi.');
    }

    /**
     * Parametre düzenleme formu
     */
    public function edit(LabParameter $parameter)
    {
        return view('parameters.edit', compact('parameter'));
    }

    /**
     * Parametre güncelle
     */
    public function update(Request $request, LabParameter $parameter)
    {
        $request->validate([
            'sample_type' => 'required|string',
            'parameter'   => 'required|string',
            'method_tr'   => 'required|string',
            'method_en'   => 'required|string',
            'method_ru'   => 'required|string',
            'unit'        => 'required|string',
            'scope'       => 'required|string',
            'uncertainty' => 'nullable|string', // ✅ text input uyumlu
        ]);

        $parameter->update($request->all());

        return redirect()->route('parameters.index')->with('success', 'Parametre güncellendi.');
    }

    /**
     * Parametre sil
     */
    public function destroy(LabParameter $parameter)
    {
        $parameter->delete();
        return redirect()->route('parameters.index')->with('success', 'Parametre silindi.');
    }
}