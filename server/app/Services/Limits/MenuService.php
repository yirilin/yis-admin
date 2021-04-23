<?php

namespace App\Services\Limits;

use App\Utils\Tree;
use App\Models\Limits\MenuModel;
use App\Models\Limits\AuthorityModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MenuService
{

    protected $menuModel;

    protected $authorityModel;

    public function __construct(MenuModel $menuModel, AuthorityModel $authorityModel)
    {
        $this->menuModel = $menuModel;
        $this->authorityModel = $authorityModel;
    }

    /**
     * 添加菜单
     * @param array $data
     * @return bool
     */
    public function create(array $data){
        try {
            return $this->menuModel->fill($data)->save();
        } catch (\Exception $ex) {
            Log::error('create menus error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 指定ID删除菜单
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            $model = $this->menuModel->find($id);
            return $model->update(array('is_delete'=>1));
        } catch (\Exception $ex) {
            Log::error('destroy menus error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 指定ID更新菜单
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $model = $this->menuModel->find($id);
            $result = $model->update($data);
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            Log::error('update menus error:',[$ex->getMessage()]);
        }
        DB::rollBack();
        return false;
    }

    /**
     * 指定ID查询菜单
     * @param string $id
     * @return array|bool
     */
    public function find(string $id)
    {
        try {
            return $this->menuModel->where('is_delete', 0)->find($id);
        } catch (\Exception $ex) {
            Log::error('find menus error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 获取所有菜单
     * @param array $data
     * @return array|bool
     */
    public function all(array $data)
    {
        $data['is_delete'] = 0;
        try {
            $result = $this->menuModel->where($data)->orderBy('sort')->get()->toArray();
            $result = Tree::makeTree($result);
            return ["menus" => $result];
        } catch (\Exception $ex) {
            Log::error('all menus error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 获取当前用户权限下的所有菜单
     * @return array|false|int
     */
    public function async()
    {
        try {
            $user = auth()->user();
            $AuthorInfo = $this->authorityModel->where('id', $user['id'])->where('is_delete', 0)->first(['menu_ids']);
            if($AuthorInfo){
                $ids = $AuthorInfo->menu_ids;
                $result = $this->menuModel->whereIn('id',$ids)->where('is_delete',0)->orderBy('sort')->get()->toArray();
                $result = Tree::makeTree($result);
                return ["menus" => $result];
            }else{
                return -1;//"账号权限错误。"
            }
        } catch (\Exception $ex) {
            Log::error('async menus error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 获取所有菜单
     * @param array $data
     * @return array|false|mixed
     */
    public function list(array $data)
    {
        try {
            $result = $this->menuModel->where($data)->where('is_delete',0)->orderBy('sort')->get()->toArray();
            return Tree::makeTree($result);
        } catch (\Exception $ex) {
            Log::error('list data error:',[$ex->getMessage()]);
        }
        return false;
    }
}
