<?php

namespace App\Admin\Controllers;

use App\Models\Deposit;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function uncharged(Content $content)
    {
        $deposits = DB::table('deposits')
            ->whereNull('charged_at')
            ->join('companies', 'companies.id', '=', 'deposits.company_id')
            ->join('rooms', 'rooms.id', '=', 'deposits.room_id')
            ->select(
                DB::raw('sum(`money`) as money'),
                DB::raw('companies.company_name as current_company_name'),
                DB::raw('deposits.company_name as old_company_name')
            )
            ->groupBy('current_company_name', 'old_company_name')
            ->get();

        $rents = DB::table('rents')
            ->whereNull('charged_at')
            ->join('companies', 'companies.id', '=', 'rents.company_id')
            ->join('rooms', 'rooms.id', '=', 'rents.room_id')
            ->select(
                DB::raw('sum(`money`) as money'),
                DB::raw('companies.company_name as current_company_name'),
                DB::raw('rents.company_name as old_company_name'),
                'year',
                'month'
            )
            ->groupBy('current_company_name', 'old_company_name', 'year', 'month')
            ->get();

        $reports = DB::table('reports')
            ->whereNotNull('discounted_at')
            ->whereNull('charged_at')
            ->join('companies', 'companies.id', '=', 'reports.company_id')
            ->join('rooms', 'rooms.id', '=', 'reports.room_id')
            ->select(
                DB::raw('sum(actual_rent) + sum(electric_money) + sum(water_money) as money'),
                DB::raw('companies.company_name as current_company_name'),
                DB::raw('reports.company_name as old_company_name'),
                'year',
                'month'
            )
            ->groupBy('current_company_name', 'old_company_name', 'year', 'month')
            ->get();

        $bills = DB::table('bills')
            ->whereNull('charged_at')
            ->join('companies', 'companies.id', '=', 'bills.company_id')
            ->select(
                DB::raw('sum(`money`) as money'),
                DB::raw('companies.company_name as current_company_name'),
            )
            ->groupBy('current_company_name')
            ->get();

        $content->title('欠费');

        return $content->view('statistics.uncharged', compact('deposits', 'rents', 'reports', 'bills'));
    }
}
