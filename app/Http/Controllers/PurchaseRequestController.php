<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Mail\PurchaseRequestMail;
use Illuminate\Support\Facades\Mail;



class PurchaseRequestController extends Controller
{
    public function index()
    {
        $requests = PurchaseRequest::with('items.inventory')->get();
        return view('purchase_requests.index', compact('requests'));
    }

    public function create()
    {
        $inventories = Inventory::with('technicalSpecification')->get();
        $suppliers = Supplier::all();
        return view('purchase_requests.create', compact('inventories','suppliers'));
    }
    public function store(Request $request)
    {
        $lastCode = PurchaseRequest::latest()->value('request_code');
        $nextNumber = $lastCode ? intval(substr($lastCode,3)) + 1 : 1;
        $requestCode = 'SP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $purchaseRequest = PurchaseRequest::create([
            'request_code' => $requestCode,
            'status' => 'Beklemede'
        ]);

        foreach($request->items as $item){
            $purchaseRequest->items()->create($item);
        }

        return redirect()->route('purchase-requests.index')
                         ->with('success','Satınalma talebi oluşturuldu.');
    }

    public function show(PurchaseRequest $request)
    {
        $request->load('items.inventory.technicalSpecification','items.supplier');
        return view('purchase_requests.show', compact('request'));
    }


    public function edit(PurchaseRequest $request)
    {
        $request->load('items');
        $inventories = Inventory::all();
        $suppliers = Supplier::all();
        return view('purchase_requests.edit', compact('request','inventories','suppliers'));
    }

    public function update(Request $req, PurchaseRequest $request)
    {
        $request->items()->delete();
        foreach($req->items as $item){
            $request->items()->create($item);
        }
        return redirect()->route('purchase-requests.show',$request->id)
                         ->with('success','Talep güncellendi.');
    }

    public function approve(PurchaseRequest $request, $step)
    {
        switch ($step) {
            case 'lab':
                $request->update(['status'=>'Laboratuvar Müdürü Onayı','lab_manager_name'=>auth()->user()->name]);
                break;
            case 'gm':
                $request->update(['status'=>'Genel Müdür Onayı','general_manager_name'=>auth()->user()->name]);
                break;
            case 'procurement':
                $request->update(['status'=>'Satınalma Talep Edildi','procurement_name'=>auth()->user()->name]);
                break;
            case 'order':
                $request->update(['status'=>'Sipariş Sürecinde','ordered_at'=>now(),'order_name'=>auth()->user()->name]);
                break;
            case 'control':
                $request->update(['status'=>'Kontrol Aşamasında','control_name'=>auth()->user()->name]);
                break;
            case 'acceptance':
                $request->update(['status'=>'Kabul Edildi','accepted_at'=>now(),'acceptance_name'=>auth()->user()->name]);
                break;
        }
        return back()->with('success','Onay adımı ilerletildi.');
    }


    public function sendMail(PurchaseRequest $request)
    {
        if(!$request->lab_manager_name){
            return back()->with('error','Laboratuvar Müdürü onayı yok, mail gönderilemez.');
        }

        $request->load('items.inventory.technicalSpecification','items.supplier');

        // Birden fazla adres
        $recipients = [
            "laboratuvar@aripet.com",
            "mrt.krmn.55@gmail.com",
            "kontrol@firma.com"
        ];

        Mail::to($recipients)->send(new PurchaseRequestMail($request));

        return back()->with('success','Mail başarıyla iletildi.');
    }


}