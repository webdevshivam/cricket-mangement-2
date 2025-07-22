<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class QrCodeSettingController extends BaseController
{
    public function index()
    {
        $model = new    QrCodeSettingModel();
        $setting = $model->first();

        return view('admin/qr_code_setting/index', ['setting' => $setting]);
    }
    public function save()
    {
        helper('form');
        $model = new QrCodeSettingModel();
        $id = $this->request->getPost('id');

        $data = [
            'upi_handler_name' => $this->request->getPost('upi_handler_name'),
            'upi_number' => $this->request->getPost('upi_number'),
            'upi_id' => $this->request->getPost('upi_id'),
        ];

        if ($this->request->getFile('qr_code')->isValid()) {
            $file = $this->request->getFile('qr_code');
            $fileName = $file->getRandomName();
            $file->move(FCPATH  . 'uploads/qr_codes', $fileName);
            $data['qr_code'] = $fileName;
        }

        if ($id) {
            $model->update($id, $data);
        } else {
            $model->insert($data);
        }

        return redirect()->to(base_url('admin/qr-code-setting'))->with('success', 'QR Code setting updated successfully.');
    }
}
