<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController as AppBaseController;
use App\Models\SettingModel;

abstract class BaseController extends AppBaseController
{
    protected $data = [];

    /**
     * Initialize controller, check authentication, and load shared data.
     *
     * @param \CodeIgniter\HTTP\RequestInterface  $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface            $logger
     *
     * @return void
     */
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);

        helper('text');

        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu.');
            redirect()->to('/login')->send();
            exit;
        }

        $settingModel = new SettingModel();
        $this->data['site_name'] = $settingModel->get('site_name') ?? 'SekolahKu';

        $this->data['user'] = [
            'userId'   => session()->get('userId'),
            'username' => session()->get('username'),
            'fullName' => session()->get('fullName'),
            'role'     => session()->get('role'),
        ];
    }

    /**
     * Build a random file name whose extension is derived from the detected
     * image type, never from the client-supplied name.
     *
     * @param int $type An IMAGETYPE_* constant.
     *
     * @return string
     */
    protected function buildSafeName(int $type): string
    {
        $ext = match ($type) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
            default        => 'img',
        };

        return time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    }
}
