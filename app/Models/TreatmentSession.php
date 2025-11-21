<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentSession extends Model
{
    protected $fillable = [
        'treatment_plan_id','session_no','package_step_id',
        'scheduled_start','scheduled_end','staff_id','room_id','status',
        'checkin_at','checkout_at','note'
    ];
    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end'   => 'datetime',
        'checkin_at'      => 'datetime',
        'checkout_at'     => 'datetime',
    ];

        public function plan()
    {
               return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }
    public function packageStep() { return $this->belongsTo(\App\Models\ServicePackageStep::class, 'package_step_id'); }

}
