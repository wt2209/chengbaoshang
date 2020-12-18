<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->xieligongyu();
        $this->qingwugongyu();
    }

    public function xieligongyu()
    {
        $data = [];
        for ($building = 1; $building <= 4; $building++) {
            for ($floor = 1; $floor <= 17; $floor++) {
                if ($floor >= 1 && $floor <= 3) {
                    $unit = '1-3层';
                } else if ($floor >= 4 && $floor <= 6) {
                    $unit = '4-6层';
                } else if ($floor >= 7 && $floor <= 9) {
                    $unit = '7-9层';
                } else if ($floor >= 10 && $floor <= 12) {
                    $unit = '10-12层';
                } else if ($floor >= 13 && $floor <= 15) {
                    $unit = '13-15层';
                } else {
                    $unit = '16-17层';
                }
                for ($number = 1; $number <= 17; $number++) {
                    $data[] = [
                        'area'=>'一区',
                        'title' => $building . '-' . $floor . str_pad($number, 2, '0', STR_PAD_LEFT),
                        'building' => $building . '#',
                        'unit' => $unit,
                        'default_number' => 8,
                        'default_deposit' => 2000,
                        'default_rent' => 588,
                    ];
                }
            }
        }
        DB::table('rooms')->insert($data);
    }

    public function qingwugongyu()
    {
        $data = [];
        for ($building = 1; $building <= 5; $building++) {
            if ($building === 2 || $building === 5) {
               continue;
            }
            for ($floor = 1; $floor <= 17; $floor++) {
                if ($floor >= 1 && $floor <= 3) {
                    $unit = '1-3层';
                } else if ($floor >= 4 && $floor <= 6) {
                    $unit = '4-6层';
                } else if ($floor >= 7 && $floor <= 9) {
                    $unit = '7-9层';
                } else if ($floor >= 10 && $floor <= 12) {
                    $unit = '10-12层';
                } else if ($floor >= 13 && $floor <= 15) {
                    $unit = '13-15层';
                } else {
                    $unit = '16-17层';
                }
                for ($n = 1; $n <= 17; $n++) {
                    $data[] = [
                        'area'=>'二区',
                        'title' => 'QW' . $building . '-' . $floor . str_pad($n, 2, '0', STR_PAD_LEFT),
                        'building' => $building . '#',
                        'unit' => $unit,
                        'default_number' => 6,
                        'default_deposit' => 2000,
                        'default_rent' => 580,
                    ];
                }
            }
        }
        DB::table('rooms')->insert($data);
    }
}
