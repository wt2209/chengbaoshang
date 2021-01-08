<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LivingQuitRequest;
use App\Http\Requests\LivingStoreRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\Deposit;
use App\Models\Record;
use App\Models\Room;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LivingController extends Controller
{
    public function index(Content $content, Request $request)
    {
        $buildings = $this->getBuildings();

        if ($request->has('building') && $request->has('unit')) {
            $rooms = $this->getRoomsByAreaAndBuilding($request->input('building'), $request->input('unit'));
        } else if ($request->has('keyword') && !empty($request->input('keyword'))) {
            $keyword = $request->input('keyword');
            if (strpos($keyword, '-') !== false) { // 房间号
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

    public function create(Content $content)
    {
        $companies = Company::orderBy('company_name', 'asc')->get();
        $categories = Category::get();
        $emptyRooms = Room::where('is_using', true)
            ->whereNotIn('id', Record::where('is_living', true)->pluck('room_id')->toArray())
            ->get();
        $content->title('入住');
        $content->view('living/create', compact('companies', 'categories', 'emptyRooms'));
        return $content;
    }

    public function store(LivingStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $selectedRooms = $request->input('rooms');
            foreach ($selectedRooms as $room) {
                $data = [
                    'company_id' => $request->company_id,
                    'category_id' => $request->category_id,
                    'entered_at' => $request->entered_at,
                    'has_lease' => (int) $request->has_lease,
                    'room_id' => $room['room_id'],
                    'gender' => $room['gender'],
                    'deposit_money' => $room['deposit'],
                    'rent' => $room['rent'],
                    'electric_start_base' => $room['electric_start_base'] ?? 0,
                    'water_start_base' => $room['water_start_base'] ?? 0,
                ];
                if ($data['has_lease']) {
                    $data['lease_start'] = $request->lease_start;
                    $data['lease_end'] = $request->lease_end;
                }
                $record = Record::create($data);

                // 创建押金记录
                $deposit = new Deposit();
                $deposit->record_id = $record->id;
                $deposit->company_name = $record->company_name;
                $deposit->money = $room['deposit'];
                $deposit->save();
            }
        });
        admin_toastr('操作成功', 'success');
        return redirect(route('admin.livings.index'));
    }

    public function quit(Content $content)
    {
        $companies = Company::get();
        $content->title('退房');
        $content->view('living/quit', compact('companies'));
        return $content;
    }

    public function delete(LivingQuitRequest $request)
    {
        DB::transaction(function () use($request) {
            $company = Company::find($request->company_id);
            foreach ($request->records as $record) {
                $model = Record::find($record['id']);
                // 确保数据无误
                if ($model && $model->company_id = $company->id) {
                    $model->is_living = false;
                    $model->quitted_at = $request->quitted_at;
                    $model->electric_end_base = $request->electric_end_base;
                    $model->water_end_base = $request->water_end_base;
                    $model->save();
                }
            }
        });
        admin_toastr('操作成功', 'success');
        return redirect(route('admin.livings.index'));
    }

    public function getRecords($companyId)
    {
        return Record::with(['room'])->where('is_living', true)->where('company_id', $companyId)->get();
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
