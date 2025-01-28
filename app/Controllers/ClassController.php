<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ClassController extends ResourceController
{
    protected $classModel;
    protected $timeSettingModel;

    //construct
    public function __construct()
    {
        $this->classModel = new \App\Models\ClassModel();
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
            'title' => 'Kelas',
            'activePage' => 'class'
        ];

        //return to view class/index
        return view('class/index', $data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //get all data
        $response['data'] = $this->classModel->findAll();

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    { //read data from post
        $data = $this->request->getPost();

        //insert data
        $this->classModel->insert($data);

        //response adalah data yang baru saja di insert untuk ditampilkan create at dan update at
        $response = $this->classModel->find($this->classModel->insertID());

        // tambahkan data ke model konfigurasi waktu dengan jam default, gunakan array dulu untuk menyimpan list data
        $timeSettingModel = new \App\Models\TimeSettingModel();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSettingData = [];
        foreach ($days as $day) {
            $timeSettingData[] = [
                'kelas_id' => $response['kelas_id'],
                'hari' => $day,
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '14:00:00',
            ];
        }
        $this->timeSettingModel->insertBatch($timeSettingData);

        return $this->response->setStatusCode(200)->setJSON($response);
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
        //get data from post
        $data = $this->request->getRawInput();

        //update data
        $this->classModel->update($id, $data);

        //response adalah data yang baru saja di update untuk ditampilkan create at dan update at
        $response = $this->classModel->find($id);

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //hapus dulu data konfigurasi waktu
        $this->timeSettingModel->where('kelas_id', $id)->delete();

        //delete data
        $this->classModel->delete($id);

        //response adalah data yang baru saja di delete
        $response = $this->classModel->find($id);

        return $this->response->setStatusCode(200)->setJSON($response);
    }
}
