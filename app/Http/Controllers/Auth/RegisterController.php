<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

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
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                // Verify reCAPTCHA response with Google
                $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
                ]);
                $responseBody = $response->json();
                logger()->info('reCAPTCHA response', $responseBody);
                if (!isset($responseBody['success']) || !$responseBody['success']) {
                    $fail('The reCAPTCHA verification failed.');
                }
            }],
        ], [
            'g-recaptcha-response.required' => 'reCAPTCHA verification is required.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' =>  $data['first_name'] . ' ' . $data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        Mail::to($user->email)->send(new WelcomeEmail($user));
        return $user;
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->route('front.home')->with(['error' => $firstError, 'register_check' => 'register'])->withErrors($validator);
        }

        $user = $this->create($request->all());
        // manual user login
        Auth::login($user);
        // Redirect the user to the desired page with a success message
        return redirect()->route('front.home')->with('message', 'You are successfully registered and logged in.');
    }
}
