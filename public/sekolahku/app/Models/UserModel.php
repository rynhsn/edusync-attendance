<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['username', 'email', 'password', 'full_name', 'avatar', 'role', 'is_active', 'last_login'];
    protected $useTimestamps = true;

    /**
     * Attempt to authenticate a user by username/email and password.
     *
     * @param string $username The username or email address.
     * @param string $password The plain-text password.
     *
     * @return array|null The user record on success, or null on failure.
     */
    public function attemptLogin(string $username, string $password)
    {
        $user = $this->groupStart()
                     ->where('username', $username)
                     ->orWhere('email', $username)
                     ->groupEnd()
                     ->where('is_active', 1)
                     ->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }
}
