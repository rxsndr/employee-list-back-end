<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePicture extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_email',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];
}