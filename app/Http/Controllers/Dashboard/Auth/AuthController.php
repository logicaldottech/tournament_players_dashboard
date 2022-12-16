<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(){

      if(Auth::check()){

        return redirect()->route('dashboard');
      }
    	return view('login');
    }

    public function authenticate(Request $request)
      {

        // return redirect()->route('dashboard');

          $validator  = $request->validate([
            'email'         => 'required|email',
            'password'      => 'required|min:6'
          ]);

          $email = $request->email;
          $password = $request->password;

          if($request->has('checkbox')){
            $remember = true;
           }else{
            $remember = false;
           }

           $user = User::where('email', '=', $email)
                        ->whereIn('role_id', [1,2])
                        ->first();
           // dd($user);

           // check credentials
          if ($user) {
             
             $auth = Hash::check($password, trim($user->password));

          }else{

              return back()->with('error','Email is wrong');

          }

          if ($auth) {
             
             // Authentication passed...

      		Auth::loginUsingId($user->id);

           return redirect()->route('dashboard');

          } else {
            
            return back()->with('error','Password is wrong');
          }


      }// end function

    public function changePassword(){
      
    	return view('password.change_password');
    }

    public function postChangePassword(Request $request){
      
    	$validator  = $request->validate([
                    'password' => 'required|string|min:6|confirmed',
                    'password_confirmation' => 'required',
                  ]);

	    $user = Auth::user();

	    $user->password = Hash::make($request->password);

	    $user->save();

	    return back()->with('success', 'Password successfully changed!');
    }

	public function logout(){

	Auth::logout();

	return redirect()->route('login');
	   
	}
}
