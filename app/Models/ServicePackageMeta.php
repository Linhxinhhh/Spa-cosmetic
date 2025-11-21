<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackageMeta extends Model {
    protected $table = 'service_package_meta';
    protected $primaryKey = 'package_service_id';
    public $incrementing = false;
    protected $fillable = ['package_service_id','total_sessions','default_duration_min','min_gap_days','max_gap_days','allowed_dow','active'];
    protected $casts = ['allowed_dow'=>'array'];
    public function service() { return $this->belongsTo(Service::class, 'package_service_id', 'service_id'); }
  

    public function steps()
    {
        return $this->hasMany(ServicePackageStep::class, 'package_service_id', 'package_service_id')
            ->orderBy('step_no');
    }
}
