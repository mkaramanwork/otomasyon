<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TetkikPlan;

class TetkikMail extends Mailable
{
    use Queueable, SerializesModels;

    public $plan;
    public $tip;

    /**
     * Create a new message instance.
     */
    public function __construct(TetkikPlan $plan, $tip)
    {
        $this->plan = $plan;
        $this->tip = $tip;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = match($this->tip) {
            'tetkik_planlandi' => 'İç Tetkik Planlandı',
            'tetkik_baslatildi' => 'İç Tetkik Başlatıldı',
            'tetkik_tamamlandi' => 'İç Tetkik Tamamlandı',
            default => 'İç Tetkik Bildirimi',
        };

        return $this->subject($subject)
                    ->view('tetkik.email')
                    ->with([
                        'plan' => $this->plan,
                        'tip'  => $this->tip,
                    ]);
    }
}