<?php

namespace App\Controllers;

use App\Models\AuthUserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends Controller
{
    private const MAX_PHOTO_SIZE = 2097152;

    public function index()
    {
        $user = $this->currentUser();
        if ($user === null) {
            return redirect()->to(site_url('login'));
        }

        return view('panel/profile', [
            'title'      => 'Profil Saya',
            'activePage' => 'profile',
            'user'       => $user,
            'message'    => session()->getFlashdata('profileMessage'),
            'error'      => session()->getFlashdata('profileError'),
            'passwordMessage' => session()->getFlashdata('profilePasswordMessage'),
            'passwordError'   => session()->getFlashdata('profilePasswordError'),
        ]);
    }

    public function update()
    {
        $user = $this->currentUser();
        if ($user === null) {
            return redirect()->to(site_url('login'));
        }

        $displayName = trim((string) $this->request->getPost('display_name'));
        $username    = strtolower(trim((string) $this->request->getPost('username')));

        if ($displayName === '' || mb_strlen($displayName) > 100) {
            return redirect()->to(site_url('profile'))
                ->with('profileError', 'Nama wajib diisi dan maksimal 100 karakter.');
        }

        if (preg_match('/^[a-z0-9._-]{3,32}$/', $username) !== 1) {
            return redirect()->to(site_url('profile'))
                ->with('profileError', 'Username harus 3–32 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.');
        }

        $users = new AuthUserModel();
        $usernameOwner = $users->where('username', $username)->first();
        if ($usernameOwner !== null && (int) $usernameOwner['user_id'] !== (int) $user['user_id']) {
            return redirect()->to(site_url('profile'))
                ->with('profileError', 'Username sudah digunakan oleh akun lain.');
        }

        $photo = $this->request->getFile('photo');
        $newPhotoName = null;

        if ($photo !== null && $photo->getError() !== UPLOAD_ERR_NO_FILE) {
            $validationError = $this->validatePhoto($photo);
            if ($validationError !== null) {
                return redirect()->to(site_url('profile'))->with('profileError', $validationError);
            }

            $extension = match ($photo->getMimeType()) {
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                default      => throw new \LogicException('Validated profile photo has an unsupported MIME type.'),
            };
            $newPhotoName = bin2hex(random_bytes(16)) . '.' . $extension;
            $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles';

            if (! is_dir($uploadPath) && ! mkdir($uploadPath, 0750, true) && ! is_dir($uploadPath)) {
                log_message('error', 'Could not create the profile photo upload directory.');
                return redirect()->to(site_url('profile'))
                    ->with('profileError', 'Folder penyimpanan foto gagal disiapkan.');
            }

            try {
                $photo->move($uploadPath, $newPhotoName);
            } catch (\RuntimeException $exception) {
                log_message('error', 'Failed to store an uploaded profile photo: {message}', [
                    'message' => $exception->getMessage(),
                ]);
                return redirect()->to(site_url('profile'))
                    ->with('profileError', 'Foto profil gagal disimpan.');
            }
        }

        $update = [
            'display_name' => $displayName,
            'username'     => $username,
        ];
        if ($newPhotoName !== null) {
            $update['avatar_path'] = $newPhotoName;
        }

        if (! $users->update($user['user_id'], $update)) {
            if ($newPhotoName !== null) {
                $this->deletePhoto($newPhotoName);
            }

            log_message('error', 'Failed to save EduSync profile changes.');
            return redirect()->to(site_url('profile'))
                ->with('profileError', 'Perubahan profil gagal disimpan. Silakan coba kembali.');
        }

        if ($newPhotoName !== null && ! empty($user['avatar_path'])) {
            $this->deletePhoto($user['avatar_path']);
        }

        $authUser = session()->get('authUser');
        $authUser['display_name'] = $displayName;
        if ($newPhotoName !== null) {
            $authUser['avatar_path'] = $newPhotoName;
        }
        session()->set('authUser', $authUser);

        return redirect()->to(site_url('profile'))
            ->with('profileMessage', 'Profil berhasil diperbarui.');
    }

    public function changePassword()
    {
        $user = $this->currentUser();
        if ($user === null) {
            return redirect()->to(site_url('login'));
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmation = $this->request->getPost('new_password_confirm');

        if (! is_string($currentPassword) || ! is_string($user['password_hash'])
            || ! password_verify($currentPassword, $user['password_hash'])) {
            return redirect()->to(site_url('profile'))
                ->with('profilePasswordError', 'Password saat ini tidak sesuai.');
        }

        if (! is_string($newPassword) || strlen($newPassword) < 8 || strlen($newPassword) > 72) {
            return redirect()->to(site_url('profile'))
                ->with('profilePasswordError', 'Password baru harus terdiri dari 8–72 karakter.');
        }

        if (! is_string($confirmation) || ! hash_equals($newPassword, $confirmation)) {
            return redirect()->to(site_url('profile'))
                ->with('profilePasswordError', 'Konfirmasi password baru tidak cocok.');
        }

        if (password_verify($newPassword, $user['password_hash'])) {
            return redirect()->to(site_url('profile'))
                ->with('profilePasswordError', 'Password baru harus berbeda dari password saat ini.');
        }

        if (! (new AuthUserModel())->update($user['user_id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ])) {
            log_message('error', 'Failed to change password for EduSync user {id}.', ['id' => $user['user_id']]);
            return redirect()->to(site_url('profile'))
                ->with('profilePasswordError', 'Password gagal diperbarui. Silakan coba kembali.');
        }

        return redirect()->to(site_url('profile'))
            ->with('profilePasswordMessage', 'Password berhasil diperbarui.');
    }

    public function photo(): ResponseInterface
    {
        $user = $this->currentUser();
        if ($user === null) {
            return $this->response->setStatusCode(404);
        }

        $filename = (string) ($user['avatar_path'] ?? '');
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];

        if (preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $filename) !== 1 || ! isset($mimeTypes[$extension])) {
            return $this->response->setStatusCode(404);
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $filename;
        if (! is_file($path)) {
            return $this->response->setStatusCode(404);
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            log_message('error', 'Could not read a stored profile photo.');
            return $this->response->setStatusCode(500);
        }

        return $this->response
            ->setHeader('Content-Type', $mimeTypes[$extension])
            ->setHeader('Content-Length', (string) strlen($contents))
            ->setHeader('Cache-Control', 'private, no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($contents);
    }

    private function currentUser(): ?array
    {
        $authUser = session()->get('authUser');
        if (! is_array($authUser) || empty($authUser['user_id'])) {
            return null;
        }

        $user = (new AuthUserModel())->find((int) $authUser['user_id']);

        return is_array($user) ? $user : null;
    }

    private function validatePhoto(\CodeIgniter\HTTP\Files\UploadedFile $photo): ?string
    {
        if (! $photo->isValid()) {
            log_message('error', 'Profile photo upload failed with code {code}.', [
                'code' => $photo->getError(),
            ]);
            return 'Foto gagal diunggah. Silakan pilih file lagi.';
        }

        if ($photo->getSize() > self::MAX_PHOTO_SIZE) {
            return 'Ukuran foto maksimal 2 MB.';
        }

        $mimeType = $photo->getMimeType();
        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return 'Format foto harus JPG, PNG, atau WebP.';
        }

        $imageInfo = getimagesize($photo->getTempName());
        if ($imageInfo === false || ! in_array($imageInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return 'File yang dipilih bukan foto yang valid.';
        }

        if ($imageInfo[0] > 6000 || $imageInfo[1] > 6000) {
            return 'Dimensi foto maksimal 6000 × 6000 piksel.';
        }

        return null;
    }

    private function deletePhoto(string $filename): void
    {
        if (preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $filename) !== 1) {
            log_message('error', 'Refused to delete an invalid profile photo path.');
            return;
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path) && ! unlink($path)) {
            log_message('error', 'Failed to delete a replaced profile photo.');
        }
    }
}
