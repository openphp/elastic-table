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
    protected $data = [];
    /**
     * @var Drive
     */
    protected $drive;

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
        $this->data = $datas;
        return $this;
    }

    /**
     * @return array
     */
    protected function getDataFiledVal()
    {
        $data      = $this->data;
        $filed_len = [];
        foreach ($data as $i => $datum) {
            foreach ($datum as $filed => $val) {
                if (is_array($val)) {
                    continue;
                }
                $filed_len[$filed][$i] = mb_strlen($val);
            }
        }
        $fileds = [];
        foreach ($filed_len as $f => $item) {
            $index      = array_search(max($item), $item);
            $fileds[$f] = $data[$index][$f];
        }
        return $fileds;
    }

    /**
     * @return bool|mixed
     */
    public function check()
    {
        $ret          = true;
        $dataFiledVal = $this->getDataFiledVal();
        $fileds       = array_keys($dataFiledVal);
        if (!$this->drive->exist($this->table)) {
            $ret = $this->drive->createTable($this->table, $dataFiledVal);
        }
        $tableFileds = $this->drive->tableFields($this->table);
        if ($diff = array_diff($fileds, $tableFileds)) {
            $alertFileds = [];
            foreach ($diff as $f) {
                $alertFileds[$f] = $dataFiledVal[$f];
            }
            $ret = $this->drive->addFiledVals($this->table, $alertFileds);
        }
        return $ret;
    }

    /**
     * @param Drive $drive
     * @param string $table
     * @param array $datas
     * @return bool|mixed
     */
    public static function checks(Drive $drive, $table, $datas)
    {
        return (new static($drive))->table($table)->datas($datas)->check();
    }
}