<?php

namespace App\Observers;

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
            $rent = new Rent();
            $rent->record_id = $record->id;
            $rent->company_name = $record->company->company_name;
            // 日期有验证，金额不会出现负数
            $rent->money = $this->getRentMoney($record->lease_start, $record->lease_end, $record->rent);
            $rent->year = date('Y', strtotime($record->entered_at));
            $rent->month = date('m', strtotime($record->entered_at));
            $rent->start_date = $record->lease_start;
            $rent->end_date = $record->lease_end;
            $rent->save();
        }
    }

    public function updated(Record $record)
    {
        // 同时变动了是否居住与退房时间，且存在退房时间，则认为此时是退房操作
        if ($record->isDirty('is_living') && $record->isDirty('quitted_at') && $record->quitted_at) {
            // 有租期且租期正常
            if ($record->has_lease && $record->lease_end) {
                $start = date('Y-m-d', strtotime('+1 day', strtotime($record->quitted_at)));
                $money = $this->getRentMoney($start, $record->lease_end, $record->rent);

                $rent = new Rent;
                $rent->record_id = $record->id;
                $rent->company_name = $record->company->company_name;
                $rent->year = date('Y', strtotime($record->quitted_at));
                $rent->month = date('m', strtotime($record->quitted_at));
              
                if ($money < 0) { // 已超期居住
                    // +1 天
                    $rent->start_date = date('Y-m-d',strtotime('+1 day', strtotime($record->lease_end)));
                    $rent->end_date = $record->quitted_at;
                    $rent->money = $money * -1;
                } else { // 需要退费
                    $rent->start_date = $start;
                    $rent->end_date = $record->lease_end;
                    $rent->money = $money;
                    $rent->is_refund = true;
                    $rent->charged_at = $record->quitted_at;
                }

                $rent->save();
            }
        }
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

    protected function getRentMoney($leaseStart, $leaseEnd, $rentPerMonth)
    {
        $start = Carbon::parse($leaseStart);
        // 结束日期加1天，方便处理
        $end = Carbon::parse($leaseEnd)->addDay();

        $diff = $start->diff($end);
        $monthCount = ($diff->y) * 12 + $diff->m;
        $dayCount = $diff->d;
        $endMonthDays = (int)date('t', strtotime($leaseEnd));
        $money = round($rentPerMonth * ($monthCount + min(1, $dayCount / $endMonthDays)), 2);
        if ($start->isAfter($end)) {
            $money = $money * -1;
        }
        return $money;
    }
}
