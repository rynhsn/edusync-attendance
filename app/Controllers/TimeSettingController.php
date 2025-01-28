<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class TimeSettingController extends ResourceController
{
    protected $timeSettingModel;

    //construct
    public function __construct()
    {
        $this->timeSettingModel = new \App\Models\TimeSettingModel();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //title
        $data = [
            'title' => 'Konfigurasi Waktu',
            'activePage' => 'timesetting'
        ];

        $data['timeSettings'] = $this->timeSettingModel->getTimeSettingsWithClassName();

        $matrixData = [];
        foreach ($data['timeSettings'] as $config) {
            //kelas id untuk identifikasi edit
            $matrixData[$config['kelas_id']]['kelas_id'] = $config['kelas_id'];

            $matrixData[$config['kelas_id']]['kelas_nama'] = $config['kelas_nama'];
            $matrixData[$config['kelas_id']][$config['hari']] = [
                // format jam masuk dan jam pulang HH:MM
                'jam_masuk' => date('H:i', strtotime($config['jam_masuk'])),
                'jam_pulang' => date('H:i', strtotime($config['jam_pulang'])),
            ];
        }

        $data['matrixData'] = $matrixData;
        // dd($data['matrixData']);

        //return to view time-setting/index
        return view('time-setting/index', $data);
    }

    public function show($id = null)
    {
        //get all data
        $timeSettings = $this->timeSettingModel->getTimeSettingsWithClassName();

        $matrixData = [];
        foreach ($timeSettings as $config) {
            //kelas id untuk identifikasi edit
            $matrixData[$config['kelas_id']]['kelas_id'] = $config['kelas_id'];
            $matrixData[$config['kelas_id']]['kelas_nama'] = $config['kelas_nama'];
            $matrixData[$config['kelas_id']][$config['hari']] = [
                //konfigurasi_waktu_id untuk identifikasi edit
                'konfigurasi_waktu_id' => $config['konfigurasi_waktu_id'],
                // format jam masuk dan jam pulang HH:MM
                'jam_masuk' => date('H:i', strtotime($config['jam_masuk'])),
                'jam_pulang' => date('H:i', strtotime($config['jam_pulang'])),
            ];
        }

        return $this->response->setStatusCode(200)->setJSON(array_values($matrixData));
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //try
        try {
            //get data from post
            $data = $this->request->getRawInput();

            //update data
            $this->timeSettingModel->update($id, $data);
        } catch (\Exception $e) {
            //return error 500
            // return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $data]);
        }
        //return success 200
        return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Data berhasil diubah']);
    }
}
