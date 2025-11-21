<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ServicePackageStep extends Model {
    protected $fillable = ['package_service_id','step_no','child_service_id','title','duration_min','min_gap_days','max_gap_days','notes'];
    public function package() { return $this->belongsTo(Service::class, 'package_service_id', 'service_id'); }
    public function child()   { return $this->belongsTo(Service::class, 'child_service_id', 'service_id'); }
}