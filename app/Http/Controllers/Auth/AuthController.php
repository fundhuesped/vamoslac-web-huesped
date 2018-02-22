<?php namespace 
	App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/
	// protected $redirectTo = '/panel';
		protected $redirectTo = '/confirmation-email';

	use AuthenticatesUsers, RegistersUsers {
	    AuthenticatesUsers::redirectPath insteadof RegistersUsers;
	    AuthenticatesUsers::guard insteadof RegistersUsers;
	}
	public function getLogin()
	{
	    if (view()->exists('auth.authenticate')) {
	        return view('auth.authenticate');
	    }

	    return view('auth.login');
	}


	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
		$this->registrar = $auth;

		$this->middleware('guest', ['except' => ['getLogout', 'getRegister', 'postRegister']]);
	}

}
