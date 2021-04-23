<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class AutoCodeRequest extends BaseRequest
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

    public function rules()
    {
        switch ($this->method()) {
            case 'GET': {
                    return [];
                }
            case 'POST': {
                    return [
                        'spacePrefix' => 'required',
                        'nameSpace' => 'required',
                        'className' => 'required',
                        'apiName' => 'required',
                        'tableName' => 'required',
                        'primaryKey' => 'required',
                        'columns' => 'required',
                    ];
                }
            case 'PUT': {
                    return [];
                }
            case 'PATCH': {
                    return [];
                }
            case 'DELETE': {
                    return [];
                }
            default: {
                    return [];
                }
        }
    }

    public function messages()
    {
        return [
            'spacePrefix.required'  => '模块前缀为必填！',
            'nameSpace.required'  => '模块为必填！',
            'className.required'  => '类名为必填！',
            'apiName.required'    => '类小驼峰名为必填！',
            'tableName.required'  => '表名为必填！',
            'primaryKey.required' => '主键为必填！',
            'columns.required'    => '表列为必填！',
            'autoCode.required'   => '是否生成本地代码为必填！',
        ];
    }
}
