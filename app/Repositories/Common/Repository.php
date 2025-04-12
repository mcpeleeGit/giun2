<?php

namespace App\Repositories\Common;

use PDO;

class Repository {
    protected $pdo;

    public function __construct() {
        global $config; // 전역 변수로 설정을 가져옵니다.
        $dsn = 'mysql:host=' . $config['database']['host'] . ';port=' . $config['database']['port'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, $config['database']['username'], $config['database']['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    protected function mapDataToObject($data, $object) {
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }
        return $object;
    }
}
