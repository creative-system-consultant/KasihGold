<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvCartKoop extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'inv_cart_koop';
    protected $guarded = [];

    public function products()
    {
        return $this->belongsTo('App\Models\InvInfo', 'item_id', 'item_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\InvItem', 'item_id', 'id');
    }

    public function commission()
    {
        return $this->belongsTo('App\Models\CommissionRateKap', 'item_id', 'item_id');
    }

    public function outright_price()
    {
        return $this->belongsTo('App\Models\OutrightPrice', 'item_id', 'item_id');
    }

    public function percentage()
    {
        $category = "1g";

        if ($this->prod_gram >= 1000) {
            $category = "1000g";
        } else if ($this->prod_gram >= 250) {
            $category = "250g";
        } else if ($this->prod_gram >= 100) {
            $category = "100g";
        } else if ($this->prod_gram >= 50) {
            $category = "50g";
        } else if ($this->prod_gram >= 20) {
            $category = "20g";
        } else if ($this->prod_gram >= 10) {
            $category = "10g";
        } else if ($this->prod_gram >= 5) {
            $category = "5g";
        }
        $spotPricePercentage = SpotGoldPricing::select('percentage')->where('range', $category)->first();
        return ($spotPricePercentage->percentage / 100);
    }
}
