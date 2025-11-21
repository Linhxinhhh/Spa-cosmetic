<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideCategory extends Model
{
    protected $table = 'guide_categories';
    protected $primaryKey = 'category_id';
    protected $fillable = ['name','slug','parent_id'];

    public function guides()
    {
        return $this->hasMany(Guide::class, 'category_id');
    }
}
