<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArrahnuSystemGlobalParameter extends Model
{
    use HasFactory;

    protected $connection = 'arrahnudb';
    protected $table = "SYSTM.GLOBAL_PARAMETER";
    protected $guarded = [];
    public $timestamps = false;
}
