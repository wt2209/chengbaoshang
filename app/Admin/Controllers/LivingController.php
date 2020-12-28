<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Record;
use App\Models\Room;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LivingController extends Controller
{
    public function index(Content $content, Request $request)
    {
        $buildings = $this->getBuildings();

        if ($request->has('building') && $request->has('unit')) {
            $rooms = $this->getRoomsByAreaAndBuilding($request->input('building'), $request->input('unit'));
        } else if ($request->has('keyword') && !empty($request->input('keyword'))) {
            $keyword = $request->input('keyword');
            if (strpos('-', $keyword) !== 0) { // 房间号
                $rooms =  $this->getRoomsByKeyword($keyword);
            } else { // 公司名
                $rooms =  $this->getRoomsByCompany($keyword);
            }
        } else {
            $rooms = collect();
        }

        $content->title('居住信息');
        $content->view('living/index', compact('buildings', 'rooms'));
        return $content;
    }

    protected function getRoomsByAreaAndBuilding($areaBuilding, $unit)
    {
        $arr = explode('-', $areaBuilding);
        $area = $arr[0];
        $building = $arr[1] ?? null;

        return Room::with([
            'records' => function ($query) {
                $query->where('is_living', true);
            },
            'records.company',
        ])
            ->where('area', $area)
            ->where('building', $building)
            ->where('unit', $unit)
            ->get();
    }

    protected function getRoomsByKeyword($keyword)
    {
        return Room::with([
            'records' => function ($query) {
                $query->where('is_living', true);
            },
            'records.company',
        ])
            ->where('title', 'like', "{$keyword}%")
            ->get();
    }

    protected function getRoomsByCompany($companyName)
    {
        $companyIds = Company::where('company_name', 'like', "%{$companyName}%")->pluck('id')->toArray();
        $recordIds = Record::where('is_living', true)->whereIn('company_id', $companyIds)->pluck('id')->toArray();
        return Room::with([
            'records' => function ($query) {
                $query->where('is_living', true);
            },
            'records.company',
        ])
            ->whereHas('records', function ($query) use ($recordIds) {
                $query->whereIn('id', $recordIds);
            })
            ->get();
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
            usort($buildings[$key], function ($a, $b) {
                return intval($a) - intval($b);
            });
        }

        Cache::forever('buildings', $buildings);
        return $buildings;
    }
}
