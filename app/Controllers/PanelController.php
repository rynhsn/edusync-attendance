<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PanelController extends BaseController
{
    public function index()
    {
        //title
        $data = [
            'title' => 'Panel', 
            'activePage' => 'panel'
        ];
        //return to view panel/index
        return view('index', $data);
    }
}
