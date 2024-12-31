<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\HasApiTokens;

class License extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'uri_access',
        'access_token',
        'finish_date',
        'status',
        'license_token',
        'license',
    ];

    // Ocultar campos a mostrar desde cualquier consulta
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
