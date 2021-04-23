<?php

namespace App\Services\Limits;

use App\Utils\Tree;
use App\Services\Service;
use App\Models\Limits\AuthorityModel;
use Illuminate\Support\Facades\Log;


class AuthorityService extends Service
{
    protected $model;

    public function __construct(AuthorityModel $model)
    {
        $this->model = $model;
    }

    /**
     * 重写指定ID删除数据
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            // 查询是否有子角色
            $hasChild = $this->model->where('pid', $id)->where('is_delete', 0)->get()->toArray();
            if (count($hasChild) > 0) {
                return -1;
            }
            $model = $this->model->find($id);
            return $model->update(array('is_delete'=>1));
        } catch (\Exception $ex) {
            Log::error('destroy data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 重写获取所有角色
     * @param array $pageInfo
     * @param array $searchInfo
     * @return mixed|array|bool
     */
    public function list(array $pageInfo, array $searchInfo)
    {
        try {
            $result = $this->model->where('is_delete',0)->get()->toArray();
            return Tree::makeTree($result, ['primary_key' => 'id']);
        } catch (\Exception $ex) {
            Log::error('list data error:',[$ex->getMessage()]);
        }
        return false;
    }
}
