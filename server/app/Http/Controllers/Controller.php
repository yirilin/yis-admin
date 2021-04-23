<?php

namespace App\Http\Controllers;

use App\Utils\ResultHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResultHelper;

    /**
     * 公共方法
     */
    protected $server;

    /**
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $result = $this->server->create(array_filter($data,function($var){return !($var===''||$var===null);}));
        if($result){
            $result = $this->success(Response::HTTP_OK, '添加成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '添加失败。');
        }
        return response()->json($result);
    }

    /**
     * 指定ID删除
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        // 判断如果是数组则是批量删除
        if (substr($id, 0, 1) == "[" && substr($id, -1, 1) == "]") {
            $id = substr($id, 1, strlen($id) - 2);
            $id = explode(",", $id);
            $temp = [];
            $time = time();
            foreach ($id as $row){
                $temp[] = ['id'=>$row,'updated_at'=>$time,'is_delete'=>'1'];
            }
            $id = $temp;
        }
        $result = $this->server->destroy($id);
        if($result){
            $result = $this->success(Response::HTTP_OK, '删除成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '删除失败。');
        }
        return response()->json($result);
    }

    /**
     * 指定ID更新
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $result = $this->server->update($id, array_filter($data,function($var){return !($var===''||$var===null);}));
        if($result){
            $result = $this->success(Response::HTTP_OK, '编辑成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '编辑失败。');
        }
        return response()->json($result);
    }

    /**
     * 指定ID查找
     * @param string $id
     * @return Response
     */
    public function find(string $id)
    {
        $result = $this->server->find($id);
        if($result){
            $result = $this->success(Response::HTTP_OK, '查询成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }

    /**
     * 获取所有数据
     * @param Request $request
     * @return Response
     */
    public function all(Request $request)
    {
        $data = $request->all();
        $result = $this->server->all($data);
        if($result){
            $result = $this->success(Response::HTTP_OK, '获取数据成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }

    /**
     * 获取所有分页数据
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $params = $request->all();
        $this->pageInfo['page'] = $params['page'] ?? 1;
        $this->pageInfo['pageSize'] = $params['pageSize'] ?? 10;
        unset($params['page'],$params['pageSize']);
        $result = $this->server->list($this->pageInfo, $params);
        if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else if($result['total']){
            $result = $this->tableData(Response::HTTP_OK, '获取数据成功！', $result);
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }
}
