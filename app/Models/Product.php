<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
 protected $fillable=['name','slug','sku','price','currency','category','description','image_urls','media_paths','active'];
 protected function casts():array{return ['image_urls'=>'array','media_paths'=>'array','active'=>'boolean','price'=>'decimal:2'];}
 public function socialPosts(){return $this->hasMany(SocialPost::class);}
}