<?php

namespace App\Repositories\Common;

use PDO;

class Repository {
    protected $pdo;

    public function __construct() {
        global $config; // 전역 변수로 설정을 가져옵니다.

        // 서버 호스트 이름을 확인하여 데이터베이스 설정 선택
        $databaseConfig = ($_SERVER['HTTP_HOST'] === 'localhost') ? $config['sandbox_database'] : $config['real_database'];

        $dsn = 'mysql:host=' . $databaseConfig['host'] . ';port=' . $databaseConfig['port'] . ';dbname=' . $databaseConfig['dbname'] . ';charset=utf8mb4';
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
