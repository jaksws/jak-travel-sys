<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the login attempt to debug why login fails
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        \Log::info('DUSK DEBUG: Attempting login', $credentials);
        $attempt = $this->guard()->attempt(
            $credentials,
            $request->filled('remember')
        );
        \Log::info('DUSK DEBUG: Attempt result', ['result' => $attempt]);
        if (!$attempt) {
            $user = \App\Models\User::where('email', $credentials['email'])->first();
            \Log::info('DUSK DEBUG: User from DB', $user ? $user->toArray() : ['not found']);
        }
        return $attempt;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if user is admin first
        if ($user->role === 'admin' || $user->is_admin == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'agency') {
            return redirect()->route('agency.dashboard');
        } elseif ($user->role === 'subagent') {
            return redirect()->route('subagent.dashboard');
        } elseif ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        }
        
        return redirect($this->redirectPath());
    }
}
