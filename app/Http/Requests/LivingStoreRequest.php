<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Company;
use App\Models\Record;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class LivingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 只有空房间才能入住
        $unusedRoomIds = Room::where('is_using', true)
            ->whereDoesntHave('records', function (Builder $query) {
                $query->where('is_living', true);
            })
            ->pluck('id')
            ->toArray();

        return [
            'company_id' => [
                'required',
                'integer',
                Rule::in(Company::pluck('id')->toArray()),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::in(Category::pluck('id')->toArray()),
            ],
            'entered_at' => 'required|date',
            'has_lease' => 'required|boolean',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|required_with:lease_start|after_or_equal:lease_start',
            'rooms' => 'required|array',
            'rooms.*.room_id' => [
                'required',
                'integer',
                Rule::in($unusedRoomIds),
            ],
            'rooms.*.gender' => 'required|in:男,女',
            'rooms.*.deposit' => 'required|numeric',
            'rooms.*.rent' => 'required|numeric',
            'rooms.*.electric_start_base' => 'nullable|integer',
            'rooms.*.water_start_base' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'company_id.required' => '必须选择公司',
            'company_id.integer' => '必须选择公司',
            'company_id.in' => '必须选择公司',
            'category_id.required' => '必须选择类型',
            'category_id.integer' => '必须选择类型',
            'category_id.in' => '必须选择类型',
            'entered_at.required' => '必须填写入住时间',
            'entered_at.date' => '入住时间格式错误',
            'entered_at.required' => '必须填写入住时间',
            'has_lease.required'=>'必须选择是否存在租期',
            'has_lease.boolean'=>'是否存在租期非法',
            'lease_start.date'=>'租期开始日格式错误',
            'lease_end.date'=>'租期结束日格式错误',
            'lease_end.required_with'=>'租期开始、结束日必须同时存在',
            'lease_end.after_or_equal'=>'租期结束日必须晚于租期开始日',
            'rooms.required'=>'必须选择房间',
            'rooms.array'=>'必须选择房间',
            'rooms.*.room_id.required'=>'房间id非法',
            'rooms.*.room_id.integer'=>'房间id非法',
            'rooms.*.room_id.in'=>'房间id非法',
            'rooms.*.gender.required'=>'房间必须选择性别',
            'rooms.*.gender.in'=>'房间性别非法',
            'rooms.*.deposit.required'=>'房间押金必须填写',
            'rooms.*.deposit.numeric'=>'房间押金必须是数字',
            'rooms.*.rent.required'=>'房间租金必须填写',
            'rooms.*.rent.numeric'=>'房间租金必须是数字',
            'rooms.*.electric_start_base.integer'=>'房间电表底数必须是数字',
            'rooms.*.water_start_base.integer'=>'房间水表底数必须是数字',
        ];
    }
}
