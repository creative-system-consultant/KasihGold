<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArrahnuRefStaff extends Model
{
    use HasFactory;

    protected $connection = 'arrahnudb';
    protected $table = 'ARRAHNU.REF_STAFF';
    protected $guarded = [];
    public $timestamps = false;
}
