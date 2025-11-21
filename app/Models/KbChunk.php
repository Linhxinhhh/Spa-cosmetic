<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KbChunk extends Model {
  protected $fillable = ['kb_document_id','ord','content','tokens'];
  public function doc(){ return $this->belongsTo(KbDocument::class,'kb_document_id'); }
}
