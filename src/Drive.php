<?php


namespace Openphp\ElasticTable;


abstract class Drive
{
    /**
     * @var mixed
     */
    protected $connect;
    /**
     * @var array
     */
    protected $option = [];

    /**
     * Drive constructor.
     * @param $connect
     * @param array $option
     */
    public function __construct($connect, array $option = [])
    {
        $this->connect = $connect;
        $this->option  = array_merge($this->option, $option);
    }

    /**
     * 连接器
     * @return mixed
     */
    abstract public function connect();

    /**
     * 判断表是否存在
     * @param string $table
     * @return mixed
     */
    abstract public function exist(string $table);

    /**
     * 创建数据表
     * @param string $table
     * @param array $data
     * @return mixed
     */
    abstract public function createTable(string $table, array $data);

    /**
     * 添加字段
     * @param string $table
     * @param array $filedVals
     * @return mixed
     */
    abstract public function addFiledVals(string $table, array $filedVals);

    /**
     * 表格字段
     * @param string $table
     * @return mixed
     */
    abstract public function tableFields(string $table);
}