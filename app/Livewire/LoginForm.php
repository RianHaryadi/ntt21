<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginForm extends Component
{
    public $email = '';
    public $password = '';

    public function mount()
    {
        if (request()->has('redirect')) {
            session(['login_redirect' => request()->query('redirect')]);
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('message', 'Login berhasil!');

            $default = Auth::user()->is_admin ?? false ? '/admin' : '/dashboard';

            // Cek login_redirect (dari ?redirect= param) atau url.intended (dari auth middleware)
            if (session()->has('login_redirect')) {
                $redirectTo = session()->pull('login_redirect');
                return redirect()->to($redirectTo);
            }

            return redirect()->intended($default);
        } else {
            session()->flash('error', 'Email atau password salah.');
        }
    }

    public function render()
    {
        return view('livewire.login-Form')->layout('layouts.Login');
    }
}

