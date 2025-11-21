<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = ['user_id','title','meta','last_message_at'];
    protected $casts = ['meta'=>'array','last_message_at'=>'datetime'];

    public function messages() {
        return $this->hasMany(ChatMessage::class);
    }
}
