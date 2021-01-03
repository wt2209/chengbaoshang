<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LivingQuitRequest extends FormRequest
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
        return [
            'company_id' => [
                'required',
                Rule::in(Company::pluck('id')->toArray()),
            ],
            'quitted_at' => 'required|date',
            'records' => 'required|array',
            'records.*.id' => 'required|integer',
            'records.*.electric_end_base' => 'nullable|integer',
            'records.*.water_end_base' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'company_id.required'=>'必须选择一个公司',
            'company_id.in'=>'必须选择一个公司',
            'quitted_at.required'=>'必须填写退房日期',
            'quitted_at.date'=>'退房日期格式错误',
            'records.required' => '必须选择房间',
            'records.array' => '必须选择房间',
            'records.*.id.required' => '记录id非法',
            'records.*.id.integer' => '记录id非法',
            'records.*.electric_end_base.integer' => '退房电表底数必须是数字',
            'records.*.water_end_base.integer' => '退房水表底数必须是数字',
        ];
    }
}
