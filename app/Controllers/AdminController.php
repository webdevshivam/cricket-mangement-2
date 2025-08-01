<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    public function index()
    {
        //
    }

    public function dashboard()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        // Load the admin dashboard view with data
        $data = [
            'title' => 'Admin Dashboard',
            'user_name' => session()->get('name')
        ];
        
        return view('admin/dashboard', $data);
    }
}