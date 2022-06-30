<?php

namespace Openphp\ElasticTable;

class Resault
{

    /**
     * 将数据结构重新生成，方便快速建表和更新表结构
     * @param array $datas
     * @return array[]
     */
    public static function datasTable(array $datas)
    {
        $master = [];
        $child  = [];
        foreach ($datas as $k => $data) {
            foreach ($data as $f => $val) {
                if (!is_array($val)) {
                    $master[$k][$f] = $val;
                } else {
                    $child[$f] = [];
                }
            }
        }
        array_walk($child, function (&$v, $k) use (&$datas) {
            $v = array_column($datas, $k);
        });
        return array_merge(['master' => $master], $child);
    }

    /**
     * 根据datasTable生成函数，存在master表与其他表存在一对多的关系的数据进行组合
     *  方便快速建表和更新表结构
     * @param array $filedVals
     * @return array
     */
    public static function moreDimensionFiledVals(array $filedVals)
    {
        $newArray = [];
        foreach ($filedVals as $v) {
            $newArray = array_merge($newArray, $v);
        }
        return $newArray;
    }


    /**
     * 生成字段和数据对应多数据
     * @param      $data
     * @param bool $continueArray
     * @return void
     */
    public static function dataFiledVal(&$data, $continueArray = true)
    {
        foreach ($data as $k => &$val) {
            if (is_bool($val)) {
                $val = $val == true ? 1 : 0;
            }
            if (is_array($val) && $continueArray) {
                unset($data[$k]);
            } else {
                $val = json_encode($val);
            }
        }
    }

    /**
     * 二维数据进行重新处理
     * @param array $datas
     * @param bool  $continueArray
     * @return array
     */
    public static function datasFiledVal(array $datas, $continueArray = true)
    {
        $filed_len = [];
        foreach ($datas as $i => $datum) {
            foreach ($datum as $filed => $val) {
                if (is_array($val) && $continueArray) {
                    continue;
                }
                $filed_len[$filed][$i] = mb_strlen($val);
            }
        }
        $fileds = [];
        foreach ($filed_len as $f => $item) {
            $index      = array_search(max($item), $item);
            $fileds[$f] = $datas[$index][$f];
        }
        return $fileds;
    }
}