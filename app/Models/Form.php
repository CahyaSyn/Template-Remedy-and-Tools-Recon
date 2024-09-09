<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $primaryKey = 'form_id';
    protected $fillable = [
        'ticket_id',
        'app_id',
        'casename',
        'action',
        'nextaction',
        'evidence',
        'kedb_id',
        'assignment',
        'user_id',
        'starts_at',
        'ends_at',
        'notes',
        'parameter',
        'document',
        'created_at',
    ];
}
