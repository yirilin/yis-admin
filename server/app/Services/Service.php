<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Service
{

    protected $model;

    /**
     * 查询配置
     */
    protected $search = [];

    /**
     * 排序规则
     * key=>value
     * key为排序字段
     * value为排序规则ASC|DESC
     * 如['id'=>'ASC','sort'=>'ASC']
     */
    protected $sort = [];

    /**
     * groupBy
     * 如：['title','name']
     */
    protected $group = [];

    /**
     * 添加数据
     * @param array $data
     * @return bool
     */
    public function create(array $data){
        try {
            return $this->model->fill($data)->save();
        } catch (\Exception $ex) {
            Log::error('create data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 批量添加数据
     * @param array $data
     * @return bool
     */
    public function createAll($data = array(array())){
        if (!empty($data)) {
            try {
                return $this->model->insert($data);
            } catch (\Exception $ex) {
                Log::error('createAll data error:',[$ex->getMessage()]);
            }
        }
        return false;
    }

    /**
     * 指定ID删除数据
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            if(is_array($id)){
                return $this->updateBatch($id);
            }else{
                $model = $this->model->find($id);
                return $model->update(array('is_delete'=>1));
            }
        } catch (\Exception $ex) {
            Log::error('destroy data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 指定ID更新数据
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $model = $this->model->find($id);
            $result = $model->update($data);
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            Log::error('update data error:',[$ex->getMessage()]);
        }
        DB::rollBack();
        return false;
    }

    /**
     * 批量更新
     * @param array $multipleData
     * @return bool
     */
    public function updateBatch(array $multipleData = [])
    {
        if (empty($multipleData)) {
            return 0;
        }
        $tableName = $this->model->table;
        $firstRow = current($multipleData);
        $updateColumn = array_keys($firstRow);
        // 默认以id为条件更新，如果没有ID则以第一个字段为条件
        $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
        unset($updateColumn[0]);
        $updateSql = "UPDATE " . 'ys_'. $tableName . " SET ";
        $sets = [];
        $bindings = [];
        foreach ($updateColumn as $uColumn) {
            $setSql = "`" . $uColumn . "` = CASE ";
            foreach ($multipleData as $data) {
                $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                $bindings[] = $data[$referenceColumn];
                $bindings[] = $data[$uColumn];
            }
            $setSql .= "ELSE `" . $uColumn . "` END ";
            $sets[] = $setSql;
        }
        $updateSql .= implode(', ', $sets);
        $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
        $bindings = array_merge($bindings, $whereIn);
        $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
        $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
        DB::beginTransaction();
        try {
            $result = DB::update($updateSql, $bindings);
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            Log::error('updateBatch data error:',[$ex->getMessage()]);
        }
        DB::rollBack();
        return false;
    }

    /**
     * 指定ID查询数据
     * @param string $id
     * @return array|bool
     */
    public function find(string $id)
    {
        try {
            return $this->model->where('is_delete', 0)->find($id);
        } catch (\Exception $ex) {
            Log::error('find data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 获取所有数据
     * @param array $data
     * @return array|bool
     */
    public function all(array $data)
    {
        $data['is_delete'] = 0;
        try {
            return $this->model->where($data)->get()->toArray();
        } catch (\Exception $ex) {
            Log::error('all data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 获取所有数据
     * @param array $pageInfo
     * @param array $searchInfo
     * @return array|bool
     */
    public function list(array $pageInfo, array $searchInfo)
    {
        try {
            if(!empty($searchInfo)){
                $this->model = $this->makeWhere($this->model,$searchInfo);
            }
            $this->model = $this->model->where('is_delete',0);
            if (!empty($this->sort)) {
                $this->model = $this->makeSort($this->model,$this->sort);
            } else {
                $this->model = $this->model->orderBy('id','DESC');
            }
            if (!empty($this->group)) {
                $this->model = $this->model->groupBy($this->group);
            }
            return $this->model->paginate($pageInfo['pageSize'])->toArray();
        } catch (\Exception $ex) {
            Log::error('list data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 生成where条件
     * @param $filtered
     * @param $data
     * @return mixed
     */
    protected function makeWhere($filtered, $data){
        $filtered = $filtered->where(function ($query) use ($data) {
            foreach ($this->search as $key => $row) {
                if ($row) {
                    if ($key == 'and') {
                        $fun_bef = 'where';
                    } else {
                        $fun_bef = 'orWhere';
                    }
                    $query->$fun_bef(function ($query) use ($row,$data) {
                        $fun_bef = 'where';
                        foreach ($row as $name => $condition) {
                            if(isset($data[$name])){
                                if (in_array($condition, array('In', 'NotIn', 'Between', 'NotBetween'))) {
                                    $where_fun = $fun_bef . $condition;
                                    $query->$where_fun($name, explode(',', $data[$name]));
                                } else if (in_array($condition, array('IsNull', 'NotNull', 'Raw'))) {
                                    $where_fun = $fun_bef . $condition;
                                    $query->$where_fun($name);
                                } else if ($condition == 'Like') {
                                    $query->$fun_bef($name, 'like', "%{$data[$name]}%");
                                } else if ($condition == 'LikeBefore') {
                                    $query->$fun_bef($name, 'like', "{$data[$name]}%");
                                } else if ($condition == 'LikeEnd') {
                                    $query->$fun_bef($name, 'like', "%{$data[$name]}");
                                } else {
                                    $query->$fun_bef($name, $condition, $data[$name]);
                                }
                            }
                        }
                    });
                }
            }
        });
        return $filtered;
    }

    /**
     * 生成排序顺序
     * @param $filtered
     * @param $sort
     * @return mixed
     */
    protected function makeSort($filtered, $sort){
        foreach ($sort as $key => $value) {
            $filtered = $filtered->orderBy($key, $value);
        }
        return $filtered;
    }

    /**
     * 生成sql语句
     * @param $filtered
     * @return string
     */
    protected function getQuerySql($filtered){
        $sql = str_replace('?','%s',$filtered->toSql());
        return vsprintf($sql,$filtered->getBindings());
    }

    /**
     * 执行原生语句
     * @param $sql
     * @param array $data
     * @param string $method
     * @return mixed
     */
    public function execute($sql,$data=array(),$method='select'){
        return DB::$method($sql, $data);
    }

}
