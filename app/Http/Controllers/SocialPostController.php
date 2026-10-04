<?php
namespace App\Http\Controllers;
use App\Jobs\PublishSocialPost;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SocialPostController extends Controller {
 public function index(){return view('posts.index',['posts'=>SocialPost::with(['publications.connection','product'])->latest()->paginate(15)]);}
 public function create(){return view('posts.create',['connections'=>SocialConnection::where('active',true)->get(),'products'=>Product::where('active',true)->latest()->get()]);}
 public function store(Request $request){
  $d=$request->validate(['product_id'=>'nullable|integer|exists:products,id','title'=>'nullable|string|max:255','caption'=>'nullable|string|max:5000','media_type'=>'required|in:image,video','media_urls'=>'nullable|string','video_url'=>'nullable|url','connections'=>'required|array|min:1','connections.*'=>'integer|exists:social_connections,id','action'=>'required|in:draft,publish,schedule','scheduled_at'=>'nullable|date|after:now']);
  $product=$d['product_id']?Product::findOrFail($d['product_id']):null;
  $caption=$d['caption']??'';
  if($product){$caption=$caption?:$product->name.' — '.$product->description; if(empty($d['media_urls']))$d['media_urls']=implode("\n",$product->image_urls??[]); if(empty($d['title']))$d['title']=$product->name;}
  if($d['media_type']==='image'&&!trim($d['media_urls']??''))return back()->withErrors(['media_urls'=>'At least one image URL is required for an image post.'])->withInput();
  $urls=collect(preg_split('/\r?\n/',$d['media_urls']??''))->map(fn($v)=>trim($v))->filter()->values()->all();
  validator(['media_urls'=>$urls],['media_urls'=>'array|max:10','media_urls.*'=>'url'])->validate();
  if($d['action']==='schedule'&&!$d['scheduled_at'])return back()->withErrors(['scheduled_at'=>'A future date/time is required.'])->withInput();
  $status=$d['action']==='draft'?'draft':($d['action']==='schedule'?'scheduled':'queued');
  $post=DB::transaction(function()use($d,$urls,$status,$caption){$post=SocialPost::create(['product_id'=>$d['product_id']??null,'title'=>$d['title']??null,'caption'=>$caption,'media_type'=>$d['media_type'],'media_urls'=>$urls,'video_url'=>$d['video_url']??null,'status'=>$status,'scheduled_at'=>$d['scheduled_at']??null,'created_by'=>session('admin_user_id')]);foreach($d['connections'] as $id){$c=SocialConnection::whereKey($id)->where('active',true)->firstOrFail();$p=$post->publications()->create(['social_connection_id'=>$c->id,'provider'=>$c->provider,'status'=>$status==='draft'?'draft':'queued']);if($status==='queued')PublishSocialPost::dispatch($p->id);}return $post;});
  return redirect()->route('posts.show',$post)->with('success',$status==='queued'?'Publishing has been queued.':'Post saved.');
 }
 public function show(SocialPost $post){return view('posts.show',['post'=>$post->load(['publications.connection','product'])]);}
 public function publish(SocialPost $post){foreach($post->publications()->whereIn('status',['queued','failed','draft'])->get() as $p){$p->update(['status'=>'queued','error'=>null]);PublishSocialPost::dispatch($p->id);}$post->update(['status'=>'queued']);return back()->with('success','Publishing queued.');}
}
