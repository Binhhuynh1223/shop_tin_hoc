<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

require_once __DIR__ . '/../../PDO.php';

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['username', 'password', 'full_name', 'email', 'phone', 'address', 'role', 'avatar'];


    public function findByUserid($userid)
    {
        return self::where('id', $userid)->first();
    }

    public function findByUsername($username)
    {
        return self::where('username', $username)->first();
    }

    public function createUser($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return self::create($data);
    }

    public function updateUser($id, $data)
    {
        $user = self::find($id);
        if (!$user) return false;
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $user->update($data);
    }

    public function deleteUser($id)
    {
        $user = self::find($id);
        if (!$user) return false;
        return $user->delete();
    }
}
