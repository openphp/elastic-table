<?php


namespace Openphp\ElasticTable\Drive;

use Openphp\ElasticTable\Drive;
use PDO;

class Mysql extends Drive
{
    /**
     * @var array
     */
    protected $config = [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'dbname'   => 'test',
        'charset'  => 'utf8mb4',
        'username' => 'root',
        'passwd'   => '123456',
        'options'  => []
    ];

    /**
     * @return PDO
     */
    public function connect()
    {
        return new PDO('mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname'] . ';charset=' . $this->config['charset'],
            $this->config['username'],
            $this->config['passwd']);
    }


    /**
     * @param string $table
     * @return bool|mixed
     */
    public function exist(string $table)
    {
        $stmt = $this->connect()->prepare("select * from information_schema.TABLES WHERE TABLE_NAME = :table AND TABLE_SCHEMA = :dbname ;");
        $stmt->execute(['table' => $table, 'dbname' => $this->config['dbname']]);
        if ($stmt->fetch(2) != false) {
            return true;
        }
        return false;
    }

    /**
     * @param $filed
     * @param $val
     * @return string
     */
    protected function filedSQL($filed, $val)
    {
        $type = gettype($val);
        if ($type == 'integer') {
            if ($val <= 127) {
                return "`{$filed}` tinyint(4) DEFAULT '0' ,";
            } elseif (mb_strlen($val) > 11) {
                return "`{$filed}` bigint(20) DEFAULT '0' ,";
            } else {
                return "`{$filed}` int(11) DEFAULT '0' ,";
            }
        }
        if ($type == 'boolean') {
            return "`{$filed}` tinyint(4) DEFAULT '0' COMMENT '1 ture 0 false' ,";
        }
        if ($type == 'double') {
            [$i, $f] = explode('.', (string)$val);
            $b = mb_strlen($i) <= 10 ? 10 : mb_strlen($i);
            $e = mb_strlen($f) <= 2 ? 2 : mb_strlen($f);
            return "`{$filed}` double({$b},{$e}) DEFAULT '0' ,";
        }
        if ($type == 'array') {
            return "`{$filed}` json DEFAULT NULL,";
        }

        $len = mb_strlen($val);
        if (strtotime($val) > strtotime('1970-01-02 00:00:00') && $len > 11 && $len <= 19) {
            return "`{$filed}` timestamp NULL DEFAULT NULL ,";
        }
        if ($len <= 50) {
            return " `{$filed}` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT '' ,";
        }
        return " `{$filed}` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' ,";
    }

    /**
     * @param string $table
     * @param array $data
     * @return bool
     */
    public function createTable(string $table, array $data)
    {
        $sql = "CREATE TABLE `{$table}` (";
        $sql .= '`id` int(11) NOT NULL AUTO_INCREMENT,';
        foreach ($data as $filed => $val) {
            $sql .= $this->filedSQL($filed, $val);
        }
        $sql .= 'PRIMARY KEY (`id`)';
        $sql .= ') ENGINE=' . ($this->option['engine'] ?? "InnoDB")
            . ' DEFAULT CHARSET=' . ($this->option['charset'] ?? "utf8mb4")
            . ' COLLATE=' . ($option['collate'] ?? "utf8mb4_bin")
            . (isset($this->option['comment']) ? "COMMENT=" . $this->option['comment'] : "")
            . ';';
        if ($this->connect()->exec($sql) === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $table
     * @param array $filedVals
     * @return bool
     */
    public function addFiledVals(string $table, array $filedVals)
    {
        $sql = 'ALTER TABLE ' . $table;
        foreach ($filedVals as $filed => $val) {
            $sql .= ' ADD COLUMN ' . $this->filedSQL($filed, $val);
        }
        $sql = substr($sql, 0, -1) . ';';
        if ($this->connect()->exec($sql) === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $table
     * @return array
     */
    public function tableFields(string $table)
    {
        $stmt = $this->connect()->prepare("SHOW FULL COLUMNS FROM {$table}");
        $stmt->execute();
        if ($table = $stmt->fetchAll(2)) {
            return array_column($table, 'Field');
        }
        return [];
    }
}