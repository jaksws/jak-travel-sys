<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Fetch only necessary columns for efficiency
        $agencies = \App\Models\Agency::orderBy('name')->get(['id', 'name']);
        return view('auth.register', ['agencies' => $agencies]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:agency,subagent,customer'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
        ];
        // Require license_number if registering as agency
        if (($data['role'] ?? null) === 'agency') {
            $rules['license_number'] = ['required', 'string', 'max:255', 'unique:agencies,license_number'];
        }
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // The defensive check for license_number may be redundant since the validator already enforces it for agency registrations.

        // Create user first
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
        ]);

        // If the user is of agency type, create an agency record
        if ($data['role'] === 'agency') {
            // Defensive check: This should not be necessary if validation is working correctly.
            // However, if this method could be called from somewhere that bypasses validation,
            // or if $data is manipulated after validation, this check prevents inconsistent state.
            // Throwing a ValidationException here is a last-resort safeguard.
            if (empty($data['license_number'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                    'license_number' => e(__('رقم الترخيص مطلوب لتسجيل الوكالة.'))
                ]);
            }
            $agency = Agency::create([
                'name' => $data['name'], // Simplified: Use user\'s name as agency name
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'license_number' => $data['license_number'],
                'status' => 'active',
            ]);
            $user->agency_id = $agency->id;
            $user->save();
        } elseif ($data['role'] === 'subagent') {
            if (!empty($data['agency_id'])) {
                // The validator ensures 'agency_id' exists in the 'agencies' table if provided.
                $user->agency_id = $data['agency_id'];
                $user->save();
            }
        }

        return $user;
    }

    /**
     * Redirect based on user role after registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if ($user->isAgency()) {
            return redirect()->route('agency.dashboard');
        } elseif ($user->isSubagent()) {
            return redirect()->route('subagent.dashboard');
        } elseif ($user->isCustomer()) {
            return redirect()->route('customer.dashboard');
        }
        
        return redirect($this->redirectPath());
    }
}
