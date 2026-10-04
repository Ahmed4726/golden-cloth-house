<?php
namespace App\Models;
use App\Enums\SocialProvider;
use Illuminate\Database\Eloquent\Model;
class SocialPostPublication extends Model {
 protected $fillable=['social_post_id','social_connection_id','provider','status','external_id','error','response','attempts','published_at'];
 protected $casts=['provider'=>SocialProvider::class,'response'=>'array','published_at'=>'datetime'];
 public function post(){return $this->belongsTo(SocialPost::class,'social_post_id');}
 public function connection(){return $this->belongsTo(SocialConnection::class,'social_connection_id');}
}
