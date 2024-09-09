<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    use HasFactory;

    protected $primaryKey = 'sop_id';

    protected $fillable = [
        'sop_name',
        'sop_link'
    ];
}
