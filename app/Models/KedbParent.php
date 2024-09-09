<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KedbParent extends Model
{
    use HasFactory;

    protected $primaryKey = 'kedb_parent_id';
    protected $fillable = ['kedb_parent_name'];
}
