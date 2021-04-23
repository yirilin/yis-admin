<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\AutoCodeRequest;
use App\Services\System\AutoCodeService;
use Symfony\Component\HttpFoundation\Response;

class AutoCodeController extends Controller
{
    protected $server;

    public function __construct(AutoCodeService $server)
    {
        $this->server = $server;
    }

    /**
     * 自动生成所有代码
     * @param AutoCodeRequest $request
     * @return Response
     */
    public function autoCode(AutoCodeRequest $request)
    {
        $data = $request->all();
        $result = $this->server->autoCodeNew(array_filter($data,function($var){return !($var===''||$var===null);}));
        if($result[0]){
            $result = $this->success(Response::HTTP_OK, '生成代码成功！', $result[2]);
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, $result[1]);
        }
        return response()->json($result);
    }

    /**
     * 获取所有DB
     * @return Response
     */
    public function getDB()
    {
        $result = $this->server->getDB();
        if($result){
            $result = $this->success(Response::HTTP_OK, '获取DB列表成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }

    /**
     * 获取所有Tables
     * @param AutoCodeRequest $request
     * @return Response
     */
    public function getTables(AutoCodeRequest $request)
    {
        $data = $request->all();
        $result = $this->server->getTables($data['dbName']);
        if($result){
            $result = $this->success(Response::HTTP_OK, '获取表成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }

    /**
     * 获取所有Column
     * @param AutoCodeRequest $request
     * @return Response
     */
    public function getColumn(AutoCodeRequest $request)
    {
        $data = $request->all();
        $result = $this->server->getColumn($data);
        if($result){
            $result = $this->success(Response::HTTP_OK, '获取列数据成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }
}
