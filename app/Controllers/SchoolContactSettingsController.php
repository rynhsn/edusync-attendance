<?php

namespace App\Controllers;

use App\Models\SchoolContactSettingsModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SchoolContactSettingsController extends BaseController
{
    public function index()
    {
        return view('panel/school-contact-settings', [
            'title' => 'Pengaturan Kontak Sekolah',
            'activePage' => 'school-contact-settings',
            'settings' => (new SchoolContactSettingsModel())->getSettings(),
            'message' => session()->getFlashdata('contactMessage'),
            'error' => session()->getFlashdata('contactError'),
        ]);
    }

    public function save()
    {
        $rules = [
            'address' => 'required|max_length[2000]',
            'phone' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[254]',
            'hours' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('school-contact-settings'))
                ->withInput()
                ->with('contactError', 'Periksa kembali alamat, telepon, email, dan jam kerja.');
        }

        $values = [];
        foreach (array_keys($rules) as $field) {
            $value = $this->request->getPost($field);
            if (! is_string($value)) {
                return redirect()->to(site_url('school-contact-settings'))
                    ->withInput()
                    ->with('contactError', 'Data kontak yang dikirim tidak valid.');
            }
            $values[$field] = trim($value);
        }

        try {
            if (! (new SchoolContactSettingsModel())->saveSettings($values)) {
                return redirect()->to(site_url('school-contact-settings'))
                    ->withInput()
                    ->with('contactError', 'Pengaturan kontak gagal disimpan. Silakan coba kembali.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save school contact settings: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('school-contact-settings'))
                ->withInput()
                ->with('contactError', 'Pengaturan kontak gagal disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url('school-contact-settings'))
            ->with('contactMessage', 'Informasi kontak sekolah berhasil disimpan.');
    }
}
