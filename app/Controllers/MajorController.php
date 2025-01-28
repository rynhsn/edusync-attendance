<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class MajorController extends ResourceController
{
    protected $majorModel;

    //construct
    public function __construct()
    {
        $this->majorModel = new \App\Models\MajorModel();
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
            'title' => 'Jurusan',
            'activePage' => 'major'
        ];

        //return to view major/index
        return view('major/index', $data);
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
        $response['data'] = $this->majorModel->findAll();

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
        $this->majorModel->insert($data);

        //response adalah data yang baru saja di insert untuk ditampilkan create at dan update at
        $response = $this->majorModel->find($this->majorModel->insertID());

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
        $this->majorModel->update($id, $data);

        //response adalah data yang baru saja di update untuk ditampilkan create at dan update at
        $response = $this->majorModel->find($id);

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
        //delete data
        $this->majorModel->delete($id);

        //response adalah data yang baru saja di delete
        $response = $this->majorModel->find($id);

        return $this->response->setStatusCode(200)->setJSON($response);
    }
}
