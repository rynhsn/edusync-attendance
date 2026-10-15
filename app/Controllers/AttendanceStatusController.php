<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class AttendanceStatusController extends ResourceController
{
    protected $attendanceStatusModel;
    protected $absenceStatusModel;

    public function __construct()
    {
        $this->attendanceStatusModel = new \App\Models\AttendanceStatusModel();
        $this->absenceStatusModel = new \App\Models\AbsenceStatusModel();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $editType = $this->request->getGet('edit_type');
        $editId = filter_var($this->request->getGet('edit_id'), FILTER_VALIDATE_INT);
        $editStatus = null;
        if (is_string($editType) && $editId !== false && $editId > 0) {
            $model = $this->modelFor($editType);
            $editStatus = $model?->find($editId);
        }

        $data = [
            'title' => 'Status',
            'activePage' => 'attendance-status',
            'attendanceStatus' => $this->attendanceStatusModel->findAll(),
            'absenceStatus' => $this->absenceStatusModel->findAll(),
            'editType' => is_string($editType) ? $editType : '',
            'editStatus' => $editStatus,
            'message' => session()->getFlashdata('statusMessage'),
            'error' => session()->getFlashdata('statusError'),
        ];

        return view('attendance-status/index', $data);
    }

    public function save()
    {
        $type = $this->request->getPost('type');
        $config = is_string($type) ? $this->statusConfig($type) : null;
        $redirect = redirect()->to(site_url('attendance-status'));
        if ($config === null) {
            return $redirect->with('statusError', 'Jenis status tidak valid.');
        }

        $rawId = $this->request->getPost('id');
        $rawName = $this->request->getPost($config['name']);
        $rawColor = $this->request->getPost($config['color']);
        $rawDescription = $this->request->getPost($config['description']);
        if ((! is_string($rawId) && $rawId !== null)
            || ! is_string($rawName)
            || ! is_string($rawColor)
            || ! is_string($rawDescription)) {
            return $redirect->with('statusError', 'Data status tidak valid.');
        }

        $id = trim((string) $rawId);
        $name = trim($rawName);
        $color = trim($rawColor);
        $description = trim($rawDescription);
        $numericId = $id === '' ? null : filter_var($id, FILTER_VALIDATE_INT);
        $model = $config['model'];
        $allowedColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        if (($id !== '' && ($numericId === false || $numericId < 1 || $model->find($numericId) === null))
            || $name === ''
            || mb_strlen($name) > 20
            || ! in_array($color, $allowedColors, true)
            || mb_strlen($description) > 65535) {
            return $redirect->with('statusError', 'Nama status wajib diisi (maksimal 20 karakter), warna harus valid, dan keterangan tidak boleh terlalu panjang.');
        }

        $data = [
            $config['name'] => $name,
            $config['color'] => $color,
            $config['description'] => $description,
        ];
        $saved = $numericId === null
            ? $model->insert($data)
            : $model->update($numericId, $data);
        if ($saved === false) {
            log_message('error', 'Failed to save {type} status: {errors}', [
                'type' => $type,
                'errors' => json_encode($model->errors()),
            ]);

            return $redirect->with('statusError', 'Status gagal disimpan.');
        }

        return $redirect->with('statusMessage', $numericId === null ? 'Status berhasil ditambahkan.' : 'Status berhasil diperbarui.');
    }

    public function deleteStatus(string $type, int $id)
    {
        $redirect = redirect()->to(site_url('attendance-status'));
        $config = $this->statusConfig($type);
        if ($config === null || $id < 1) {
            return $redirect->with('statusError', 'Jenis atau data status tidak valid.');
        }

        $status = $config['model']->find($id);
        if ($status === null) {
            return $redirect->with('statusError', 'Status yang akan dihapus tidak ditemukan.');
        }

        $referenceCount = 0;
        foreach ($config['references'] as $reference) {
            $referenceCount += db_connect()->table('absensi_absensi_sekolah')
                ->where($reference, $id)
                ->countAllResults();
        }
        if ($referenceCount > 0) {
            return $redirect->with('statusError', 'Status tidak dapat dihapus karena sudah digunakan pada riwayat absensi.');
        }

        if (! $config['model']->delete($id)) {
            log_message('error', 'Failed to delete {type} status {id}.', ['type' => $type, 'id' => $id]);

            return $redirect->with('statusError', 'Status gagal dihapus.');
        }

        return $redirect->with('statusMessage', 'Status berhasil dihapus.');
    }

    private function modelFor(string $type): ?\CodeIgniter\Model
    {
        return match ($type) {
            'rfid' => $this->absenceStatusModel,
            'attendance' => $this->attendanceStatusModel,
            default => null,
        };
    }

    private function statusConfig(string $type): ?array
    {
        return match ($type) {
            'rfid' => [
                'model' => $this->absenceStatusModel,
                'name' => 'status_absensi',
                'color' => 'status_absensi_warna',
                'description' => 'status_absensi_keterangan',
                'references' => ['status_absensi_masuk_id', 'status_absensi_pulang_id'],
            ],
            'attendance' => [
                'model' => $this->attendanceStatusModel,
                'name' => 'status_kehadiran',
                'color' => 'status_kehadiran_warna',
                'description' => 'status_kehadiran_keterangan',
                'references' => ['status_kehadiran_id'],
            ],
            default => null,
        };
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
        //
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
        //
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
