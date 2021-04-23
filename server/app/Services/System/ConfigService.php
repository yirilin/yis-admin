<?php

namespace App\Services\System;

use App\Services\Service;
use Illuminate\Support\Facades\DB;
use App\Models\System\ConfigModel;
use App\Models\System\ConfigValueModel;
use Illuminate\Support\Facades\Log;

class ConfigService extends Service
{

    protected $model;

    protected $configValueModel;

    /**
     * 查询配置
     */
    protected $search = array(
        'and'=>array(
            'title'=>'Like',
            'name'=>'Like',
            'status'=>'='
        ),
        'or'=>array()
    );

    protected $sort = ['id'=>'ASC'];

    public function __construct(ConfigModel $model, ConfigValueModel $configValueModel)
    {
        $this->model = $model;
        $this->configValueModel = $configValueModel;
    }

    /**
     * 根据父级ID或者Type获取子集所有数据
     * @param string $info
     * @return array|bool
     */
    public function find(string $info)
    {
        try {
            if (is_numeric($info)) {
                $result = $this->model->where('is_delete',0)->where('id', $info)->first();
            } else {
                $result = $this->model->where('is_delete',0)->where('name', $info)->with('configValues')->first();
            }
            if($result){
                $result = ["configValue" => $result];
            }
            return $result;
        } catch (\Exception $ex) {
            Log::error('find config error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 指定ID删除数据连带子集中的数据
     * @param $id
     * @return bool|int
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // 删除父级数据
            $model = $this->model->find($id);
            $result = $model->update(array('is_delete'=>1));
            if($result){
                // 删除子集中的数据
                $result = $this->configValueModel->where('config_id', $id)->update(array('is_delete'=>1));
                if($result){
                    DB::commit();
                    return $result;
                }
            }
        } catch (\Exception $ex) {
            Log::error('destroy config error:',[$ex->getMessage()]);
        }
        DB::rollBack();
        return false;
    }
}
