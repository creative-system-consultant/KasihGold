<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiuuBills extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "fiuu_bills";
    protected $guarded = [];
    public $timestamps = true;
    protected $fillable = ['ref_no', 'amount', 'status', 'data', 'user_id', 'customer_purchase_flag', 'created_by', 'updated_by', 'deleted_by'];
}
