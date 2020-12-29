<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Deposit;
use App\Models\Record;
use App\Models\Rent;
use App\Models\Report;
use Carbon\Carbon;

class RecordObserver
{
    public function created(Record $record)
    {
        // 完善其他信息
        $record->company_name = $record->company->company_name;
        $record->is_living = true;
        $record->save();

        // 若有租期，则直接生成整个租期的租金
        if ($record->has_lease) {
            $start = Carbon::parse($record->lease_start);
            // 结束日期加1天，方便处理
            $end = Carbon::parse($record->lease_end)->addDay();
            $diff = $start->diff($end);
            $monthCount = ($diff->y) * 12 + $diff->m;
            $dayCount = $diff->d;
            $endMonthDays = (int)date('t', strtotime($record->lease_end));
            $rentPerMonth = $record->rent;
            $money = round($rentPerMonth * ($monthCount + min(1, $dayCount / $endMonthDays)), 2);

            // 租金模型
            $rent = new Rent();
            $rent->record_id = $record->id;
            $rent->company_name = $record->company->company_name;
            $rent->money = $money;
            $rent->year = date('Y');
            $rent->month = date('m');
            $rent->start_date = $record->lease_start;
            $rent->end_date = $record->lease_end;
            $rent->save();
        }
    }

    public function updated(Record $record)
    {
        //
    }

    public function deleted(Record $record)
    {
        // 删除记录时，自动删除对应的押金记录
        $deposit = Deposit::where('record_id', $record->id)->first();
        $deposit->delete();

        // 删除预交费记录
        $rent = Rent::where('record_id', $record->id)->first();
        if ($rent) {
            $rent->delete();
        }

        // 删除月度报表中的记录
        $report = Report::where('record_id', $record->id)->first();
        if ($report) {
            $report->delete();
        }
    }

    public function restored(Record $record)
    {
        //
    }

    public function forceDeleted(Record $record)
    {
        //
    }
}
