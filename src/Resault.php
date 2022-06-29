<?php

namespace Openphp\ElasticTable;

class Resault
{

    /**
     * @param array $array
     * @param string $colum
     * @return array
     */
    public static function column(array $array, string $colum)
    {
        $newArray = [];
        foreach (array_column($array, $colum) as $v) {
            $newArray = array_merge($newArray, $v);
        }
        return $newArray;
    }

    /**
     * @param $data
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
     * @param array $datas
     * @param bool $continueArray
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