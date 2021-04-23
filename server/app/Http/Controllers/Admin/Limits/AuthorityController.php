<?php

namespace App\Http\Controllers\Admin\Limits;

use App\Http\Controllers\Controller;
use App\Services\Limits\AuthorityService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorityController extends Controller
{
    protected $server;

    public function __construct(AuthorityService $server)
    {
        $this->server = $server;
    }

    /**
     * 指定ID删除
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $result = $this->server->destroy($id);
        if($result===-1) {
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '含有子角色，请先删除子角色！');
        }else if($result){
            $result = $this->success(Response::HTTP_OK, '删除成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '删除失败。');
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
        $result = $this->server->list([], []);
        if($result){
            $result = $this->success(Response::HTTP_OK, '获取数据成功！', ['list'=>$result]);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }
}
