<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ProductController extends Controller {
 public function index(){return view('products.index',['products'=>Product::latest()->paginate(20)]);}
 public function create(){return view('products.create');}
 public function store(Request $request){
  $d=$request->validate(['name'=>'required|string|max:255','slug'=>'nullable|string|max:255|unique:products,slug','sku'=>'nullable|string|max:100|unique:products,sku','price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','category'=>'nullable|string|max:120','description'=>'nullable|string','image_urls'=>'nullable|string']);
  $urls=collect(preg_split('/\r?\n/',$d['image_urls']??''))->map(fn($v)=>trim($v))->filter()->values()->all();
  validator(['image_urls'=>$urls],['image_urls'=>'array|max:20','image_urls.*'=>'url'])->validate();
  Product::create(['name'=>$d['name'],'slug'=>$d['slug']?:Str::slug($d['name']),'sku'=>($d['sku']??'')?:null,'price'=>$d['price']??null,'currency'=>strtoupper($d['currency']),'category'=>$d['category']??null,'description'=>$d['description']??null,'image_urls'=>$urls,'active'=>$request->boolean('active',true)]);
  return redirect()->route('products.index')->with('success','Product added.');
 }
 public function edit(Product $product){return view('products.edit',compact('product'));}
 public function update(Request $request,Product $product){
  $d=$request->validate(['name'=>'required|string|max:255','slug'=>'required|string|max:255|unique:products,slug,'.$product->id,'sku'=>'nullable|string|max:100|unique:products,sku,'.$product->id,'price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','category'=>'nullable|string|max:120','description'=>'nullable|string','image_urls'=>'nullable|string']);
  $urls=collect(preg_split('/\r?\n/',$d['image_urls']??''))->map(fn($v)=>trim($v))->filter()->values()->all();
  validator(['image_urls'=>$urls],['image_urls'=>'array|max:20','image_urls.*'=>'url'])->validate();
  $product->update(['name'=>$d['name'],'slug'=>$d['slug'],'sku'=>($d['sku']??'')?:null,'price'=>$d['price']??null,'currency'=>strtoupper($d['currency']),'category'=>$d['category']??null,'description'=>$d['description']??null,'image_urls'=>$urls,'active'=>$request->boolean('active')]);
  return redirect()->route('products.index')->with('success','Product updated.');
 }
 public function destroy(Product $product){$product->delete();return back()->with('success','Product removed.');}
}
