<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kedb extends Model
{
    use HasFactory;

    protected $primaryKey = 'kedb_id';
    protected $fillable = [
        'kedb_parent_id',
        'kedb_child_id',
        'app_id',
        'old_kedb',
        'new_symtom_kedb',
        'new_specific_symtom_kedb',
        'kedb_finalisasi',
        'action',
        'responsibility_action',
        'sop'
    ];
}
