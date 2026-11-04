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
}