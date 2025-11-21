<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $primarykey = 'id';
    protected $fillable = ['contact_id','question','answer','category','subcategory','sort_order','status','views', 'cover_image',];
    protected $casts = ['sort_order'=>'integer','views'=>'integer'];

   const STATUS_DRAFT     = 'Bản nháp';
    const STATUS_PUBLISHED = 'Xuất bản';

    // Scopes
    public function scopePublished($q){ return $q->where('status', self::STATUS_PUBLISHED); }
    public function scopeSearch($q, $term){
        $term = trim((string)$term);
        if ($term==='') return $q;
        return $q->where(fn($x)=>$x
            ->where('question','like',"%$term%")
            ->orWhere('answer','like',"%$term%")
            ->orWhere('category','like',"%$term%")
        );
    }
    public function scopeCategory($q, $cat){
        $cat = trim((string)$cat);
        return $cat ? $q->where('category',$cat) : $q;
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'contact_id');
    }
    public function user()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
 
}
