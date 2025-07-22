<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CoachController extends BaseController
{
    public function index()
    {
        //
    }

    public function dashboard()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'coach') {
            return redirect()->to('/unauthorized');
        }

        echo "I am coach";
    }
}
