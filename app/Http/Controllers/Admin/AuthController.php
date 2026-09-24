<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect(site_admin());
        }

        return view('admin.login', ['google_key' => '6LclA_gUAAAAANkamn1z99Jk7Dbdq5guIZrz5yOm']);
    }

    public function login(Request $request)
    {
        if (! $request->has('btn_login')) {
            return $this->showLogin();
        }

        if (! config('beranda.disable_recaptcha') && ! $this->recaptchaOk($request)) {
            return redirect(site_admin('login'))->with('msg_flash', error_message('Sorry Google Recaptcha Unsuccessful!!'));
        }

        $request->validate([
            'loginName' => 'required',
            'loginPassword' => 'required',
        ]);

        $user = User::where('user_email', $request->input('loginName'))->first();
        $password = (string) $request->input('loginPassword');

        $valid = $user && (
            $user->user_password === $password
            || (str_starts_with((string) $user->user_password, '$2') && Hash::check($password, $user->user_password))
        );

        if (! $valid) {
            return redirect(site_admin('login'))->with('msg_flash', error_message('Username atau katasandi tidak cocok'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect(site_admin());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(site_admin('login'));
    }

    public function showForget()
    {
        return view('admin.forget', ['google_key' => '6LclA_gUAAAAANkamn1z99Jk7Dbdq5guIZrz5yOm']);
    }

    public function forget(Request $request)
    {
        if (! $request->has('btn_forget')) {
            return $this->showForget();
        }

        if (! config('beranda.disable_recaptcha') && ! $this->recaptchaOk($request)) {
            return redirect('forget')->with('msg_flash', error_message('Sorry Google Recaptcha Unsuccessful!!'));
        }

        $email = $request->input('loginEmail');
        $user = User::where('user_email', $email)->first();

        if (! $user) {
            return redirect('forget')->with('msg_flash', error_message('Akun dengan Email ini tidak ditemukan!'));
        }

        // Aplikasi lama mengirim email berisi kredensial. DI Laravel, kirim via Mail.
        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Username : {$user->user_email}\nPassword : {$user->user_password}",
                function ($m) use ($user) {
                    $m->to($user->user_email)->subject('Info Login Anda');
                }
            );
        } catch (\Throwable $e) {
            return redirect('forget')->with('msg_flash', error_message('Email gagal dikirim, silahkan coba kembali beberapa saat!'));
        }

        return redirect('forget')->with('msg_flash', success_message('Email berhasil dikirim, Silahkan cek email anda.'));
    }

    private function recaptchaOk(Request $request): bool
    {
        $secret = config('beranda.google_recaptcha_secret');
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$request->input('g-recaptcha-response').'&remoteip='.$request->ip());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return ! empty(json_decode($output, true)['success']);
    }
}
