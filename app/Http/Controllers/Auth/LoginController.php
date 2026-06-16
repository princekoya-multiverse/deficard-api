<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
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
    public function login(Request $request)
    {
        $input = $request->all();
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->route('front.home')->with(['error' => $firstError, 'login_check' => 'login'])->withErrors($validator);
        }

        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password']))) {
            if (auth()->user()->is_admin) {
                return redirect()->route('admin.dashboard')->with('message', 'You are Successfuly Logged In.');
            } else {
                return redirect()->route('front.ne_card', ['step' => 5])->with('message', 'You are Successfuly Logged In.');
            }
        } else {
            return redirect()->route('front.home')
                ->with(['error' => 'Email-Address and password did not match.', 'login_check' => 'login']);
        }
    }
}
