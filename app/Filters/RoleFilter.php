<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $role = (string) session()->get('roleid');
       

        if (!$arguments || !in_array($role, $arguments, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        //
    }
}