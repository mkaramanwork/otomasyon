<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'production_order_id',
        'step',
        'status',
        'started_at',
        'finished_at',
        'executed_by'
    ];

    // 🔗 İlişkiler

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
    public function chemicals()
    {
        return $this->hasMany(ProductionStepChemical::class);
    }
    public function isCompleted()
    {
        $expected = $this->formula->chemicals->count();
        $entered = $this->chemicals->count();

        return $entered >= $expected;
    }
    public function complete(ProductionStep $step)
    {
        $step->update(['status' => 'tamamlandı']);

        return redirect()->back()->with('success', 'Adım tamamlandı olarak işaretlendi.');
    }
}