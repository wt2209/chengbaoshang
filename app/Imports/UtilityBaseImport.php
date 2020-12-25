<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Room;
use App\Models\UtilityBase;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UtilityBaseImport implements ToCollection
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $roomMapper = Room::where('is_using', true)->pluck('id', 'title');
        foreach ($rows as $row) {
            // 跳过不是底数的行（第一行）
            if (!is_numeric($row[1])) {
                continue;
            }
            // 跳过不存在的房间
            if (isset($roomMapper[$row[0]])) {
                // 保留最后导入的数据
                UtilityBase::updateOrCreate(
                    [
                        'room_id' => $roomMapper[$row[0]],
                        'year' => $this->year,
                        'month' => $this->month,
                    ],
                    [
                        'pre_electric_base' => $row[1],
                        'current_electric_base' => $row[2],
                        'pre_water_base' => $row[3],
                        'current_water_base' => $row[4],
                    ]
                );
            }
        }
    }
}
