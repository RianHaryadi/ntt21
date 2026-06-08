<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterForm extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        if (request()->has('redirect')) {
            session(['login_redirect' => request()->query('redirect')]);
        }
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        session()->flash('message', 'Registrasi berhasil dan Anda telah masuk!');

        // Check if we should redirect back to where they came from
        $default = '/dashboard';
        $redirectTo = session('login_redirect') ?? $default;
        session()->forget('login_redirect');

        return redirect()->to($redirectTo);
    }

    public function render()
    {
        return view('livewire.register-form')->layout('layouts.Login');
    }
}
