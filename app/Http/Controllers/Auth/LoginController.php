<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Traits\ActivityTraits;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    use ActivityTraits;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username=$this->findUsername();
    }

    public function findUsername()
    {
        $login=request()->input('login');
        $fieldType=filter_var($login,FILTER_VALIDATE_EMAIL)?'email':'username';
        request()->merge([$fieldType=>$login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['status_aktif' => 1]);
    }

    public function authenticated(Request $request, $user)
    {
        $this->logLoginDetails($user);

        // dd($user);
        // if(!$user->verified)
        // {
        //     auth()->logout();
        //     return back()->with('warning','You need to confirm your account. We have sent you an activation code, please check your email.');
        // }
        // if(setting('language_setting')!==null)
        // {
        //     Session::put('locale', setting('language_setting'));
        // }
        $pesan='';
        $pesan.='Pengguna '.strtoupper(strtolower (Auth::user()->name)).'';
        $pesan.='<br>Anda melakukan login ke sistem pada '.date('d-m-Y H:i:s');

        message(true,$pesan,'');
        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        $this->logLogoutDetails(Auth::user());

        $this->guard()->logout();
        
        $request->session()->invalidate();

        

        return $this->loggedOut($request) ?: redirect('/home');
    }
    
}
