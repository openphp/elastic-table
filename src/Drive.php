<?php


namespace Openphp\ElasticTable;


abstract class Drive
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $option = [];

    /**
     * Drive constructor.
     * @param array $config
     * @param array $option
     */
    public function __construct(array $config = [], array $option = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->option = array_merge($this->option, $option);
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