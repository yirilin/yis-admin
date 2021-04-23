<?php

namespace App\Http\Controllers\Admin\Limits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\System\UserRequest;
use App\Services\Limits\UserService;
use App\Utils\ResultHelper;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ResultHelper;

    protected $userServer;

    public function __construct(UserService $userServer)
    {
        $this->userServer = $userServer;
    }
    /**
     * 用户注册
     * @param UserRequest $request
     * @return Response
     */
    public function register(UserRequest $request)
    {
        $data = $request->all();
        $data['uuid'] = Uuid::uuid1();
        $result = $this->userServer->register(array_filter($data));
        if($result===-1){
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '用户名重复，请更换。');
        }else if($result){
            $result = $this->success(Response::HTTP_OK, '用户注册成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '用户注册失败。');
        }
        return response()->json($result);
    }

    /**
     * 指定ID删除用户
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $result = $this->userServer->destroy($id);
        if($result){
            $result = $this->success(Response::HTTP_OK, '删除用户成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '删除用户失败。');
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
        $result = $this->userServer->update($id, array_filter($data,function($var){return !($var===''||$var===null);}));
        if($result){
            $result = $this->success(Response::HTTP_OK, '编辑用户成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '编辑用户失败。');
        }
        return response()->json($result);
    }

    /**
     * 修改密码
     * @param UserRequest $request
     * @return Response
     */
    public function changePassword(UserRequest $request)
    {
        $data = $request->all();
        $result = $this->userServer->changePassword(array_filter($data));
        if($result===-1){
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '账户或密码不正确！');
        }else if($result){
            $result = $this->success(Response::HTTP_OK, '用户注册成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '用户注册失败。');
        }
        return response()->json($result);
    }

    /**
     * 用户重置角色
     * @param Request $request
     * @param $uuid
     * @return Response
     */
    public function setAuthority(Request $request, $uuid)
    {
        $data = $request->all();
        $result = $this->userServer->setAuthority($uuid, array_filter($data));
        if($result){
            $result = $this->success(Response::HTTP_OK, '重置角色成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '重置角色失败。');
        }
        return response()->json($result);
    }

    /**
     * 用户登录
     * @param UserRequest $request
     * @return Response
     */
    public function login(UserRequest $request)
    {
        $data = $request->all();
        $result = $this->userServer->login(array_filter($data));
        if($result===-1){
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '账户或密码不正确！');
        }else if($result){
            $result = $this->success(Response::HTTP_OK, '用户登录成功！', $result);
        }else if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else{
            $result = $this->failed(Response::HTTP_BAD_REQUEST, '用户登录失败。');
        }
        return response()->json($result);
    }

    /**
     * 用户列表
     * @param Request $request
     * @return Response
     */
    public function userList(Request $request)
    {
        $pageInfo = $request->all();
        $result = $this->userServer->userList($pageInfo);
        if($result===false){
            $result = $this->failed(Response::HTTP_INTERNAL_SERVER_ERROR, '服务错误,请检查日志！');
        }else if($result['total']){
            $result = $this->tableData(Response::HTTP_OK, '获取用户列表成功！', $result);
        }else{
            $result = $this->failed(Response::HTTP_OK, '没有数据。');
        }
        return response()->json($result);
    }

    /**
     * 用户登出
     */
    public function loginOut()
    {
        Auth::logout();
        $result = $this->success(Response::HTTP_OK, "退出成功");
        return response()->json($result);
    }
}
