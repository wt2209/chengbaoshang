<?php

namespace App\Admin\Actions\Report;

use App\Models\Record;
use App\Models\Report;
use App\Models\UtilityBase;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Generate extends Action
{
    protected $selector = '.generate';

    public function handle(Request $request)
    {
        $arr = explode('-', $request->year_month);
        $year = (int) $arr[0];
        $month = (int) $arr[1];
        if ($year < 2000 || $year > 2200 || $month < 1 || $month > 12) {
            return $this->response()->error('错误：月度输入错误');
        }

        $startTimestamp = mktime(0, 0, 0, $month, 1, $year);
        $startDate = date('Y-m-d', $startTimestamp);
        $endDate = date('Y-m-t', $startTimestamp);
        $endTimestamp = strtotime($endDate);
        // 存在租期的单独计费
        $records = Record::where('has_lease', false)
            ->where('entered_at', '<=', $endDate)
            ->where(function ($query) use ($startDate) {
                $query->where('is_living', true)
                    ->orWhere('quitted_at', '>=', $startDate);
            })
            ->get();

        $bases = UtilityBase::where('year', $year)
            ->where('month', $month)
            ->get()
            ->toArray();
        $baseMapper = [];
        foreach ($bases as $base) {
            $baseMapper[$base['room_id']] = $base;
        }

        // 电单价
        $electricPrice = 0.55;
        // 水单价
        $waterPrice = 3.7;

        // 已经生成过的
        $hasGeneratedIds = Report::where('year', $year)->where('month', $month)->pluck('record_id')->toArray();

        $insertData = [];
        foreach ($records as $record) {
            $recordStart = max($startTimestamp, strtotime($record->entered_at));
            $quittedAt = $record->is_living ? $endDate : $record->quitted_at;
            $recordEnd = min($endTimestamp, strtotime($quittedAt));
            $t = date('t', $startTimestamp); // total days of this month
            $days = intval(date('d', $recordEnd)) - intval(date('d', $recordStart)) + 1; // 本月住了几天

            // 已经生成，就不再重新生成
            if (in_array($record->id, $hasGeneratedIds)) {
                continue;
            }

            $report = [
                'record_id' => $record->id,
                'company_name' => $record->company->company_name,
                'start_date' => date('Y-m-d', $recordStart),
                'end_date' => date('Y-m-d', $recordEnd),
                'year' => $year,
                'month' => $month,
                'rent' => round($record->rent * ($days / $t), 2),
                'pre_electric_base' => $baseMapper[$record->room_id]['pre_electric_base'] ?? 0,
                'current_electric_base' => $baseMapper[$record->room_id]['current_electric_base'] ?? 0,
                'pre_water_base' => $baseMapper[$record->room_id]['pre_water_base'] ?? 0,
                'current_water_base' => $baseMapper[$record->room_id]['current_water_base'] ?? 0,
            ];
            if ($recordStart > $startTimestamp) {
                $report['pre_electric_base'] = $record->electric_start_base ?? 0;
                $report['pre_water_base'] = $record->water_start_base ?? 0;
            }
            if ($recordEnd < $endTimestamp) {
                $report['current_electric_base'] = $record->electric_end_base ?? 0;
                $report['current_water_base'] = $record->water_end_base ?? 0;
            }

            $report['electric_price'] = $electricPrice;
            $report['electric_amount'] = $report['current_electric_base'] - $report['pre_electric_base'];
            $report['electric_amount'] = max(0, $report['electric_amount']);
            $report['electric_money'] = round($report['electric_price'] * $report['electric_amount'], 2);
            $report['water_price'] = $waterPrice;
            $report['water_amount'] = $report['current_water_base'] - $report['pre_water_base'];
            $report['water_amount'] = max(0, $report['water_amount']);
            $report['water_money'] = round($report['water_price'] * $report['water_amount'], 2);
            $report['created_at'] = $report['updated_at'] = now();

            $report['actual_rent'] = $report['rent'];

            $insertData[] = $report;
        }

        // 插入剩余的
        if (count($insertData) > 0) {
            DB::table('reports')->insert($insertData);
        }
        return $this->response()->success('操作成功')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-success generate">生成报表</a>
HTML;
    }

    public function form()
    {
        $this->text('year_month', '请填写月度')->placeholder('格式：2020-12')->rules('required');
    }
}
