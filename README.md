## 使用

~~~
<?php

require 'vendor/autoload.php';

$mysql = new \Openphp\ElasticTable\Drive\Mysql([
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'dbname'   => 'test',
    'charset'  => 'utf8mb4',
    'username' => 'root',
    'passwd'   => '123456',
    'options'  => []
]);

$table = new \Openphp\ElasticTable\ElasticTable($mysql);

// 首先检查表是否存在，不存在就创建user 表，字段name，sex，age，hight
// 然后检查db的字段是否与最新的数据字段是否保持一致，如果不一致就同步最新的字段
$table->table('user')->datas(
    [
        [
            'name' => 'zhiqiang',
            'sex'  => 1,
            'age'  => 18
        ],
        [
            'name'  => 'zhiqiang',
            'sex'   => 1,
            'age'   => 18,
            'hight' => 175
        ]
    ]
)->checkAndUpdate();
~~~

