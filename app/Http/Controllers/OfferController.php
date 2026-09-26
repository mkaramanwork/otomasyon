<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\OfferParameter;
use App\Models\OfferSample;
use App\Models\Customer;
use App\Models\LabParameter;
use App\Models\SampleAcceptance;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with(['customer','samples','acceptedSamples'])->get();
        return view('offers.index', compact('offers'));
    }


    public function create()
    {
        $customers = Customer::all();
        $parameters = LabParameter::all();

        $year = date('Y');
        $last = Offer::whereYear('created_at',$year)->count() + 1;
        $offerNo = sprintf("%s-T%04d", $year, $last);

        return view('offers.create', compact('customers','parameters','offerNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'nullable|string',
            'text' => 'nullable|string',
            'offer_date' => 'required|date',
            'sample_count' => 'required|integer|min:1',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'parameters' => 'array',
        ]);

        $year = date('Y');
        $last = Offer::whereYear('created_at',$year)->count() + 1;
        $offerNo = sprintf("%s-T%04d", $year, $last);

        $offer = Offer::create([
            ...$validated,
            'offer_no' => $offerNo,
        ]);

        $total = 0;
        foreach($request->parameters ?? [] as $param){
            if(isset($param['selected'])){ // sadece seçilen parametreler
                OfferParameter::create([
                    'offer_id' => $offer->id,
                    'parameter_id' => $param['id'],
                    'method' => $param['method'],
                    'price' => $param['price'],
                    'scope' => $param['scope'],
                ]);
                $total += $param['price'];
            }
        }

        $net = $total - ($total * ($validated['discount_rate']/100));
        $offer->update(['total_amount'=>$total,'net_amount'=>$net]);

        return redirect()->route('offers.index')->with('success','Teklif kaydedildi.');
    }

    public function show(Offer $offer)
    {
       
        $offer->load([
            'parameters.parameter',
            'samples.sample',
            'customer'
        ]);

        
        $activeSamples  = SampleAcceptance::where('offer_id', $offer->id)->get();
        $deletedSamples = SampleAcceptance::onlyTrashed()->where('offer_id', $offer->id)->get();

        
        return view('offers.show', [
            'offer'          => $offer,
            'samples'        => $activeSamples,
            'deletedSamples' => $deletedSamples
        ]);
    }

    public function edit(Offer $offer)
    {
        $customers = Customer::all();
        $parameters = LabParameter::all();
        $offer->load('parameters'); // parametreleri yükle

        return view('offers.edit', compact('offer','customers','parameters'));
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'nullable|string',
            'text' => 'nullable|string',
            'offer_date' => 'required|date',
            'sample_count' => 'required|integer|min:1',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'parameters' => 'array',
        ]);

        $offer->update($validated);

        $total = 0;
        $offer->parameters()->delete(); // eski parametreleri sil
        foreach($request->parameters ?? [] as $param){
            if(isset($param['selected'])){ // sadece seçilenler
                OfferParameter::create([
                    'offer_id' => $offer->id,
                    'parameter_id' => $param['id'],
                    'method' => $param['method'],
                    'price' => $param['price'],
                    'scope' => $param['scope'],
                ]);
                $total += $param['price'];
            }
        }

        $net = $total - ($total * ($validated['discount_rate']/100));
        $offer->update(['total_amount'=>$total,'net_amount'=>$net]);

        return redirect()->route('offers.index')->with('success','Teklif güncellendi.');
    }

    public function uploadPdf(Request $request, Offer $offer)
    {
        $request->validate(['approved_pdf'=>'required|file|mimes:pdf|max:5120']);
        $path = $request->file('approved_pdf')->store('offers','public');
        $offer->update(['approved_pdf'=>$path,'status'=>'approved']);
        return back()->with('success','Onaylı teklif PDF yüklendi.');
    }

    public function attachSample(Request $request, Offer $offer)
    {
        $request->validate(['sample_id'=>'required|exists:sample_acceptances,id']);
        $sample = SampleAcceptance::findOrFail($request->sample_id);
        $count = $offer->samples()->count() + 1;

        OfferSample::create([
            'offer_id'=>$offer->id,
            'sample_id'=>$sample->id,
            'sequence'=>$count,
        ]);

        $offer->decrement('sample_count');
        return back()->with('success',"Numune teklife eklendi ({$count}/kalan:{$offer->sample_count}).");
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offers.index')->with('success','Teklif silindi.');
    }

    public function attachAllSamples(Offer $offer)
{
  
    $samples = SampleAcceptance::where('offer_id', $offer->id)->get();

    foreach($samples as $sample){
     
        $count = $offer->samples()->count() + 1;

        OfferSample::create([
            'offer_id'  => $offer->id,
            'sample_id' => $sample->id,
            'sequence'  => $count,
        ]);

      
        $offer->decrement('sample_count');
    }

    return back()->with('success','Tüm bağlı numuneler pivot tabloya kaydedildi ve kalan hak güncellendi.');
}

}
