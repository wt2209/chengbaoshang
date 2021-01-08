<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Record;
use App\Models\Room;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }

    public function migrate()
    {
        $companies = DB::connection('old_cbs')->table('company')->orderBy('is_quit', 'desc')->get()->toArray();
        $data = [];
        $categories = Category::get()->toArray();
        $categoryMapper = [];
        $categoryIdMapper = [];
        foreach ($categories as $category) {
            $categoryMapper[$category['title']] = $category;
        }
        foreach ($categories as $category) {
            $categoryIdMapper[$category['id']] = $category;
        }
        foreach (array_values($companies) as $company) {
            $data[$company->company_name] = [
                'company_name' => $company->company_name,
                'category_id' => $categoryMapper[$company->belongs_to]['id'],
                'manager' => $company->manager,
                'manager_phone' => $company->manager_tel,
                'linkman' => $company->linkman,
                'linkman_phone' => $company->linkman_tel,
                'remark' => $company->company_remark,
                'created_at' => $company->created_at === '0000-00-00 00:00:00' ? now() : $company->created_at,
                'updated_at' => $company->updated_at === '0000-00-00 00:00:00' ? now() : $company->updated_at,
            ];
            if ($categoryIdMapper[$data[$company->company_name]['category_id']]['has_lease']) {
                $data[$company->company_name]['lease_start'] = '2021-1-1';
                $data[$company->company_name]['lease_end'] = '2021-6-30';
            } else {
                $data[$company->company_name]['lease_start'] = null;
                $data[$company->company_name]['lease_end'] = null;
            }
        }
        DB::table('companies')->insert($data);


        $oldRecords = DB::connection('old_cbs')
            ->table('records')
            ->join('room', 'records.room_id', '=', 'room.room_id')
            ->join('company', 'records.company_id', '=', 'company.company_id')
            ->select(
                DB::raw('cbs_company.company_name company_name'),
                DB::raw('cbs_company.belongs_to company_belongs_to'),
                DB::raw('cbs_records.belongs_to records_belongs_to'),
                DB::raw('cbs_room.room_name room_name'),
                DB::raw('cbs_records.gender gender'),
                DB::raw('cbs_room.room_remark room_remark'),
                DB::raw('cbs_records.price price'),
                DB::raw('cbs_records.enter_electric_base electric_start_base'),
                DB::raw('cbs_records.enter_water_base water_start_base'),
                DB::raw('cbs_records.quit_electric_base electric_end_base'),
                DB::raw('cbs_records.quit_water_base water_end_base'),
                DB::raw('cbs_records.in_use in_use'),
                DB::raw('cbs_records.entered_at entered_at'),
                DB::raw('cbs_records.quit_at quit_at'),
                DB::raw('cbs_records.created_at created_at'),
                DB::raw('cbs_records.updated_at updated_at')
            )
            ->get();

        $newCompanies = Company::pluck('id', 'company_name')->toArray();

        foreach ($oldRecords as $record) {
            $categoryId =  $record->records_belongs_to
                ? $categoryMapper[$record->records_belongs_to]['id']
                : $categoryMapper[$record->company_belongs_to]['id'];

            $deposit = 2000;
            if (strpos($record->room_remark, '1000') !== false) {
                $deposit = 1000;
            }
            if (strpos($record->room_remark, '1200') !== false) {
                $deposit = 1200;
            }
            if ($record->entered_at === null || $record->entered_at === '0000-00-00 00:00:00') {
                continue;
            }
            $data = [
                'company_id' => $newCompanies[$record->company_name],
                'category_id' => $categoryId,
                'entered_at' => $record->entered_at,
                'has_lease' => $categoryIdMapper[$categoryId]['has_lease'],
                'room_id' => $this->getRoomIdByTitle($record->room_name),
                'gender' => $record->gender == 1 ? '男' : '女',
                'deposit_money' => $deposit,
                'rent' => $record->price,
                'electric_start_base' => $record->electric_start_base,
                'water_start_base' => $record->water_start_base,
                'is_living' => $record->in_use,
            ];
            if ($record->created_at && $record->created_at !== '0000-00-00 00:00:00') {
                $data['created_at'] = $record->created_at;
            } else {
                $data['created_at'] = null;
            }
            if ($record->updated_at && $record->updated_at !== '0000-00-00 00:00:00') {
                $data['updated_at'] = $record->updated_at;
            } else {
                $data['updated_at'] = null;
            }

            if ($data['has_lease']) {
                $data['lease_start'] = '2021-1-1';
                $data['lease_end'] = '2021-6-30';
            }
            if ($data['is_living'] == 0) {
                $data['quitted_at'] = $record->quit_at;
                $data['electric_end_base'] = $record->electric_end_base;
                $data['water_end_base'] = $record->water_end_base;
            }
            // 不需要触发Observer
            $record = DB::table('records')->insert($data);
        }
        return 'ok';
    }

    protected function getRoomIdByTitle($title)
    {
        if (in_array(mb_substr($title, 0, 1), ['1', '2', '3', '4'])) {
            $room = mb_substr($title, 0, 1) . '-' .  intval(mb_substr($title, 1, 4));
        } else {
            $room = $title;
        }
        return Room::where('title', $room)->value('id');
    }
}
