<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Document;
use App\Models\DocumentRevision;

class DocumentController extends Controller
{
    // 📑 Dokümanları listele
    public function index()
    {
        $documents = Document::with('latestRevision')->get();
        return view('documents.index', compact('documents'));
    }

    // ➕ Yeni doküman formu
    public function create()
    {
        return view('documents.create');
    }

    // 📥 Doküman kaydet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doc_no' => 'required|unique:documents',
            'title' => 'required',
            'revision_no' => 'required|integer',
            'publish_date' => 'required|date',
            'last_revision_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'revision_reason' => 'nullable|string',
            'status' => 'required|in:guncel,arsiv'
        ]);

        $validated['created_by'] = auth()->user()->name ?? 'system';
        $document = Document::create($validated);

        // ✅ Mail gönder
        Mail::send('documents.mail', ['document' => $document], function($message) use ($document) {
            $message->to('laboratuvar@aripet.com')
                    ->cc(['kalite@firma.com'])
                    ->subject("Yeni Doküman - {$document->doc_no}");
        });

        return redirect()->route('documents.index')->with('success','Doküman başarıyla eklendi ve mail gönderildi ✅');
    }

    // 📄 Doküman detay
    public function show(Document $document)
    {
        $document->load('revisions');
        return view('documents.show', compact('document'));
    }

    // ✏️ Doküman düzenleme formu
    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    // 🔄 Doküman güncelle
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title'=>'required',
            'revision_no'=>'required|integer',
            'publish_date'=>'required|date',
            'status'=>'required|in:guncel,arsiv'
        ]);

        $document->update($validated);

        // ✅ Mail gönder
        Mail::send('documents.mail', ['document' => $document], function($message) use ($document) {
            $message->to('laboratuvar@aripet.com')
                    ->subject("Doküman Güncellendi - {$document->doc_no}");
        });

        return redirect()->route('documents.index')->with('success','Doküman güncellendi ve mail gönderildi ✅');
    }

    // ❌ Doküman sil
    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success','Doküman silindi ✅');
    }

    // ➕ Revizyon ekle
    public function storeRevision(Request $request, Document $document)
    {
        $validated = $request->validate([
            'file'           => 'required|mimes:pdf,docx|max:4096',
            'change_note'    => 'nullable|string',
            'revision_date'  => 'required|date',
            'review_date'    => 'nullable|date'
        ]);

        $revNo = $document->revisions()->max('revision_no') + 1;
        $path = $request->file('file')->store('documents','public');

        $revision = $document->revisions()->create([
            'revision_no'   => $revNo,
            'revision_date' => $request->revision_date,
            'review_date'   => $request->review_date,
            'change_note'   => $request->change_note,
            'file_path'     => $path,
            'updated_by'    => auth()->user()->name ?? 'system'
        ]);

        $document->update([
            'revision_no'        => $revNo,
            'last_revision_date' => $request->revision_date,
            'review_date'        => $request->review_date,
            'revision_reason'    => $request->change_note
        ]);

        // ✅ Mail gönder
        Mail::send('documents.mail_rev', ['document' => $document, 'revision' => $revision], function($message) use ($document, $revision) {
            $message->to('laboratuvar@aripet.com')
                    ->cc(['kalite@firma.com','bakim@firma.com'])
                    ->subject("Yeni Revizyon - {$document->doc_no} / R{$revision->revision_no}");
        });

        return back()->with('success','Revizyon eklendi ve mail gönderildi ✅');
    }
}