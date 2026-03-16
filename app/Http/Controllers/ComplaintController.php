<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;        
use App\Models\ComplaintFile;    
use App\Models\ComplaintAction;  
use App\Models\Product;          
use App\Models\User;             
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;


class ComplaintController extends Controller
    {
    public function index()
    {
        $complaints = Complaint::with(['product','user'])->get();
        $products   = Product::all();   
        $users      = User::all();      

        return view('complaints.index', compact('complaints','products','users'));
    }

    public function create(){
        $products = Product::all();
        return view('complaints.create',compact('products'));
    }

    public function store(Request $request){
        $last = Complaint::latest()->first();
        $nextNo = $last ? '2026-'.str_pad($last->id+1,3,'0',STR_PAD_LEFT) : '2026-001';

        $complaint = Complaint::create([
            'complaint_no'=>$nextNo,
            'received_date'=>now(),
            'subject'=>$request->subject,
            'description'=>$request->description,
            'owner'=>$request->owner,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'received_by'=>$request->received_by,
            'product_id'=>$request->product_id,
            'user_id'=>auth()->id(),
            'status'=>'open'
        ]);

        // dosya upload
        if($request->hasFile('files')){
            foreach($request->file('files') as $file){
                $path = $file->store('complaints','public');
                ComplaintFile::create([
                    'complaint_id'=>$complaint->id,
                    'file_path'=>$path,
                    'file_type'=>$file->getClientOriginalExtension(),
                    'stage'=>'initial'
                ]);
            }
        }

        // sabit mail bilgilendirmesi
        Mail::raw("
        Yeni şikayet kaydı oluşturuldu.

        Şikayet No: {$complaint->complaint_no}
        Konu: {$complaint->subject}
        Tanım: {$complaint->description}
        Kayıt Tarihi: {$complaint->received_date}
        ", function($msg){
            $msg->to(['mrt.krmn.55@gmail.com','info@revonex.com.tr'])
                ->subject('Yeni Şikayet Kaydı');
        });


        return redirect()->route('complaints.index');
    }

    public function show($id)
    {
        $complaint = Complaint::with(['files','actions','product','user'])->findOrFail($id);

        return view('complaints.show', compact('complaint'));
    }
    public function edit($id)
    {
        $complaint = Complaint::with(['files','actions','product','user'])->findOrFail($id);
        $products  = Product::all();
        $users     = User::all();

        return view('complaints.edit', compact('complaint','products','users'));
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $complaint->update([
            'subject'        => $request->subject,
            'description'    => $request->description,
            'owner'          => $request->owner,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'received_by'    => $request->received_by,
            'product_id'     => $request->product_id,
            'evaluation'     => $request->evaluation,
            'evaluation_date'=> $request->evaluation_date ?? now(),
            'evaluated_by'   => $request->evaluated_by,
            'result'         => $request->result,
            'dif_opened'     => $request->dif_opened,
            'dif_description'=> $request->dif_description,
            'feedback'       => $request->feedback,
            'status'         => $request->status ?? $complaint->status,
        ]);

        // Dosya yükleme (sonuç aşaması için)
        if($request->hasFile('files')){
            foreach($request->file('files') as $file){
                $path = $file->store('complaints','public');
                ComplaintFile::create([
                    'complaint_id' => $complaint->id,
                    'file_path'    => $path,
                    'file_type'    => $file->getClientOriginalExtension(),
                    'stage'        => 'result'
                ]);
            }
        }

        // Sabit mail bilgilendirmesi
        Mail::raw("
        Şikayet güncellendi.

        Şikayet No: {$complaint->complaint_no}
        Konu: {$complaint->subject}
        Tanım: {$complaint->description}
        Kayıt Tarihi: {$complaint->received_date}
        ", function($msg){
            $msg->to(['mrt.krmn.55@gmail.com','info@revonex.com.tr'])
                ->subject('Şikayet Güncelleme');
        });

        return redirect()->route('complaints.show',$complaint->id)->with('success','Şikayet başarıyla güncellendi.');
    }


    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);

        $complaint->delete();

        Mail::raw("
        Şikayet silindi.

        Şikayet No: {$complaint->complaint_no}
        Konu: {$complaint->subject}
        Tanım: {$complaint->description}
        Kayıt Tarihi: {$complaint->received_date}
        ", function($msg){
            $msg->to(['mrt.krmn.55@gmail.com','info@revonex.com.tr'])
                ->subject('Şikayet Silme Bildirimi');
        });

        return redirect()->route('complaints.index')->with('success','Şikayet başarıyla silindi.');
    }



    public function exportWord($id)
    {
        $complaint = Complaint::with(['product','user','files'])->findOrFail($id);

        $template = new TemplateProcessor(storage_path('app/templates/complaint_template.docx'));

        // Normal alanlar
        $template->setValue('complaint_no', $complaint->complaint_no);
        $template->setValue('subject', $complaint->subject);
        $template->setValue('description', $complaint->description);
        $template->setValue('owner', $complaint->owner);
        $template->setValue('phone', $complaint->phone);
        $template->setValue('email', $complaint->email);
        $template->setValue('product', $complaint->product->name);
        $template->setValue('user', $complaint->user->name);
        $template->setValue('status', $complaint->status);
        $template->setValue('evaluation', $complaint->evaluation);
        $template->setValue('result', $complaint->result);
        $template->setValue('dif_opened', $complaint->dif_opened);
        $template->setValue('dif_description', $complaint->dif_description);
        $template->setValue('feedback', $complaint->feedback);

        // Dosyalar (ilk 3 örnek)
        $files = $complaint->files->take(3);
        foreach($files as $index => $f){
            $placeholder = 'file'.($index+1);
            $path = storage_path('app/public/'.$f->file_path);

            if(in_array(strtolower($f->file_type), ['jpg','jpeg','png'])){
                // Resim bas
                $template->setImageValue($placeholder, [
                    'path' => $path,
                    'width' => 200,
                    'height' => 200,
                    'ratio' => true,
                ]);
            } else {
                // Sadece isim yaz
                $template->setValue($placeholder, $f->file_path);
            }
        }

        // Eksik placeholderları boş bırak
        for($i = count($files)+1; $i <= 3; $i++){
            $template->setValue('file'.$i, '');
        }

        // Kaydet ve indir
        $fileName = "complaint_{$complaint->complaint_no}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $template->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

}
