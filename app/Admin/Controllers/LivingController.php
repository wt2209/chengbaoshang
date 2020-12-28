<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Cache;

class LivingController extends Controller
{
    public function index(Content $content)
    {
        $buildings = $this->getBuildings();

        $content->title('居住信息');
        $content->view('living/index', compact('buildings'));
        return $content;
    }

    protected function getBuildings()
    {
        if (Cache::has('buildings')) {
            return Cache::get('buildings');
        }

        $groups = Room::select('area', 'building', 'unit')->groupBy('area', 'building', 'unit')->get();

        $buildings = [];
        foreach ($groups as $group) {
            $key = $group['area'] . '-' . $group['building'];
            $value = $group['unit'];
            if (!isset($buildings[$key][$value])) {
                $buildings[$key][] = $value;
            } 
        }

        // 每栋楼的楼层排序
        foreach ($buildings as $key => $value) {
            usort($buildings[$key], function($a, $b) {
                return intval($a) - intval($b);
            });
        }

        Cache::forever('buildings', $buildings);
        return $buildings;
    }
}
