<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_email',
        'task_name',
        'status',
        'notes',
        'deadline_date',
        'given_by',
        'signature',
    ];
}
