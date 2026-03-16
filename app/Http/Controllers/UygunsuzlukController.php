<?php
namespace App\Http\Controllers;

use App\Models\Uygunsuzluk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UygunsuzlukMail;

class UygunsuzlukController extends Controller
{
    public function index()
    {
        $records = Uygunsuzluk::orderByDesc('id')->get();
        return view('uygunsuzluk.index', compact('records'));
    }

    public function create()
    {
        return view('uygunsuzluk.create');
    }

    public function store(Request $request)
    {
        $uygunsuzluk = Uygunsuzluk::create($request->all());

        // 📧 Mail gönderimi (Yeni kayıt)
        Mail::to(['laboratuvar@aripet.com', 'mrt.krmn.55@gmail.com'])
            ->send(new UygunsuzlukMail($uygunsuzluk, 'uygunsuzluk_new'));

        return redirect()->route('uygunsuzluk.index')
                         ->with('success','Uygunsuzluk kaydı eklendi.');
    }

    public function destroy(Uygunsuzluk $uygunsuzluk)
    {
        $uygunsuzluk->delete();

        // 📧 Mail gönderimi (Silme yerine kapatma gibi düşünebilirsin)
        Mail::to(['laboratuvar@aripet.com', 'mrt.krmn.55@gmail.com'])
            ->send(new UygunsuzlukMail($uygunsuzluk, 'uygunsuzluk_close'));

        return redirect()->route('uygunsuzluk.index')
                        ->with('success', 'Uygunsuzluk kaydı silindi.');
    }

    public function edit(Uygunsuzluk $uygunsuzluk)
    {
        return view('uygunsuzluk.edit', compact('uygunsuzluk'));
    }

    public function show(Uygunsuzluk $uygunsuzluk)
    {
        return view('uygunsuzluk.show', compact('uygunsuzluk'));
    }

    public function update(Request $request, Uygunsuzluk $uygunsuzluk)
    {
        $data = $request->all();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            $data['file_name'] = $filename;
            $data['uploaded_on'] = now();
        }

        $uygunsuzluk->update($data);

        // 📧 Mail gönderimi (Güncelleme)
        Mail::to(['laboratuvar@aripet.com', 'mrt.krmn.55@gmail.com'])
            ->send(new UygunsuzlukMail($uygunsuzluk, 'uygunsuzluk_update'));

        return redirect()->route('uygunsuzluk.index')
                        ->with('success', 'Uygunsuzluk kaydı güncellendi.');
    }
}