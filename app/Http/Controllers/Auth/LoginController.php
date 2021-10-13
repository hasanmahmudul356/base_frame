<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function loginForm()
    {
        return view('admin_login');
    }

    public function username()
    {
        return 'username';
    }

    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['status'] = '1';
        return $credentials;

    }

    protected function authenticated(Request $request, $user)
    {
        return redirect('admin/dashboard');
    }
}
