<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OutrightSell extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'outright_sell';
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function userDefault()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function goldbarOwnerships()
    {
        return $this->hasMany(GoldbarOwnership::class, 'ex_id', 'id');
    }

    public function hasFinancingFlag()
    {
        return $this->goldbarOwnerships()->where('financing_flag', 2)->exists();
    }
}
