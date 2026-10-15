<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthUserModel extends Model
{
    protected $table            = 'auth_users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'email',
        'display_name',
        'username',
        'password_hash',
        'avatar_path',
        'role',
        'guru_id',
        'is_verified',
        'otp_hash',
        'otp_expires_at',
        'otp_sent_at',
        'otp_attempts',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
