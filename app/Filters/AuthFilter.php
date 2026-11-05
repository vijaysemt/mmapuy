<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please login first.');
        }

        if ($session->get('must_set_password')) {
            $path = trim(
                service('uri')->getPath(),
                '/'
            );

            if ($path !== 'auth/set-password') {
                return redirect()->to(
                    site_url('auth/set-password')
                );
            }
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No action required.
    }
}
