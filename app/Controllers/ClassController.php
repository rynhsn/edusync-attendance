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
        $editId = filter_var($this->request->getGet('edit'), FILTER_VALIDATE_INT);
        $editClass = $editId !== false && $editId > 0 ? $this->classModel->find($editId) : null;

        return view('panel/classes', [
            'title'      => 'Data Kelas',
            'activePage' => 'class',
            'classes'    => $this->classModel->orderBy('kelas_tingkat', 'ASC')->orderBy('kelas_nama', 'ASC')->findAll(),
            'editClass'  => $editClass,
            'masterDataRoute' => 'class',
            'canImportMasterData' => \App\Libraries\ActionAccess::can('class.import'),
            'canExportMasterData' => \App\Libraries\ActionAccess::can('class.export'),
            'message'    => session()->getFlashdata('classMessage'),
            'error'      => session()->getFlashdata('classError'),
        ]);
    }

    public function saveForm()
    {
        $id = $this->request->getPost('kelas_id');
        $grade = $this->request->getPost('kelas_tingkat');
        $name = $this->request->getPost('kelas_nama');
        $description = $this->request->getPost('kelas_keterangan');
        $redirect = redirect()->to(site_url('class'));

        if ((! is_string($id) && $id !== null)
            || (! is_string($grade) && ! is_int($grade))
            || ! is_string($name)
            || ! is_string($description)) {
            return $redirect->with('classError', 'Data kelas tidak valid.');
        }

        $id = trim((string) $id);
        $grade = filter_var($grade, FILTER_VALIDATE_INT);
        $name = trim($name);
        $description = trim($description);
        $numericId = $id === '' ? null : filter_var($id, FILTER_VALIDATE_INT);

        if (($id !== '' && ($numericId === false || $numericId < 1 || $this->classModel->find($numericId) === null))
            || $grade === false
            || $grade < 1
            || $grade > 12
            || $name === ''
            || mb_strlen($name) > 50
            || mb_strlen($description) > 65535) {
            return $redirect->with('classError', 'Tingkat harus antara 1 sampai 12 dan nama kelas wajib diisi (maksimal 50 karakter).');
        }

        $data = [
            'kelas_tingkat' => $grade,
            'kelas_nama' => $name,
            'kelas_keterangan' => $description,
        ];

        if ($numericId !== null) {
            if (! $this->classModel->update($numericId, $data)) {
                return $redirect->with('classError', 'Data kelas gagal diperbarui.');
            }

            return $redirect->with('classMessage', 'Kelas berhasil diperbarui.');
        }

        $db = db_connect();
        $db->transStart();
        $newId = $this->classModel->insert($data, true);
        if ($newId !== false) {
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $timeSettings = [];
            foreach ($days as $day) {
                $timeSettings[] = [
                    'kelas_id' => $newId,
                    'hari' => $day,
                    'jam_masuk' => '07:00:00',
                    'jam_pulang' => '14:00:00',
                ];
            }
            $this->timeSettingModel->insertBatch($timeSettings);
        }
        $db->transComplete();

        if ($newId === false || $db->transStatus() === false) {
            log_message('error', 'Failed to create class and default time settings.');

            return $redirect->with('classError', 'Data kelas gagal disimpan.');
        }

        return $redirect->with('classMessage', 'Kelas berhasil ditambahkan.');
    }

    public function deleteForm(int $id)
    {
        $redirect = redirect()->to(site_url('class'));
        $class = $id > 0 ? $this->classModel->find($id) : null;
        if ($class === null) {
            return $redirect->with('classError', 'Data kelas tidak ditemukan.');
        }

        $db = db_connect();
        $isUsedByStudents = $db->table('master_siswa')->where('kelas_id', $id)->countAllResults() > 0;
        $isUsedByAttendance = $db->table('absensi_absensi_kelas')->where('kelas_id', $id)->countAllResults() > 0;
        if ($isUsedByStudents || $isUsedByAttendance) {
            return $redirect->with('classError', 'Kelas tidak dapat dihapus karena masih digunakan oleh siswa atau riwayat absensi.');
        }

        $db->transStart();
        $this->timeSettingModel->where('kelas_id', $id)->delete();
        $this->classModel->delete($id);
        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Failed to delete class {id} and its time settings.', ['id' => $id]);

            return $redirect->with('classError', 'Data kelas gagal dihapus.');
        }

        return $redirect->with('classMessage', 'Kelas berhasil dihapus.');
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
