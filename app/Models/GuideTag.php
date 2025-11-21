<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideTag extends Model
{
    protected $table = 'guide_tags';
    protected $primaryKey = 'tag_id';
    protected $fillable = ['name','slug'];

    public function guides()
    {
        return $this->belongsToMany(Guide::class, 'guide_tag_pivot', 'tag_id', 'guide_id');
    }
}
