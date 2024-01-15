<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function connct_db(){
        try {
            DB::connection()->getPdo();
    
            echo 'Sudah terkoneksi dengan database: ' . DB::connection()->getDatabaseName();
    
        } catch (\Exception $e) {
            echo 'Belum terkoneksi database, error: ' . $e->getMessage();
        }
    }
    public function login(){
        return view('auth.login');
    }

    public function authenticated(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credential = $request->only('email', 'password');

        if(Auth::attempt($credential)){
            $request->session()->regenerate();

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'LoginError' => 'Email atau Password salah'
        ]);
    }

    public function logout(){
        Auth::logout();

        return redirect('/login');
    }
}
