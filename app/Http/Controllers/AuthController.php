<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
 public function showLogin(){return view('auth.login');}
 public function login(Request $request){
  $credentials=$request->validate(['email'=>['required','email'],'password'=>['required']]);
  $user=User::where('email',$credentials['email'])->first();
  if(!$user||!Hash::check($credentials['password'],$user->password)) return back()->withErrors(['email'=>'The provided credentials are incorrect.'])->withInput();
  $request->session()->regenerate();$request->session()->put('admin_user_id',$user->id);return redirect()->intended(route('dashboard'));
 }
 public function showRegister(){abort_if(User::query()->exists(),404);return view('auth.register');}
 public function register(Request $request){
  abort_if(User::query()->exists(),403,'Administrator registration is already closed.');
  $data=$request->validate(['name'=>['required','string','max:120'],'email'=>['required','email','max:255','unique:users,email'],'password'=>['required','confirmed',Password::min(8)]]);
  $user=User::create($data);$request->session()->regenerate();$request->session()->put('admin_user_id',$user->id);return redirect()->route('dashboard');
 }
 public function logout(Request $request){$request->session()->forget('admin_user_id');$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('login');}
}