<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ManagerController extends BaseController
{
    public function index()
    {
        //
    }
    public function dashboard()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'manager') {
            return redirect()->to('/unauthorized');
        }

        echo "I am manager";
    }
}
