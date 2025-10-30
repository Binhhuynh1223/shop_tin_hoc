<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

require_once __DIR__ . '/../../PDO.php';

class Service extends Model
{
    protected $table = 'services';
    protected $fillable = ['service_name', 'description', 'price'];

    public function getAllServices()
    {
        return self::all();
    }

    public function findServiceById($id)
    {
        return self::find($id);
    }
}