<?php

namespace App\Http\Controllers\Admin\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\System\FileService;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    protected $server;

    public function __construct(FileService $server)
    {
        $this->server = $server;
    }

    /**
     * 上传文件
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request){
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $result = $this->failed(Response::HTTP_BAD_REQUEST, '文件上传失败');
        if ($file) {
            $dir = base_path() . "/public/upload/images/";
            if (!is_array($filename)&&$file->getSize()<20481000) {
                $time = time();
                $ext = $file->getClientOriginalExtension();;
                $types = 'gif|jpeg|jpg|png|bmp|xls|xlsx|doc|docx|ppt|pptx|pdf|txt';
                if (stripos($types, $ext)) {
                    $name = md5($dir.$time.$filename);
                    $data['name'] = $name.'.'.$ext;
                    $path=$file->move($dir,$data['name']);
                    if ($path) {
                        $data['path'] = $dir;
                        $data['url'] = env('APP_URL').'/upload/images/'.$data['name'];
                        $data['tag'] = $filename;
                        $res = $this->server->create($data);
                        if($res){
                            $result = $this->success(Response::HTTP_OK, '文件上传成功！',$data['url']);
                        }
                    }
                    $result['msg'] = '文件上传失败';
                } else {
                    $result['msg'] = '文件格式不正确';
                }
            } else {
                $result['msg'] = '文件不得大于1M';
            }
        } else {
            $result['msg'] = '没有文件';
        }
        return response()->json($result);
    }

    /**
     * 下载文件
     * @param $file_id
     * @return array|Response
     */
    public function download($file_id){
        $result = $this->server->find($file_id);
        if($result['success']){
            return response()->download($result['path'].$result['name'], $result['name']);
        }else {
            return $this->server->failed(Response::HTTP_BAD_REQUEST, '文件加载失败');
        }
    }
}
