<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentPlan extends Model
{
    protected $table = 'treatment_plans';

    protected $fillable = [
        'customer_id',
        'package_service_id',
        'single_service_id',
        'start_date',
        'preferred_dow',
        'preferred_time_range',
        'branch_id',
        'staff_id',
        'room_id',
        'status',
        'note',
    ];

    protected $casts = [
        'preferred_dow'        => 'array',
        'preferred_time_range' => 'array',
        'start_date'           => 'date',
    ];

    public function customer()
    {
        // nếu PK customers là customer_id thì đổi 'id' -> 'customer_id'
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function packageService()
    {
        return $this->belongsTo(Service::class, 'package_service_id', 'service_id');
    }

    public function singleService()
    {
        return $this->belongsTo(Service::class, 'single_service_id', 'service_id');
    }

    public function sessions()
    {
        return $this->hasMany(TreatmentSession::class, 'treatment_plan_id')->orderBy('session_no');
    }
}
