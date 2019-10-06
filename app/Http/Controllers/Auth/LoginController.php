<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Session;
use App\Model\User;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/app';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected function authenticated(Request $request,$user){
        $user->employee;
        //$request->session()->put('auth_user',$user);
    }

    protected function loggedOut(Request $request)
    {
        //
       //$request->session()->forget('auth_user');
    }

    protected function redirectTo(){
        return '/app';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'email';
    }

    public function showLoginForm()
    {
        return view('login');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials=$this->credentials($request);

        $loggedIn=\Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password']],true);

        return $loggedIn;


    }

    public function auth_user(Request $request){
        return auth_user($request);
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }


}
