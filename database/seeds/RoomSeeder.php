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
        $this->xieli_zonghelou();
        $this->qingwugongyu();
    }

    public function xieligongyu()
    {
        $data = [];
        for ($building = 1; $building <= 4; $building++) {

            $topFloor = in_array($building, [1, 2]) ? 18 : 16;

            for ($floor = 1; $floor <= $topFloor; $floor++) {
                if ($floor >= 1 && $floor <= 4) {
                    $unit = '1-4层';
                } else if ($floor >= 5 && $floor <= 8) {
                    $unit = '5-8层';
                } else if ($floor >= 9 && $floor <= 12) {
                    $unit = '9-12层';
                } else if ($floor >= 13 && $floor <= 16) {
                    $unit = '13-16层';
                } else {
                    $unit = '17-18层';
                }
                for ($number = 1; $number <= 17; $number++) {

                    $d = [
                        'area' => '一区',
                        'title' => $building . '-' . $floor . str_pad($number, 2, '0', STR_PAD_LEFT),
                        'building' => $building . '#楼',
                        'unit' => $unit,
                        'default_deposit' => 2000,
                    ];

                    if ($building === 1 || $building === 2) {
                        if ($number === 1 || $number === 15) {
                            $d['default_number'] = 6;
                            $d['default_rent'] = 588;
                        } else if ($number === 2) {
                            $d['default_number'] = 12;
                            $d['default_rent'] = 1128;
                        } else {
                            $d['default_number'] = 8;
                            $d['default_rent'] = 768;
                        }
                    } else {
                        if ($number === 16) {
                            $d['default_number'] = 6;
                            $d['default_rent'] = 588;
                        } else if ($number === 17) {
                            $d['default_number'] = 12;
                            $d['default_rent'] = 1128;
                        } else {
                            $d['default_number'] = 8;
                            $d['default_rent'] = 768;
                        }
                    }

                    $data[] = $d;
                }
            }
        }
        DB::table('rooms')->insert($data);
    }

    public function xieli_zonghelou()
    {
        $names = [
            '101' => '每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '102'=>'',
            '103' =>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '104'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计120平米',
            '104甲'=>'因为丰县瑞星盛餐厅有两块电表，虚拟一个房间便于电费的计算',
            '105'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计120平米',
            '106'=>'',
            '107'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '108'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '201'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '202'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '203'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '204'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计120平米',
            '205'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计120平米',
            '206'=>'',
            '207'=>'',
            '208'=>'',
            '301'=>'',
            '302'=>'',
            '303'=>'每平米租金0.8元，卫生费03.元，合计1.1元。共计85平米',
            '305'=>'',
            '超市'=>'',
            '过道'=>'',
        ];
        $data = [];
        foreach ($names as $name => $remark) {
            $data[] = [
                'area' => '一区',
                'title' => '综合楼' . $name,
                'building' => '综合楼',
                'unit' => '1-3层',
                'default_number' => 0,
                'default_deposit' => 0,
                'default_rent' => 0,
                'remark' => $remark,
            ];
        }
        DB::table('rooms')->insert($data);
    }


    public function qingwugongyu()
    {
        $data = [];
        for ($building = 1; $building <= 4; $building++) {
            if ($building === 2) {
                $topFloor = 16;
            } else {
                $topFloor = 18;
            }
            for ($floor = 1; $floor <= $topFloor; $floor++) {
                if ($floor >= 1 && $floor <= 4) {
                    $unit = '1-4层';
                } else if ($floor >= 5 && $floor <= 8) {
                    $unit = '5-8层';
                } else if ($floor >= 9 && $floor <= 12) {
                    $unit = '9-12层';
                } else if ($floor >= 13 && $floor <= 16) {
                    $unit = '13-16层';
                } else {
                    $unit = '17-18层';
                }
                for ($n = 1; $n <= 17; $n++) {
                    $d = [
                        'area' => '二区',
                        'title' => 'QW' . $building . '-' . $floor . str_pad($n, 2, '0', STR_PAD_LEFT),
                        'building' => $building . '#楼',
                        'unit' => $unit,
                        'default_number' => 6,
                        'default_deposit' => 2000,
                        'default_rent' => 580,
                    ];
                    if ($building === 1 || $building === 2 || $building === 3) {
                        if ($n === 17) {
                            $d['default_number'] = 10;
                            $d['default_rent'] = 910;
                        } else {
                            $d['default_number'] = 6;
                            $d['default_rent'] = 580;
                        }
                    } else { // 4#楼
                        if ($n === 1) {
                            $d['default_number'] = 10;
                            $d['default_rent'] = 910;
                        } else {
                            $d['default_number'] = 6;
                            $d['default_rent'] = 580;
                        }
                    }

                    $data[] = $d;
                }
            }
        }
        DB::table('rooms')->insert($data);
    }
}
