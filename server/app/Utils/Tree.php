<?php

namespace App\Utils;

class Tree
{
    protected static $config = array(
        'primary_key' => 'id',
        'parent_key' => 'pid',
        'expanded_key' => 'expanded',
        'leaf_key' => 'leaf',
        'children_key' => 'children',
        'expanded' => false
    );

    protected static $result = array();

    protected static $level = array();

    /**
     * 生成树形结构
     * @param $data
     * @param array $options 二维数组
     * @return mixed 多维数组
     */
    public static function makeTree($data, $options = array())
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if ($value['pid'] == null) {
                    $data[$key]['pid'] = "0";
                }
            }
            $dataSet = self::buildData($data, $options);
            return self::makeTreeCore("0", $dataSet, 'normal');
        } else {
            return $data;
        }
    }

    private static function buildData(array $data, array $options)
    {
        $config = array_merge(self::$config, $options);
        self::$config = $config;
        extract($config);
        $res = array();
        foreach ($data as $item) {
            $id = $item[self::$config['primary_key']];
            $parent_id = $item[self::$config['parent_key']];
            $res[$parent_id][$id] = $item;
        }
        return $res;
    }

    private static function makeTreeCore($index, $data, $type = 'linear')
    {
        extract(self::$config);
        $res = array();
        foreach ($data[$index] as $id => $item) {
            if ($type == 'normal') {
                if (isset($data[$id])) {
                    $item[self::$config['expanded_key']] = self::$config['expanded'];
                    $item[self::$config['children_key']] = self::makeTreeCore($id, $data, $type);
                } else {
                    $item[self::$config['leaf_key']] = true;
                }
                $res[] = $item;
            } else if ($type == 'linear') {
                $parent_id = $item[self::$config['parent_key']];
                self::$level[$id] = $index == 0 ? 0 : self::$level[$parent_id] + 1;
                $item['level'] = self::$level[$id];
                self::$result[] = $item;
                if (isset($data[$id])) {
                    self::makeTreeCore($id, $data, $type);
                }
                $res = self::$result;
            }
        }
        return $res;
    }

    public static function makeTreeForHtml($data, $options = array())
    {
        $dataSet = self::buildData($data, $options);
        return self::makeTreeCore(0, $dataSet, 'linear');
    }
}
