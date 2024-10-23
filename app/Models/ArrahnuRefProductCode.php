<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArrahnuRefProductCode extends Model
{
    use HasFactory;

    protected $connection = 'arrahnudb';
    protected $table = "ARRAHNU.REF_PRODUCT_CODE";
    protected $primaryKey = 'PROD_CODE';

    protected $guarded = [];

    public $timestamps = false;

    public function getMaturityDate($date)
    {
        $day_count = 30 * $this->DURATION;
        return date('Y-m-d', strtotime('+' . $day_count . ' days', strtotime($date)));
    }

    /**
     * Fetch active Arrahnu products.
     *
     * @return array
     */
    public static function fetchActiveArrahnuProducts()
    {
        $products = static::where('RECORD_STATUS', 'AKTIF')->where('PROD_TYPE', 'KAPG')->where('CLIENT_ID', config('app.client_id'))->get();
        $value = [];

        foreach ($products as $row) {
            $value[$row->PROD_CODE] = [
                'description' => $row->PROD_DESC,
                'min_financing' => $row->MIN_FIN,
                'max_financing' => $row->MAX_FIN,
                'duration' => $row->DURATION,
                'margin' => $row->MARGIN,
                'profit' => $row->PROFIT,
                'prod_code' => $row->PROD_CODE,
            ];
        }

        return $value;
    }
}
