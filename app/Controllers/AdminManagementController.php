<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminManagementController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $data['admins'] = $this->userModel->getAdmins();
        return view('admin/admin_management/index', $data);
    }

    public function create()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        return view('admin/admin_management/create');
    }

    public function store()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'mobile' => 'required|regex_match[/^[0-9]{10}$/]',
            'password' => 'required|min_length[6]|max_length[50]',
            'confirm_password' => 'required|matches[password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if email already exists
        if ($this->userModel->isEmailExists($this->request->getPost('email'))) {
            return redirect()->back()->withInput()->with('error', 'Email already exists');
        }

        // Check if mobile already exists
        if ($this->userModel->isMobileExists($this->request->getPost('mobile'))) {
            return redirect()->back()->withInput()->with('error', 'Mobile number already exists');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'admin'
        ];

        if ($this->userModel->save($data)) {
            return redirect()->to('/admin/manage-admins')->with('success', 'Admin created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create admin');
        }
    }

    public function edit($id)
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $admin = $this->userModel->find($id);
        if (!$admin || $admin['role'] !== 'admin') {
            return redirect()->to('/admin/manage-admins')->with('error', 'Admin not found');
        }

        $data['admin'] = $admin;
        return view('admin/admin_management/edit', $data);
    }

    public function update($id)
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $admin = $this->userModel->find($id);
        if (!$admin || $admin['role'] !== 'admin') {
            return redirect()->to('/admin/manage-admins')->with('error', 'Admin not found');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'mobile' => 'required|regex_match[/^[0-9]{10}$/]'
        ];

        // Only validate password if it's provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]|max_length[50]';
            $rules['confirm_password'] = 'matches[password]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if email already exists (excluding current admin)
        if ($this->userModel->isEmailExists($this->request->getPost('email'), $id)) {
            return redirect()->back()->withInput()->with('error', 'Email already exists');
        }

        // Check if mobile already exists (excluding current admin)
        if ($this->userModel->isMobileExists($this->request->getPost('mobile'), $id)) {
            return redirect()->back()->withInput()->with('error', 'Mobile number already exists');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/manage-admins')->with('success', 'Admin updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update admin');
        }
    }

    public function delete($id)
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $admin = $this->userModel->find($id);
        if (!$admin || $admin['role'] !== 'admin') {
            return redirect()->to('/admin/manage-admins')->with('error', 'Admin not found');
        }

        // Prevent deleting current logged-in admin
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/manage-admins')->with('error', 'You cannot delete your own account');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/manage-admins')->with('success', 'Admin deleted successfully');
        } else {
            return redirect()->to('/admin/manage-admins')->with('error', 'Failed to delete admin');
        }
    }
}
