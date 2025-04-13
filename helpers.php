<?php
use App\Models\Seo;
use App\Repositories\SeoRepository;

function view($name, $data = []) {
    extract($data);
    $seo = getSeo($data['seo'] ?? null);
    include "layouts/header.php";
    include "pages/{$name}.php";
    include "layouts/footer.php";
}

function adminView($name, $data = []) {
    extract($data);
    include "pages/admin/layouts/header.php";
    include "pages/admin/{$name}.php";
}

function getSeo($seo_data) {
    $seoRepository = new SeoRepository(); // path 에 따라 저장된 seo 데이터를 가져오는 경우
    $seo = $seoRepository->findByPath($_SERVER['REQUEST_URI']);

    if (!$seo) {
        $seo = new Seo();
        $seo->path = $_SERVER['REQUEST_URI'];
        $seo->title = '소셜 서비스 연동 도구 - 온라인 도구 제공 사이트';
        $seo->description = '각종 소셜 서비스와 연동할 수 있는 다양한 온라인 도구를 제공합니다.';
        $seo->image = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/assets/images/favicon.png';
        $seo->url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    if (isset($seo_data)) { // 게시판 상세와 같이 페이지 별로 활용가능한 seo 데이터가 있는 경우
        if(isset($seo_data->title)) $seo->title = $seo_data->title;
        if(isset($seo_data->description)) $seo->description = $seo_data->description;
        if(isset($seo_data->image)) $seo->image = $seo_data->image;
        if(isset($seo_data->url)) $seo->url = $seo_data->url; 
    }

    return $seo;
}
