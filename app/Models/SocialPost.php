<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SocialPost extends Model {
 protected $fillable=['product_id','title','caption','media_type','media_urls','video_url','status','scheduled_at','published_at','created_by'];
 protected function casts():array{return ['media_urls'=>'array','scheduled_at'=>'datetime','published_at'=>'datetime'];}
 public function publications(){return $this->hasMany(SocialPostPublication::class);}
 public function product(){return $this->belongsTo(Product::class);}
}
