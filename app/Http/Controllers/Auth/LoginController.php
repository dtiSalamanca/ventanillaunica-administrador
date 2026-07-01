<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest:ad')->except('logout');
        $this->middleware('auth:ad')->only('logout');
    }

    public function username(): string
    {
        return 'username';
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if (config('services.recaptcha_v3.enabled')) {
            $rules['recaptcha_token'] = 'required|string';
        }

        $request->validate($rules);

        if (config('services.recaptcha_v3.enabled')) {
            $this->verifyRecaptcha($request->recaptcha_token);
        }
    }

    protected function guard()
    {
        return Auth::guard('ad');
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $user = $this->guard()->user();

        if ($user) {
            Cache::forget("ad_user_{$user->getAuthIdentifier()}");
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    protected function verifyRecaptcha(string $token): void
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha_v3.secret_key'),
            'response' => $token,
        ]);

        $body = $response->json();

        if (! ($body['success'] ?? false)) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['La validación de seguridad falló. Intente nuevamente.'],
            ]);
        }

        $expectedAction = config('services.recaptcha_v3.action', 'login');

        if (($body['action'] ?? '') !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['La validación de seguridad falló. Intente nuevamente.'],
            ]);
        }

        $score = $body['score'] ?? 0;
        $threshold = (float) config('services.recaptcha_v3.score_threshold', 0.5);

        if ($score < $threshold) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['La validación de seguridad falló. Intente nuevamente.'],
            ]);
        }
    }
}
