<?php

namespace App\Http\Livewire\Page;

use App\Models\Announcement;
use App\Models\BuyBack;
use App\Models\CommissionDetailKap;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\InvCart;
use App\Models\InvInfo;
use App\Models\OutrightPrice;
use App\Models\OutrightSell;
use App\Models\outrightSG;
use App\Models\PhysicalConvert;
use App\Models\SpotGoldPricing;
use App\Models\User;
use App\Models\UserDownline;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Home extends Component
{
    public $cart;
    public $pendingApproval, $myAgent, $withdrawalRequest, $todayTrans, $cashback;
    public $activeUser, $myWallet;
    public $userGold;
    public $tGold, $pGold, $goldInfo, $lGold;
    public $chart1, $mainchart1, $chart2, $mainchart2, $chart3, $mainchart3;
    public $subchart1day, $subchart1month, $subchart2day, $subchart2month, $subchart3day, $subchart3month, $subchart4day, $subchart4month;
    public $announcement;
    public $spotPrice, $spotPriceB, $dinarPrice, $digitalPrice, $digital1gOutPrice;

    public function mount()
    {
        // admin KAP
        if (auth()->user()->isAdminKAP()) {
            $this->pendingApproval = User::where('client', 2)->where('role', 3)->where('active', 0)->get();
            $this->myAgent = User::where('client', 2)->where('role', 3)->where('active', 1)->get();
            $this->withdrawalRequest = (OutrightSell::where('status', 0)->count() + BuyBack::where('status', 0)->count() + PhysicalConvert::where('status', 0)->count() + outrightSG::where('status', 0)->count());
            $this->todayTrans = GoldbarOwnership::where('split', '!=', 3)->whereDate('created_at', '=', now()->format('Y-m-d'))->sum('bought_price');
            $this->cashback = CommissionDetailKap::sum('commission');
            $this->chart1 = GoldbarOwnership::get();
            $this->mainchart1 = DB::table('gold_ownership')
                ->select(DB::raw("top 12 CAST(YEAR(created_at) AS VARCHAR(4)) + '-' +  CAST(MONTH(created_at) AS VARCHAR(2)) as x"), DB::raw('SUM(bought_price) as y'))
                ->groupBy(DB::raw("CAST(YEAR(created_at) AS VARCHAR(4)) + '-' +  CAST(MONTH(created_at) AS VARCHAR(2))"))
                ->orderBy(DB::raw("CAST(YEAR(created_at) AS VARCHAR(4)) + '-' +  CAST(MONTH(created_at) AS VARCHAR(2))"), 'desc')
                ->get()
                ->toArray();
            $this->chart2 = Goldbar::get();
            $this->mainchart2 = DB::table('goldbar')
                ->select(DB::raw("top 12 CAST(YEAR(created_at) AS VARCHAR(4)) + '-' +  CAST(MONTH(created_at) AS VARCHAR(2)) as x"), DB::raw('SUM(bought_price) as y'))
                ->groupBy(DB::raw("CAST(YEAR(created_at) AS VARCHAR(4)) + '-' +  CAST(MONTH(created_at) AS VARCHAR(2))"))
                ->orderBy(DB::raw("CAST(YEAR(created_at) AS VARCHAR(4)) + '-' +  CAST(MONTH(created_at) AS VARCHAR(2))"), 'desc')
                ->get()
                ->toArray();
            $this->mainchart3 = DB::select('SET NOCOUNT ON ; exec calculate_revenue');
            $this->chart3 = collect($this->mainchart3);

            $this->subchart1day = DB::table('gold_ownership')
                ->select(DB::raw('top 10 CAST(created_at AS DATE) as x'), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 0.01)
                ->groupBy(DB::raw("CAST(created_at AS DATE)"))
                ->orderBy(DB::raw("CAST(created_at AS DATE)"), 'desc')
                ->get()
                ->toArray();
            $this->subchart1month = DB::table('gold_ownership')
                ->select(DB::raw("top 12 DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4)) AS x"), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 0.01)
                ->groupBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"))
                ->orderBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"), 'desc')
                ->get()
                ->toArray();

            $this->subchart2day = DB::table('gold_ownership')
                ->select(DB::raw('top 10 CAST(created_at AS DATE) as x'), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 0.1)
                ->groupBy(DB::raw("CAST(created_at AS DATE)"))
                ->orderBy(DB::raw("CAST(created_at AS DATE)"), 'desc')
                ->get()
                ->toArray();
            $this->subchart2month = DB::table('gold_ownership')
                ->select(DB::raw("top 12 DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4)) AS x"), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 0.1)
                ->groupBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"))
                ->orderBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"), 'desc')
                ->get()
                ->toArray();

            $this->subchart3day = DB::table('gold_ownership')
                ->select(DB::raw('top 10 CAST(created_at AS DATE) as x'), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 0.25)
                ->groupBy(DB::raw("CAST(created_at AS DATE)"))
                ->orderBy(DB::raw("CAST(created_at AS DATE)"), 'desc')
                ->get()
                ->toArray();
            $this->subchart3month = DB::table('gold_ownership')
                ->select(DB::raw("top 12 DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4)) AS x"), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 0.25)
                ->groupBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"))
                ->orderBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"), 'desc')
                ->get()
                ->toArray();

            $this->subchart4day = DB::table('gold_ownership')
                ->select(DB::raw('top 10 CAST(created_at AS DATE) as x'), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 1)
                ->groupBy(DB::raw("CAST(created_at AS DATE)"))
                ->orderBy(DB::raw("CAST(created_at AS DATE)"), 'desc')
                ->get()
                ->toArray();
            $this->subchart4month = DB::table('gold_ownership')
                ->select(DB::raw("top 12 DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4)) AS x"), DB::raw('SUM(bought_price) as y'))
                ->where('weight', 1)
                ->groupBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"))
                ->orderBy(DB::raw("DATENAME(month, DATEADD(month, CAST(MONTH(created_at) AS VARCHAR(2))-1, CAST('2008-01-01' AS datetime))) + ' ' + CAST(YEAR(created_at) AS VARCHAR(4))"), 'desc')
                ->get()
                ->toArray();
        } elseif (auth()->user()->isAgentKAP()) {
            $this->announcement = Announcement::orderBy('id', 'desc')->get();
            $logged_user = auth()->user()->id;
            $this->pendingApproval = User::whereHas('profile', function ($query) use ($logged_user) {
                $query->where('agent_id', '=', $logged_user);
            })->whereClient(2)->whereRole(4)->whereActive(0)->get();
            $this->activeUser = UserDownline::where('user_id', $logged_user)->get();
            $x = User::whereId($logged_user)->first();
            $downline_id = array();
            foreach ($x->downline as $y) {
                $downline_id[] = array($y->downline_id);
            }
            $this->todayTrans = GoldbarOwnership::where('split', '!=', 3)
                ->whereIn('user_id', $downline_id)
                ->whereDate('created_at', '=', now()->format('Y-m-d'))
                ->sum('bought_price');
            $this->cashback = CommissionDetailKap::where('user_id', auth()->user()->id)->where('status', 0)->sum('commission');
            $this->myWallet = array_sum(GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->pluck('bought_price')->toArray());

            $this->chart1 = collect(DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_DETAIL ' . $logged_user));
            $this->mainchart1 = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_MONTHLY ' . $logged_user . ', "0.00"');
            $this->subchart1day = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_DAILY ' . $logged_user . ', "0.01" ');
            $this->subchart1month = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_MONTHLY ' . $logged_user . ', "0.01" ');
            $this->subchart2day = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_DAILY ' . $logged_user . ', "0.1" ');
            $this->subchart2month = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_MONTHLY ' . $logged_user . ', "0.1" ');
            $this->subchart3day = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_DAILY ' . $logged_user . ', "0.25" ');
            $this->subchart3month = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_MONTHLY ' . $logged_user . ', "0.25" ');
            $this->subchart4day = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_DAILY ' . $logged_user . ', "1" ');
            $this->subchart4month = DB::select('SET NOCOUNT ON ; exec DOWNLINE_TOTAL_BOUGHT_MONTHLY ' . $logged_user . ', "1" ');
        } elseif (auth()->user()->isUserKAP()) {
            $this->announcement = Announcement::orderBy('id', 'desc')->get();
            $chartQuery1 = DB::table('gold_ownership')
                ->select(DB::raw("count(weight) as weight"))
                ->where('user_id', auth()->user()->id)
                ->where('weight', '0.01')
                ->value('weight');
            $chartQuery2 = DB::table('gold_ownership')
                ->select(DB::raw("count(weight) as weight"))
                ->where('user_id', auth()->user()->id)
                ->where('weight', '0.1')
                ->value('weight');
            $chartQuery3 = DB::table('gold_ownership')
                ->select(DB::raw("count(weight) as weight"))
                ->where('user_id', auth()->user()->id)
                ->where('weight', '0.25')
                ->value('weight');
            $chartQuery4 = DB::table('gold_ownership')
                ->select(DB::raw("count(weight) as weight"))
                ->where('user_id', auth()->user()->id)
                ->where('weight', '1')
                ->value('weight');

            $dataArray = array($chartQuery1, $chartQuery2, $chartQuery3, $chartQuery4);
            $chartData = array_map('intval', $dataArray);
            $this->mainchart1 = $chartData;

            $goldInfo = GoldbarOwnership::where('user_id', auth()->user()->id)
                ->where('active_ownership', 1)
                ->get();
            $this->tGold = 0;
            $this->lGold = 0;
            $this->pGold = 0;

            foreach ($goldInfo as $gold) {
                if ($gold->financing_flag == 0) {
                    $this->tGold += $gold->available_weight;
                } elseif ($gold->financing_flag == 1) {
                    $this->lGold += $gold->available_weight;
                }
            }

            $phyExit = PhysicalConvert::where('user_id', auth()->user()->id)->where('status', 1)->get();

            foreach ($phyExit as $golds) {
                $this->pGold += $golds->one_gram + ($golds->quarter_gram * 0.25);
            }


            $this->digitalPrice = InvInfo::where('prod_cat', 1)->get();
            $this->digital1gOutPrice = OutrightPrice::select('price')->where('item_id', 9)->first();
            $this->dinarPrice = InvInfo::where('prod_cat', 2)->first();
            $this->spotPrice = InvInfo::where('prod_cat', 3)->first();
            $this->spotPriceB = SpotGoldPricing::select('percentage')->where('range', '1g')->first();
        }
    }

    public function render()
    {
        return view('livewire.page.home');
    }
}
