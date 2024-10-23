<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArrahnuDailyPrice extends Model
{
    use HasFactory;

    protected $connection = 'arrahnudb';
    protected $table = "ARRAHNU.DAILY_GOLD_PRICE";
    protected $guarded = [];
    public $timestamps = false;

    public function details()
    {
        return $this->belongsTo(ArrahnuRefGoldType::class, 'GOLD_CODE', 'GOLD_CODE')->where('CLIENT_ID', config('app.client_id'));
    }

    /**
     * Fetch today's gold price details.
     *
     * @return array
     */
    public static function fetchTodayGoldPriceDetails()
    {
        $prices = static::where('EFF_DATE', date('Y-m-d'))
                        ->where('GOLD_CODE', 1)  // filter 24k karat
                        ->where('CLIENT_ID', config('app.client_id'))
                        ->get();
        // $prices = static::with(['details' => function($query){
        //     $query->where('GOLD_CODE', 1);
        // }])
        // ->where('EFF_DATE', date('Y-m-d'))
        // ->where('GOLD_CODE', 1)  // filter 24k karat
        // ->where('CLIENT_ID', config('app.client_id'))
        // ->get();
        $value = [];

        foreach ($prices as $row) {
            $value[trim($row->GOLD_CODE)] = [
                'type' => $row->details->GOLD_TYPE,
                'carat' => $row->details->GOLD_KARAT,
                'price' => $row->PRICE,
            ];
        }

        return $value;
    }
}
