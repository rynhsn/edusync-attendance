<?php

namespace App\Controllers\Admin;

use App\Models\GalleryModel;

class Gallery extends BaseController
{
    protected $galleryModel;

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
        $this->galleryModel = new GalleryModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Galeri';
        $this->data['items'] = $this->galleryModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->galleryModel->pager;

        return view('admin/gallery/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title']      = 'Tambah Galeri';
        $this->data['item']       = [];
        $this->data['categories'] = $this->galleryModel->getCategories();

        return view('admin/gallery/form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['is_featured'] = $data['is_featured'] ?? 0;
        $data['show'] = $data['show'] ?? 0;

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploaded = $this->uploadAndResizeImage($file);
            if ($uploaded) {
                $data['image'] = $uploaded;
            } else {
                return redirect()->back()->withInput()->with('error', 'File gambar harus berupa gambar (JPG, PNG, atau WEBP).');
            }
        }

        if (!$this->galleryModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan galeri.');
        }

        return redirect()->to('/admin/gallery')->with('success', 'Galeri berhasil ditambahkan.');
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
        $this->data['title'] = 'Edit Galeri';
        $this->data['item']  = $this->galleryModel->find($id);
        $this->data['categories'] = $this->galleryModel->getCategories();

        if (!$this->data['item']) {
            return redirect()->to('/admin/gallery')->with('error', 'Galeri tidak ditemukan.');
        }

        return view('admin/gallery/form', $this->data);
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
        $data['is_featured'] = $data['is_featured'] ?? 0;
        $data['show'] = $data['show'] ?? 0;

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $old = $this->galleryModel->find($id);
            $this->deleteOldImageFile($old['image'] ?? null);

            $uploaded = $this->uploadAndResizeImage($file);
            if ($uploaded) {
                $data['image'] = $uploaded;
            } else {
                return redirect()->back()->withInput()->with('error', 'File gambar harus berupa gambar (JPG, PNG, atau WEBP).');
            }
        }

        if (!$this->galleryModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui galeri.');
        }

        return redirect()->to('/admin/gallery')->with('success', 'Galeri berhasil diperbarui.');
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
        $item = $this->galleryModel->find($id);
        if ($item) {
            $this->deleteOldImageFile($item['image'] ?? null);
        }

        if (!$this->galleryModel->delete($id)) {
            return redirect()->to('/admin/gallery')->with('error', 'Gagal menghapus galeri.');
        }

        return redirect()->to('/admin/gallery')->with('success', 'Galeri berhasil dihapus.');
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

        $this->galleryModel->reorder($ids);

        return $this->response->setJSON(['success' => true, 'csrfHash' => csrf_hash()]);
    }

    /**
     * Upload and resize an image.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     *
     * @return string
     */
    private function uploadAndResizeImage($file)
    {
        $uploadPath = FCPATH . 'uploads/gallery';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $tempPath = $file->getTempName();

        $srcInfo = @getimagesize($tempPath);
        if ($srcInfo === false || !in_array($srcInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return null;
        }

        [$origWidth, $origHeight, $type] = $srcInfo;
        $newName = $this->buildSafeName($type);
        $maxWidth = 1600;

        if ($origWidth > $maxWidth) {
            $ratio = $maxWidth / $origWidth;
            $newWidth  = $maxWidth;
            $newHeight = (int) round($origHeight * $ratio);

            $srcImage = match ($type) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($tempPath),
                IMAGETYPE_PNG  => imagecreatefrompng($tempPath),
                IMAGETYPE_WEBP => imagecreatefromwebp($tempPath),
                default        => null,
            };

            if ($srcImage) {
                $dstImage = imagecreatetruecolor($newWidth, $newHeight);

                if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                    imagealphablending($dstImage, false);
                    imagesavealpha($dstImage, true);
                }

                imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                $destPath = $uploadPath . '/' . $newName;
                match ($type) {
                    IMAGETYPE_JPEG => imagejpeg($dstImage, $destPath, 85),
                    IMAGETYPE_PNG  => imagepng($dstImage, $destPath, 8),
                    IMAGETYPE_WEBP => imagewebp($dstImage, $destPath, 85),
                    default        => null,
                };

                imagedestroy($srcImage);
                imagedestroy($dstImage);
            }
        } else {
            $file->move($uploadPath, $newName);
        }

        return base_url('uploads/gallery/' . $newName);
    }

    /**
     * Delete old image file from disk.
     *
     * @param string|null $imageUrl
     */
    private function deleteOldImageFile(?string $imageUrl): void
    {
        if (empty($imageUrl)) {
            return;
        }

        $baseUrl = rtrim(base_url(), '/') . '/';
        if (str_starts_with($imageUrl, $baseUrl . 'uploads/gallery/')) {
            $localPath = str_replace($baseUrl, '', $imageUrl);
            $file = FCPATH . $localPath;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
