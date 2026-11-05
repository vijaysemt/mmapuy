<?php

namespace App\Controllers;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $this->setNoCacheHeaders();

        return view('pages/auth/login');
    }

    public function passwordLogin()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $this->setNoCacheHeaders();

        return view('pages/auth/password_login');
    }

    private function setNoCacheHeaders()
    {
        $this->response->setHeader(
            'Cache-Control',
            'no-store, no-cache, must-revalidate, max-age=0'
        );

        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
    }
}