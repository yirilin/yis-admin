<?php

namespace App\Http\Controllers\Admin\Limits;

use App\Http\Controllers\Controller;
use App\Utils\ResultHelper;
use Illuminate\Http\Request;
use App\Services\Limits\MenuService;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    use ResultHelper;

    protected $server;

    public function __construct(MenuService $menuServer)
    {
        $this->server = $menuServer;
    }

    /**
     * 获取所有菜单数据
     * @param Request $request
     * @return Response
     */
    public function async(Request $request)
    {
        $data = $request->all();
        $result = $this->server->async($data);
        if($result===-1){
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '账号权限错误。');
        }else if($result){
            $result = $this->success(Response::HTTP_OK, '获取菜单成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }

    /**
     * 获取所有菜单分页数据
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $data = $request->all();
        $result = $this->server->list($data);
        if($result){
            $result = $this->success(Response::HTTP_OK, '获取菜单成功！', ["list" => $result]);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }
}
