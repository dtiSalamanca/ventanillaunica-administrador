<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

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

    protected function guard()
    {
        return Auth::guard('ad');
    }
}
