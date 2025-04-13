<?php

namespace App\Repositories;

use App\Repositories\Common\Repository;
use App\Models\Seo;

class SeoRepository extends Repository {

    public function findByPath($path) {
        $stmt = $this->pdo->prepare("SELECT * FROM seo WHERE path = ?");
        $stmt->execute([$path]);
        $seoData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($seoData) {
            $seo = new Seo();
            return $this->mapDataToObject($seoData, $seo);
        }

        return null;
    }
}
