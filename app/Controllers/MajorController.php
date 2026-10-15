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
        $editId = filter_var($this->request->getGet('edit'), FILTER_VALIDATE_INT);
        $editMajor = $editId !== false && $editId > 0 ? $this->majorModel->find($editId) : null;

        return view('panel/majors', [
            'title'      => 'Data Jurusan',
            'activePage' => 'major',
            'majors'     => $this->majorModel->orderBy('jurusan_nama', 'ASC')->findAll(),
            'editMajor'  => $editMajor,
            'message'    => session()->getFlashdata('majorMessage'),
            'error'      => session()->getFlashdata('majorError'),
        ]);
    }

    public function saveForm()
    {
        $id = $this->request->getPost('jurusan_id');
        $name = $this->request->getPost('jurusan_nama');
        $code = $this->request->getPost('jurusan_kode');
        $description = $this->request->getPost('jurusan_keterangan');
        $redirect = redirect()->to(site_url('major'));

        if ((! is_string($id) && $id !== null)
            || ! is_string($name)
            || ! is_string($code)
            || ! is_string($description)) {
            return $redirect->with('majorError', 'Data jurusan tidak valid.');
        }

        $id = trim((string) $id);
        $name = trim($name);
        $code = trim($code);
        $description = trim($description);
        $numericId = $id === '' ? null : filter_var($id, FILTER_VALIDATE_INT);

        if (($id !== '' && ($numericId === false || $numericId < 1 || $this->majorModel->find($numericId) === null))
            || $name === ''
            || mb_strlen($name) > 50
            || $code === ''
            || mb_strlen($code) > 10
            || mb_strlen($description) > 65535) {
            return $redirect->with('majorError', 'Nama wajib diisi (maksimal 50 karakter), kode wajib diisi (maksimal 10 karakter), dan data lainnya harus valid.');
        }

        $data = [
            'jurusan_nama' => $name,
            'jurusan_kode' => $code,
            'jurusan_keterangan' => $description,
        ];
        $saved = $numericId === null
            ? $this->majorModel->insert($data)
            : $this->majorModel->update($numericId, $data);

        if ($saved === false) {
            log_message('error', 'Failed to save major: {errors}', ['errors' => json_encode($this->majorModel->errors())]);

            return $redirect->with('majorError', 'Data jurusan gagal disimpan.');
        }

        return $redirect->with('majorMessage', $numericId === null ? 'Jurusan berhasil ditambahkan.' : 'Jurusan berhasil diperbarui.');
    }

    public function deleteForm(int $id)
    {
        if ($id < 1 || $this->majorModel->find($id) === null) {
            return redirect()->to(site_url('major'))->with('majorError', 'Data jurusan tidak ditemukan.');
        }

        if (! $this->majorModel->delete($id)) {
            return redirect()->to(site_url('major'))->with('majorError', 'Data jurusan gagal dihapus.');
        }

        return redirect()->to(site_url('major'))->with('majorMessage', 'Jurusan berhasil dihapus.');
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
