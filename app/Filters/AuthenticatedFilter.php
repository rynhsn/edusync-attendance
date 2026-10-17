<?php

namespace App\Filters;

use App\Libraries\OnlineUserTracker;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthenticatedFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! is_array(session()->get('authUser'))) {
            return redirect()->to(site_url('login'));
        }

        OnlineUserTracker::touch();

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
