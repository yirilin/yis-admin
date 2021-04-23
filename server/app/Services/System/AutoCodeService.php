<?php

namespace App\Services\System;

use App\Services\Service;
use App\Utils\VariableConversion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCodeService extends Service
{

    /**
     * 自动创建所有文件
     * @param array $data
     * @return array
     */
    public function autoCodeNew(array $data)
    {
        /** 配置文件 */
        $namespace_prefix = $data['spacePrefix'];

        $autoCodeConfig = [
            [
                'type' => "Controller",
                'file' => "Controller.php",
                'end'  => 'back',
                'path' => base_path() . "/app/Http/Controllers/".$namespace_prefix.'/',
                'name' => "控制器"
            ],
            [
                'type' => "Model",
                'file' => "Model.php",
                'end'  => 'back',
                'path' => base_path() . "/app/Models/",
                'name' => "模型"
            ],
            [
                'type' => "Service",
                'file' => "Service.php",
                'end'  => 'back',
                'path' => base_path() . "/app/Services/",
                'name' => "服务"
            ],
            [
                'type' => "Api",
                'file' => ".js",
                'end'  => 'front',
                'path' => $this->adminPath()."/src/api/",
                'name' => "前端API接口"
            ],
            [
                'type' => "index",
                'file' => ".vue",
                'end'  => 'front',
                'path' => $this->adminPath()."/src/view/",
                'name' => "前端页面"
            ],
            [
                'type' => "Route",
                'file' => "Route.php",
                'end'  => 'back',
                'path' => base_path() . "/routes/",
                'name' => "路由"
            ],
        ];

        //模板文件路径
        $tmpPath = base_path() . "/resources/Template/";
        //命名空间路径
        $nameSpacePath =  $data['nameSpace'] . "/";
        // 返回结果
        $result = [true, '', ''];

        $file_exists_num = 0;

        /** 业务代码 */
        //文件生成处理
        foreach ($autoCodeConfig as $value) {
            if($value['path']==null){
                $file_exists_num++;
                continue;
            }
            if (in_array($value['type'], ['Api', 'index'])) {
                $middlePath = $value['type'] === 'Api' ? $data['apiName'] :$data['apiName'] . "/index";
            }else{
                $middlePath =  $data['nameSpace'] . "/" . $data['className'];
            }
            if (file_exists($value['path'] . $middlePath . $value['file'])) {
                $file_exists_num++;
                $result[1] .= $value['path'] . $middlePath . $value['file']."\r\n";
                continue;
            }
            if($file_exists_num==5){
                $result[0] = false;
                $result[1] .= '文件已存在，请检查！';
                break;
            }
            //获取模板文件内容
            $tmpCtrollerPath = $tmpPath . $value['type'] . ".tpl";
            $tmpContent = file_get_contents($tmpCtrollerPath);

            //替换文件内容
            $newContent = str_replace("{{nameSpacePrefix}}", $namespace_prefix, $tmpContent);
            $newContent = str_replace("{{nameSpace}}", $data['nameSpace'], $newContent);

            $newContent = str_replace("{{Template}}", $data['className'], $newContent);

            //model需要特殊处理
            if ($value['type'] == "Model") {
                $newContent = str_replace("{{table}}", $data['tableName'], $newContent);         //替换表名
                $newContent = str_replace("{{primaryKey}}", $data['primaryKey'], $newContent);   //替换主键
                $newContent = str_replace("{{columns}}", $data['columns'], $newContent);         //替换模型列数据
            }

            //Service需要特殊处理
            if ($value['type'] == "Service") {
                $search = array();
                foreach ($data['fields'] as $filed) {
                    if ($filed['fieldSearchType']){
                        $search = array_merge($search,array("{$filed['columnName']}'=>'{$filed['fieldSearchType']}"));
                    }
                }
                $newContent = str_replace("{{search}}", VariableConversion::convertArrayToString($search,3), $newContent);
            }

            //Route和Api需要特殊处理
            if ($value['type'] == "Api" || $value['type'] == "Route") {
                $newContent = str_replace("{{apiName}}", $data['apiName'], $newContent);
                if($value['type'] == "Route"){
                    $result[2] = $newContent;
                }
            }

            //VUE文件特殊处理
            if ($value['type'] == "index") {
                $newContent = $this->indexVueContent($newContent, $data['fields'], $data);
            }

            //检测命名空间文件夹|不存在则创建
            $dir_path = $value['end']=='back'?$value['path'] . $nameSpacePath:$value['path'];
            if (!is_dir($dir_path)&&$value['type'] != "Route") {
                mkdir($dir_path);
            }
            //前台检查模块文件夹|不存在则创建
            if (in_array($value['type'], ['index']) &&  !is_dir($value['path'] . $data['apiName'])) {
                mkdir($value['path'] . $data['apiName']);
            }

            //自动化代码实现
            if (in_array($value['type'], ['Controller', 'Model', 'Service', 'Api', 'index'])) {//'Route'
                if (($myFile = fopen($value['path'] . $middlePath . $value['file'], "w+")) === false) {
                    $result[0] = false;
                    $result[1] = "autoCode创建文件失败，请检查权限！";
                    break;
                }
                fwrite($myFile, $newContent);
                fclose($myFile);
            }

        }

        return $result;
    }

    /**
     * VUE文件内容循环处理
     * @param string $newContent
     * @param array $fileds
     * @param array $data
     * @return string $newContent
     */
    private function indexVueContent(string $newContent, array $fileds, array $data)
    {
        /** 1.替换搜索表单 */
        $searchTmp = "<el-form-item><el-input v-model=\"searchInfo.{{fieldName}}\" placeholder=\"{{fieldDesc}}\" clearable :style=\"{ width: '100%' }\"></el-input></el-form-item>".PHP_EOL."\t\t\t\t";
        $searchRepalce = "";

        /** 2.替换表单字段 */
        $tableTmp = "<el-table-column label=\"{{fieldDesc}}\" prop=\"{{fieldName}}\" min-width=\"120\"></el-table-column>".PHP_EOL."\t\t\t";
        $tableRepalce = "";

        /** 3.替换弹窗表单 */
        $formTmp = "<el-form-item label=\"{{fieldDesc}}\" prop=\"{{fieldName}}\"><el-input v-model=\"formData.{{fieldName}}\" placeholder=\"请输入{{fieldDesc}}\" clearable :style=\"{ width: '100%' }\"></el-input></el-form-item>".PHP_EOL."\t\t\t\t";
        $formRepalce = "";

        /** 4.替换formData*/
        $formData = "";

        /** 5.替换校验规则(必填)*/
        $ruleTmp = "{{fieldName}}: [{ required: true, message: \"请输入{{fieldDesc}}\", trigger: \"blur\",}],".PHP_EOL."\t\t\t\t";
        $ruleRepalce = "";

        foreach ($fileds as $filed) {
            //搜索表单生成
            if ($filed['fieldSearchType']) {
                $searchOne = str_replace("{{fieldName}}", $filed['columnName'], $searchTmp);
                $searchOne = str_replace("{{fieldDesc}}", $filed['fieldDesc'], $searchOne);
            } else {
                $searchOne = "";
            }
            $searchRepalce .= $searchOne;

            //数据表格生成
            $tableOne = str_replace("{{fieldName}}", $filed['columnName'], $tableTmp);
            $tableOne = str_replace("{{fieldDesc}}", $filed['fieldDesc'], $tableOne);
            $tableRepalce .= $tableOne;

            //弹窗表单生成
            $formOne = str_replace("{{fieldName}}", $filed['columnName'], $formTmp);
            $formOne = str_replace("{{fieldDesc}}", $filed['fieldDesc'], $formOne);
            $formRepalce .= $formOne;

            //生成formData
            $formData .= $filed['fieldName'] . ":null,";

            //生成表单验证规则
            $ruleOne = str_replace("{{fieldName}}", $filed['columnName'], $ruleTmp);
            $ruleOne = str_replace("{{fieldDesc}}", $filed['fieldDesc'], $ruleOne);
            $ruleRepalce .= $ruleOne;
        }

        //集中替换
        $newContent = str_replace("{{search}}", $searchRepalce, $newContent);
        $newContent = str_replace("{{table}}", $tableRepalce, $newContent);
        $newContent = str_replace("{{form}}", $formRepalce, $newContent);
        $newContent = str_replace("{{formData}}", "{" . $formData . "}", $newContent);
        $newContent = str_replace("{{rules}}", "{" . $ruleRepalce . "}", $newContent);

        /** 6.替换API请求引入 */
        $newContent = str_replace("{{apiName}}", $data['apiName'], $newContent);
        /** 7.替换类名 */
        $newContent = str_replace("{{className}}", $data['className'], $newContent);

        return $newContent;
    }

    /**
     * 相对admin文件路径
     * @return string $adminPath
     */
    private function adminPath()
    {
        $backPath = base_path();
        $backPath = substr($backPath, 0, -7) . "/admin";
        return $backPath;
    }

    /**
     * 获取所有DB
     * @return array|false
     */
    public function getDB()
    {
        try {
            $dbs = DB::select('SHOW DATABASES');
            $result = [];
            foreach ($dbs as $value) {
                //过滤掉系统自带
                if (!in_array($value->Database, ['information_schema', 'default', 'mysql', 'performance_schema', 'sys'])) {
                    $result[] = ['database' => $value->Database];
                }
            }
            return ['dbs' => $result];
        } catch (\Exception $ex) {
            Log::error('getDB data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 根据库名找到所有表
     * @param string $dbName
     * @return array|false
     */
    public function getTables(string $dbName)
    {
        try {
            $query = "SELECT table_name FROM information_schema.tables WHERE TABLE_SCHEMA='$dbName'";
            $tables = DB::select($query);
            $result = [];
            foreach ($tables as $value) {
                $result[] = ['tableName' => $value->table_name];
            }
            return ['tables' => $result];
        } catch (\Exception $ex) {
            Log::error('getTables data error:',[$ex->getMessage()]);
        }
        return false;
    }

    /**
     * 根据表名找到所有列
     * @param array $data[dbName,tableName]
     * @return array|false
     */
    public function getColumn(array $data)
    {
        $dbName = $data['dbName'];
        $tableName = $data['tableName'];
        try {
            $query = "SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME = '$tableName' ";
            $columns = DB::select($query);
            $result = [];
            foreach ($columns as $value) {
                $result[] = [
                    'columnComment' => $value->COLUMN_COMMENT,
                    'columnName' => $value->COLUMN_NAME,
                    'dataType' => $value->DATA_TYPE,
                    'dataTypeLong' => $value->CHARACTER_MAXIMUM_LENGTH,
                ];
            }
            return ['columns' => $result];
        } catch (\Exception $ex) {
            Log::error('getColumn data error:',[$ex->getMessage()]);
        }
        return false;
    }
}
