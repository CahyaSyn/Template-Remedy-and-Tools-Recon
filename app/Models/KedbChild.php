<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KedbChild extends Model
{
    use HasFactory;

    protected $primaryKey = 'kedb_child_id';
    protected $fillable = ['kedb_child_name', 'kedb_parent_id'];
}
