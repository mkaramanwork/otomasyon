<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExternalDocument;
use App\Models\ExternalDocumentRevision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\TemplateProcessor;

class ExternalDocumentController extends Controller
{
    public function index()
    {
        $docs = ExternalDocument::with('user')->get();
        return view('external_documents.index', compact('docs'));
    }

    public function create()
    {
        $lastSeq = ExternalDocument::max('sequence_no');
        $nextSeq = $lastSeq ? $lastSeq + 1 : 1;

        return view('external_documents.create', compact('nextSeq'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sequence_no'=>'required|integer',
            'document_code'=>'required',
            'title'=>'required',
            'institution'=>'required',
            'revision_no'=>'required',
            'publish_date'=>'required|date',
            'file'=>'required|file|mimes:pdf,doc,docx,ppt,pptx'
        ]);

        $path = $request->file('file')->store('external_docs','public');

        $doc = ExternalDocument::create([
            'sequence_no'=>$request->sequence_no,
            'document_code'=>$request->document_code,
            'title'=>$request->title,
            'institution'=>$request->institution,
            'revision_no'=>$request->revision_no,
            'publish_date'=>$request->publish_date,
            'review_date'=>$request->review_date,
            'url'=>$request->url,
            'file_path'=>$path,
            'created_by'=>Auth::id(),
        ]);

        $doc->load('user');
        $this->notify("Yeni doküman eklendi", $doc);

        return redirect()->route('external-documents.index')->with('success','Doküman eklendi.');
    }

    public function show(ExternalDocument $external_document)
    {
        $external_document->load('revisions','user');
        return view('external_documents.show', ['doc' => $external_document]);
    }

    public function edit(ExternalDocument $external_document)
    {
        return view('external_documents.edit', ['doc' => $external_document]);
    }

    public function update(Request $request, ExternalDocument $external_document)
    {
        $request->validate([
            'title'=>'required',
            'revision_no'=>'required',
            'publish_date'=>'required|date',
        ]);

        if($request->hasFile('file')){
            $path = $request->file('file')->store('external_docs','public');

            $external_document->revisions()->create([
                'revision_no'=>$external_document->revision_no,
                'publish_date'=>$external_document->publish_date,
                'file_path'=>$external_document->file_path,
            ]);

            $external_document->file_path = $path;
        }

        $external_document->update($request->only([
            'sequence_no','document_code','title','institution',
            'revision_no','publish_date','review_date','url'
        ]));

        $external_document->load('user');
        $this->notify("Doküman güncellendi", $external_document);

        return redirect()->route('external-documents.index')->with('success','Doküman güncellendi.');
    }

    public function destroy(ExternalDocument $external_document)
    {
        $external_document->load('user');
        $this->notify("Doküman silindi", $external_document);

        $external_document->delete();

        return back()->with('success','Doküman silindi.');
    }

    private function notify($action, ExternalDocument $doc)
    {
        $recipients = ['laboratuvar@aripet.com','mrt.krmn.55@gmail.com'];
        $userName = optional($doc->user)->name ?? 'Sistem';

        $message = <<<EOT
{$action}

Doküman Adı: {$doc->title}
Doküman Kodu: {$doc->document_code}
Kurum: {$doc->institution}
Revizyon No: {$doc->revision_no}
Yayın Tarihi: {$doc->publish_date}
Gözden Geçirilme Tarihi: {$doc->review_date}
URL: {$doc->url}
İşlemi Yapan: {$userName}
EOT;

        foreach($recipients as $to){
            Mail::raw($message, function($mail) use ($to, $action){
                $mail->to($to)
                     ->subject("Doküman Bildirimi - {$action}");
            });
        }
    }

    public function exportWord()
    {
        $templatePath = storage_path('app/templates/external_documents.docx');
        if (!file_exists($templatePath)) {
            abort(404, "Template bulunamadı: ".$templatePath);
        }

        $template = new TemplateProcessor($templatePath);
        $docs = ExternalDocument::with('revisions')->get();

        $template->cloneRow('sequence_no', $docs->count());

        foreach ($docs as $index => $doc) {
            $i = $index + 1;
            $template->setValue("sequence_no#{$i}", $doc->sequence_no);
            $template->setValue("document_code#{$i}", $doc->document_code);
            $template->setValue("title#{$i}", $doc->title);
            $template->setValue("institution#{$i}", $doc->institution);
            $template->setValue("revision_no#{$i}", $doc->revision_no);
            $template->setValue("publish_date#{$i}", $doc->publish_date);
            $template->setValue("review_date#{$i}", $doc->review_date);
            $template->setValue("url#{$i}", $doc->url);

            if ($doc->revisions->count() > 0) {
                $template->cloneRow("rev_no{$i}", $doc->revisions->count());
                foreach ($doc->revisions as $j => $rev) {
                    $r = $j + 1;
                    $template->setValue("rev_no{$i}#{$r}", $rev->revision_no);
                    $template->setValue("rev_date{$i}#{$r}", $rev->publish_date);
                    $template->setValue("rev_file{$i}#{$r}", $rev->file_path);
                }
            }
        }

        $fileName = storage_path('app/public/external_documents.docx');
        $template->saveAs($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }
}