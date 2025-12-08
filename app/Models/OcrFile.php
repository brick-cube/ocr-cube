<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcrFile extends Model
{
    protected $fillable = ['filename', 'path', 'server_path'];
}
