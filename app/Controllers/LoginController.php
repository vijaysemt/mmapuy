<?php

namespace App\Controllers;

class LoginController extends BaseController
{
    public function index(): string
    {
        
        return view('pages/auth/login');
    }

    public function passwordLogin(): string
    {
        
        return view('pages/auth/password_login');
    }

     // Show OTP screen after mobile and role are submitted
    public function showOtp()
    {
        $mobile = $this->request->getPost('mobile');

        if (empty($mobile)) {
            return redirect()->to('/login');
        }

        session()->set('mob', $mobile);

        return redirect()->to('/mobile-otp');
    }

    // Display OTP screen
    public function mobileOtp()
    {
        return view('pages/mobile-otp', [
            'mob' => session()->get('mob')
        ]);
    }

    // Demo OTP submission - no actual OTP verification
    public function demoVerifyOtp()
    {
        return redirect()->to('/dashboard');
    }

    // Demo password login - no actual password verification
    public function demoPasswordLogin()
    {
        return redirect()->to('/dashboard');
    }

    // Display dashboard
    public function dashboard()
    {
        return view('pages/dashboard');
    }
}