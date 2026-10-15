<?php

namespace App\Controllers\Admin;

use App\Models\TeacherModel;

class Teachers extends BaseController
{
    protected $teacherModel;

    /**
     * Initialize controller.
     *
     * @param \CodeIgniter\HTTP\RequestInterface  $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface            $logger
     */
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        $this->teacherModel = new TeacherModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Guru & Tenaga Pendidik';
        $this->data['items'] = $this->teacherModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->teacherModel->pager;

        return view('admin/teachers/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Guru';
        $this->data['item']  = [];
        return view('admin/teachers/form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['show'] = $data['show'] ?? 0;

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploaded = $this->uploadAndResizePhoto($file);
            if ($uploaded) {
                $data['photo'] = $uploaded;
            } else {
                return redirect()->back()->withInput()->with('error', 'File foto harus berupa gambar (JPG, PNG, atau WEBP).');
            }
        }

        if (!$this->teacherModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data guru.');
        }

        return redirect()->to('/admin/teachers')->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        $this->data['title'] = 'Edit Guru';
        $this->data['item']  = $this->teacherModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/teachers')->with('error', 'Data guru tidak ditemukan.');
        }

        return view('admin/teachers/form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $data = $this->request->getPost();
        $data['show'] = $data['show'] ?? 0;

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $old = $this->teacherModel->find($id);
            if (!empty($old['photo']) && !preg_match('#^https?://#', $old['photo'])) {
                $oldFile = FCPATH . 'uploads/teachers/' . $old['photo'];
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $uploaded = $this->uploadAndResizePhoto($file);
            if ($uploaded) {
                $data['photo'] = $uploaded;
            } else {
                return redirect()->back()->withInput()->with('error', 'File foto harus berupa gambar (JPG, PNG, atau WEBP).');
            }
        }

        if (!$this->teacherModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data guru.');
        }

        return redirect()->to('/admin/teachers')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Reorder resources via drag-and-drop.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function reorder()
    {
        $input = json_decode($this->request->getBody(), true);
        $ids = $input['ids'] ?? [];

        if (empty($ids)) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->teacherModel->reorder($ids);

        return $this->response->setJSON(['success' => true, 'csrfHash' => csrf_hash()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        $teacher = $this->teacherModel->find($id);
        if ($teacher && !empty($teacher['photo']) && !preg_match('#^https?://#', $teacher['photo'])) {
            $file = FCPATH . 'uploads/teachers/' . $teacher['photo'];
            if (is_file($file)) {
                unlink($file);
            }
        }

        if (!$this->teacherModel->delete($id)) {
            return redirect()->to('/admin/teachers')->with('error', 'Gagal menghapus data guru.');
        }

        return redirect()->to('/admin/teachers')->with('success', 'Data guru berhasil dihapus.');
    }

    /**
     * Upload and resize a photo.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     *
     * @return string
     */
    private function uploadAndResizePhoto($file): string
    {
        $uploadPath = FCPATH . 'uploads/teachers';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $srcPath = $file->getTempName();

        $srcInfo = @getimagesize($srcPath);
        if ($srcInfo === false || !in_array($srcInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return '';
        }

        [$srcWidth, $srcHeight, $type] = $srcInfo;
        $newName = $this->buildSafeName($type);
        $maxWidth = 400;

        if ($srcWidth > $maxWidth) {
            $newHeight = (int) round($srcHeight * ($maxWidth / $srcWidth));
            $dst = imagecreatetruecolor($maxWidth, $newHeight);

            $src = match ($type) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($srcPath),
                IMAGETYPE_PNG  => imagecreatefrompng($srcPath),
                IMAGETYPE_WEBP => imagecreatefromwebp($srcPath),
                default        => null,
            };

            if (!$src) {
                $file->move($uploadPath, $newName);
                return $newName;
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $newHeight, $srcWidth, $srcHeight);

            $destPath = $uploadPath . '/' . $newName;
            match ($type) {
                IMAGETYPE_JPEG => imagejpeg($dst, $destPath, 85),
                IMAGETYPE_PNG  => imagepng($dst, $destPath, 8),
                IMAGETYPE_WEBP => imagewebp($dst, $destPath, 85),
                default        => $file->move($uploadPath, $newName),
            };

            imagedestroy($src);
            imagedestroy($dst);
        } else {
            $file->move($uploadPath, $newName);
        }

        return $newName;
    }
}
