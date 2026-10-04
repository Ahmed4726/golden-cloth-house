<?php
namespace App\Models;
use App\Enums\SocialProvider;
use Illuminate\Database\Eloquent\Model;
class SocialConnection extends Model {
 protected $fillable=['provider','name','external_id','access_token','refresh_token','token_expires_at','metadata','active','status','last_error'];
 protected $casts=['provider'=>SocialProvider::class,'access_token'=>'encrypted','refresh_token'=>'encrypted','token_expires_at'=>'datetime','metadata'=>'array','active'=>'boolean'];
 public function tokenIsExpired():bool{return $this->token_expires_at?->isPast()??false;}
}
