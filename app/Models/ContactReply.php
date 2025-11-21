<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactReply extends Model
{
    use HasFactory;
    protected $table = 'contact_replies';
    protected $fillable = ['contact_id','admin_id','via','message'];

    public function contact() { return $this->belongsTo(Contact::class); }
    public function admin()   { return $this->belongsTo(User::class, 'admin_id'); }
}
