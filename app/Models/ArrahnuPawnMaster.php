<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class ArrahnuPawnMaster extends Model
{
    use HasFactory;

    protected $connection = 'arrahnudb';
    protected $table = "ARRAHNU.PAWN_MASTER";
    protected $guarded = [];
    public $timestamps = false;

    public function marhun()
    {
        return  $this->hasMany(PawnDetails::class, 'SIRI_NO', 'SIRI_NO');
    }

    public function customer()
    {
        return  $this->belongsTo(Cif_Customers::class, 'CIF_NO', 'id');
    }

    public function product()
    {
        return  $this->belongsTo(ArrahnuRefProductCode::class, 'PROD_CODE', 'PROD_CODE');
    }

    public function branch()
    {
        return  $this->belongsTo(Ref_Branch::class, 'BRANCH_CODE', 'BRANCH_CODE');
    }

    public function officer()
    {
        return  $this->belongsTo(Ref_Staff::class, 'STAFF_ID', 'STAFF_ID');
    }

    public function approver()
    {
        return  $this->belongsTo(Ref_Staff::class, 'APPRV_BY', 'STAFF_ID');
    }

    public function payment()
    {
        return  $this->hasMany('App\Models\Payment', 'SIRI_NO', 'SIRI_NO');
    }

    public function bank()
    {
        return $this->belongsTo(Banks::class, 'bank_id', 'id');
    }

    public function beenAuction()
    {
        return $this->hasOne(AuctionList::class, 'SIRI_NO', 'SIRI_NO');
    }

    public function getPawnStatus()
    {
        if (trim($this->STATUS) == "REVERSAL") {
            return "GADAIAN DIBATALKAN";
        } else if (trim($this->STATUS) == "CLOSED") {
            if ($this->beenAuction) {
                if (trim($this->beenAuction->AUCTION_STATUS) == "BUKA") {
                    return "GADAIAN SELESAI NAMUN MASIH AKTIF DI LELONGAN";
                } else if (trim($this->beenAuction->AUCTION_STATUS) == "SELESAI") {
                    return "TELAH DILELONG";
                } else {
                    return "GADAIAN SELESAI DAN TIDAK AKTIF DI LELONGAN";
                }
            } else {
                return "GADAIAN SELESAI";
            }
        } else if (trim($this->STATUS) == "APPLY") {
            return "PERMOHONAN";
        } else if (trim($this->STATUS) == "APPROVED") {
            if ($this->beenAuction) {
                if (trim($this->beenAuction->AUCTION_STATUS) == "BUKA") {
                    return "SEDANG DALAM LELONGAN";
                } else if (trim($this->beenAuction->AUCTION_STATUS) == "TUTUP") {
                    return "TELAH DILELONG";
                } else {
                    return "AKTIF NAMUN TELAH DILELONG";
                }
            } else {
                return "AKTIF";
            }
        } else if (trim($this->STATUS) == "CANCELLED") {
            return "PERMOHONAN DIBATALKAN";
        } else if (trim($this->STATUS) == "REJECTED") {
            return "PERMOHONAN DITOLAK";
        } else {
            return "TIDAK DIKENAL PASTI";
        }
    }

    public function getPawnPaymentBreakdown()
    {
        $pawn_date = date('Y-m-d', strtotime($this->PAWN_DATE));

        $pawn_duration = isset($this->product->DURATION) ? $this->product->DURATION : null;
        $total_days = (int)$pawn_duration * 30;
        $day_for_each_quarter = $total_days / 3;

        $q1_date = date('Y-m-d', strtotime('+' . $day_for_each_quarter . ' days', strtotime($pawn_date)));
        $q2_date = date('Y-m-d', strtotime('+' . $day_for_each_quarter . ' days', strtotime($q1_date)));
        $q3_date = date('Y-m-d', strtotime('+' . $day_for_each_quarter . ' days', strtotime($q2_date)));

        return [
            1 => [
                'month' => 6,
                'date' => $q1_date,
                'profit' => $this->TOT_PROFIT / 3,
                'principal' => 0,
            ],
            2 => [
                'month' => 12,
                'date' => $q2_date,
                'profit' => $this->TOT_PROFIT / 3,
                'principal' => 0,
            ],
            3 => [
                'month' => 18,
                'date' => $q3_date,
                'profit' => $this->TOT_PROFIT / 3,
                'principal' => $this->TOT_AMT_FIN,
            ]
        ];
    }


    public function getPawnPaymentBreakdown2()
    {
        $pawn_date = date('Y-m-d', strtotime($this->PAWN_DATE));

        $pawn_duration = isset($this->product->DURATION) ? $this->product->DURATION : null;
        $total_days = (int)$pawn_duration * 30;
        $day_for_each_quarter = $total_days / 3;

        $q1_date = date('Y-m-d', strtotime('+' . $day_for_each_quarter . ' days', strtotime($pawn_date)));
        $q2_date = date('Y-m-d', strtotime('+' . $day_for_each_quarter . ' days', strtotime($q1_date)));
        $q3_date = date('Y-m-d', strtotime('+' . $day_for_each_quarter . ' days', strtotime($q2_date)));

        return [
            1 => [
                'month' => 6,
                'date' => $q1_date,
                'profit' => $this->Q1_PROFIT_ORI,
                'principal' => $this->Q1_PROFIT,
            ],
            2 => [
                'month' => 12,
                'date' => $q2_date,
                'profit' => $this->Q2_PROFIT_ORI,
                'principal' => $this->Q2_PROFIT,
            ],
            3 => [
                'month' => 18,
                'date' => $q3_date,
                'profit' => $this->Q3_PROFIT_ORI,
                'principal' => $this->Q3_PROFIT + $this->TOT_AMT_FIN,
            ]
        ];
    }

    public static function getPawnList($category = null, $input = null)
    {
        $list = [];
        if (!is_null($input) and $input != "") {
            if ($category == 1) {
                $list = self::where('SIRI_NO', 'like', '%' . $input . '%')
                    ->where('STATUS', 'APPLY')
                    ->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['customer', 'branch', 'officer'])
                    ->paginate(10);
            } else if ($category == 2) {
                $list = self::whereHas('customer', function ($query) use ($input) {
                    $query->where('name', 'like', '%' . $input . '%');
                })->where('STATUS', 'APPLY')
                    ->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['customer' => function ($query) use ($input) {
                        $query->where('name', 'like', '%' . $input . '%');
                    }, 'branch', 'officer'])
                    ->paginate(10);
            } else if ($category == 3) {
                $list = self::whereHas('customer', function ($query) use ($input) {
                    $query->where('identity_no', 'like', '%' . $input . '%');
                })->where('STATUS', 'APPLY')
                    ->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['branch', 'officer'])
                    ->paginate(10);
            } else if ($category == 4) {
                $from = Carbon::parse($input[0])->startOfDay()->format('Y-m-d H:i:s');
                $to = Carbon::parse($input[1])->endOfDay()->format('Y-m-d H:i:s');
                $list = self::whereBetween('PAWN_DATE', [$from, $to])
                    ->where('STATUS', 'APPLY')
                    ->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['customer', 'branch', 'officer'])
                    ->paginate(10);
            }
        }

        if (!is_null($list)) {
            return $list;
        }

        return [];
    }

    public static function getPawnList2($category = null, $input = null)
    {
        $list = [];
        if (!is_null($input) and $input != "") {
            if ($category == 1) {
                $list = self::where('SIRI_NO', 'like', '%' . $input . '%')
                    ->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['customer', 'branch', 'officer'])
                    ->has('marhun')
                    ->paginate(10);
            } else if ($category == 2) {
                $list = self::whereHas('customer', function ($query) use ($input) {
                    $query->where('name', 'like', '%' . $input . '%');
                })->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['customer' => function ($query) use ($input) {
                        $query->where('name', 'like', '%' . $input . '%');
                    }, 'branch', 'officer'])
                    ->has('marhun')
                    ->paginate(10);
            } else if ($category == 3) {
                $list = self::whereHas('customer', function ($query) use ($input) {
                    $query->where('identity_no', 'like', '%' . $input . '%');
                })->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['branch', 'officer'])
                    ->has('marhun')
                    ->paginate(10);
            } else if ($category == 4) {
                $from = Carbon::parse($input[0])->startOfDay()->format('Y-m-d H:i:s');
                $to = Carbon::parse($input[1])->endOfDay()->format('Y-m-d H:i:s');
                $list = self::whereBetween('PAWN_DATE', [$from, $to])
                    ->where('BRANCH_CODE', Session::get('user')['branch_code'])
                    ->with(['customer', 'branch', 'officer'])
                    ->has('marhun')
                    ->paginate(10);
            }
        }

        if (!is_null($list)) {
            return $list;
        }

        return [];
    }

    public function isBske()
    {
        return ($this->marhun->first()->CERT_NO and $this->marhun->first()->CERT_NO != '') ? true : false;
    }

    public function productKAP($prod_code)
    {
        return self::where('PRODUCT_CODE', $prod_code)
                    ->whereHas('product', function($query) {
                        $query->where('PROD_TYPE', 'KAP');
                    })
                    ->exists();
    }
}
