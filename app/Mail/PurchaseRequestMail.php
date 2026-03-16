<?php
namespace App\Mail;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(PurchaseRequest $request)
    {
        $this->request = $request;
    }

    public function build()
    {
        return $this->subject('Satınalma Talebi: '.$this->request->request_code)
                    ->view('purchase_requests.email'); // doğru view yolu
    }
}