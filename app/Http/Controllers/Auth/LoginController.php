<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ohffs\Ldap\LdapFacade;

class LoginController extends Controller
{
    public function show()
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $credentials['username'])->first();
        if (! $user) {
            return $this->failedLoginResponse();
        }

        if (config('ldap.use_ldap_auth', true)) {
            $ldapUser = \Ohffs\Ldap\LdapFacade::authenticate($credentials['username'], $credentials['password']);
            if (! $ldapUser) {
                return $this->failedLoginResponse();
            }
        }

        auth()->login($user, $request->boolean('remember-me'));
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function destroy()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function failedLoginResponse()
    {
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
}
