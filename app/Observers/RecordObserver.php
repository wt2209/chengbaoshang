<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Deposit;
use App\Models\Record;

class RecordObserver
{
    public function created(Record $record)
    {
        $record->company_name = $record->company->company_name;
        $record->is_living = true;
        $record->has_lease = Category::where('id', $record->category_id)->value('has_lease');
        $record->save();

        // 创建押金记录
        $deposit = new Deposit();
        $deposit->record_id = $record->id;
        $deposit->company_name = $record->company_name;
        $deposit->money = request()->input('deposit');
        $deposit->billed_at = now();
        $deposit->save();
    }

    public function updated(Record $record)
    {
        //
    }

    public function deleted(Record $record)
    {
        //
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
