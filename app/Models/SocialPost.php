<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SocialPost extends Model {
 protected $fillable=['title','caption','media_type','media_urls','video_url','status','scheduled_at','published_at','created_by'];
 protected $casts=['media_urls'=>'array','scheduled_at'=>'datetime','published_at'=>'datetime'];
 public function publications(){return $this->hasMany(SocialPostPublication::class);}
}
