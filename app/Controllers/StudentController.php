<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class StudentController extends ResourceController
{
    protected $studentModel;
    protected $classModel;

    public function __construct()
    {
        $this->studentModel = new \App\Models\StudentModel();
        $this->classModel = new \App\Models\ClassModel();
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
            'title' => 'Siswa',
            'activePage' => 'student'
        ];
        //get all data
        $data['classes'] = $this->classModel->findAll();

        //return to view setudent/index
        return view('student/index', $data);
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
        $response['data'] = $this->studentModel->getAllStudent();

        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['jk_display'] = JK[$value['jk']];
        }

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
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
        // get data from post
        $data = $this->request->getRawInput();

        // update data
        $this->studentModel->update($id, $data);

        $response = $this->studentModel->find($id);

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
        //
    }
}
