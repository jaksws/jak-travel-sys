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
            if (empty($data['license_number'])) {
                // Defensive: throw a validation exception if license_number is missing
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'license_number' => __('رقم الترخيص مطلوب لتسجيل الوكالة.')
                ]);
            }
            $agency = Agency::create([
                'name' => $data['agency_name'] ?? $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'license_number' => $data['license_number'],
                'status' => 'active',
            ]);
            $user->agency_id = $agency->id;
            $user->save();
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
