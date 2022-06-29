<?php


namespace Openphp\ElasticTable;


class ElasticTable
{
    /**
     * @var string
     */
    protected $table;
    /**
     * @var array
     */
    protected $datas = [];
    /**
     * @var Drive
     */
    protected $drive;

    /**
     * 忽略数组中的数组
     * @var bool
     */
    protected $continueArray = true;

    /**
     * ElasticTable constructor.
     * @param Drive $drive
     */
    public function __construct(Drive $drive)
    {
        $this->drive = $drive;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function dataOne(array $data)
    {
        $this->datas([0 => $data]);
        return $this;
    }

    /**
     * @param array $datas
     * @return $this
     */
    public function datas(array $datas)
    {
        $this->datas = $datas;
        return $this;
    }


    /**
     * @return $this
     */
    public function continueArray()
    {
        $this->continueArray = false;
        return $this;
    }

    /**
     * @return void
     */
    public function checkForException()
    {
        if (!$this->drive->exist($this->table)) {
            throw new \RuntimeException($this->table . ' table does not exist');
        }
        $datasFiledVal = Resault::datasFiledVal($this->datas, $this->continueArray);
        $dataFileds    = array_keys($datasFiledVal);
        $dbFileds      = $this->drive->tableFields($this->table);
        $diff          = array_diff($dataFileds, $dbFileds);
        if ($diff) {
            throw new \RuntimeException($this->table . ' Fields are inconsistent');
        }
    }

    /**
     * @return bool|mixed
     */
    public function checkAndUpdate()
    {
        $ret           = true;
        $datasFiledVal = Resault::datasFiledVal($this->datas, $this->continueArray);
        $dataFileds    = array_keys($datasFiledVal);
        if (!$this->drive->exist($this->table)) {
            $ret = $this->drive->createTable($this->table, $datasFiledVal);
        }
        $dbFileds = $this->drive->tableFields($this->table);
        if ($diff = array_diff($dataFileds, $dbFileds)) {
            $filedVals = [];
            foreach ($diff as $filed) {
                $filedVals[$filed] = $datasFiledVal[$filed];
            }
            if ($filedVals) {
                $ret = $this->drive->addFiledVals($this->table, $filedVals);
            }
        }
        return $ret;
    }
}