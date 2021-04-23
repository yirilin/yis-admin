<?php

namespace App\Services\Limits;

use App\Models\Limits\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserService
{

    protected $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * 用户注册
     * @param array $data
     * @return bool|int
     */
    public function register(array $data)
    {
        try {
            $check = $this->userModel->where(['username' => $data['username'],'is_delete' => 0])->first();
            if ($check) {
                return -1;//"用户名重复，请更换"
            }
            return $this->userModel->fill($data)->save();
        } catch (\Exception $ex) {
            Log::error('register user error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 指定ID删除用户
     * @param $id
     * @return false
     */
    public function destroy($id)
    {
        try {
            $model = $this->userModel->find($id);
            return $model->update(array('is_delete'=>1));
        } catch (\Exception $ex) {
            Log::error('destroy user error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 指定ID更新用户
     * @param string $id
     * @param array $data
     * @return false
     */
    public function update(string $id, array $data)
    {
        try {
            $model = $this->userModel->find($id);
            return $model->update($data);
        } catch (\Exception $ex) {
            Log::error('update user error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 修改密码
     * @param array $data
     * @return false|int
     */
    public function changePassword(array $data)
    {
        try {
            $result = $this->userModel->where(['id'=>$data['id'],'username'=>$data['username'],'is_delete'=>0])->first();
            if($result){
                if (md5('a08a0e3'.$data['password'].'a7255583d') != $result->password) {
                    return -1;//"账户或密码不正确！"
                }
                $data['password'] = $data['newPassword'];
                $model = $this->userModel->find($data['id']);
                unset($data['id'],$data['username'],$data['newPassword']);
                return $model->update($data);
            }else{
                return -1;
            }
        } catch (\Exception $ex) {
            Log::error('changePassword user error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 用户重置角色
     * @param string $uuid
     * @param array $data
     * @return false
     */
    public function setAuthority(string $uuid, array $data)
    {
        try {
            return $this->userModel->where('uuid', $uuid)->update($data);
        } catch (\Exception $ex) {
            Log::error('setAuthority user error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 用户登录
     * @param array $data
     * @return false|int
     */
    public function login(array $data)
    {
        try {
            $result = $this->userModel->where(['username' => $data['username'],'is_delete' => 0])->first();
            if($result){
                if (md5('a08a0e3'.$data['password'].'a7255583d') != $result->password) {
                    return -1;//"账户或密码不正确！"
                }
                $result->token = Auth::login($result);
                return $result;
            }else{
                return -1;
            }
        } catch (\Exception $ex) {
            Log::error('login user error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 用户列表
     * @param array $pageInfo
     * @return false
     */
    public function userList(array $pageInfo)
    {
        try {
            return $this->userModel->where(['is_delete' => 0])->with('authority')->paginate($pageInfo['pageSize'])->toArray();
        } catch (\Exception $ex) {
            Log::error('userList user error:',[$ex->getMessage()]);
        }
        return false;
    }
}
