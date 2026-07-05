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
    public $referral_code = '';

    public function mount()
    {
        if (request()->has('redirect')) {
            session(['login_redirect' => request()->query('redirect')]);
        }

        if (request()->has('ref')) {
            $this->referral_code = strtoupper(request()->query('ref'));
        }
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'referral_code' => 'nullable|string|max:20',
        ]);

        $referrer = null;
        if (!empty($this->referral_code)) {
            $referrer = User::where('referral_code', strtoupper($this->referral_code))->first();
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'referred_by_id' => $referrer?->id,
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
