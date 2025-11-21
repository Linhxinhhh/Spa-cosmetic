<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class KbDocument extends Model {
  protected $fillable = ['title','source','meta'];
  protected $casts = ['meta'=>'array'];
  public function chunks(){ return $this->hasMany(KbChunk::class); }
}
