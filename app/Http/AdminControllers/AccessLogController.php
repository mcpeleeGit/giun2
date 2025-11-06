<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Services\AccessLogService;

class AccessLogController extends Controller
{
    private $accessLogService;

    public function __construct()
    {
        parent::__construct();
        $this->accessLogService = new AccessLogService();
    }

    public function index(): void
    {
        $days = 30;
        $dailyStats = $this->accessLogService->getDailyStats($days);
        $topPages = $this->accessLogService->getTopPages($days, 5);
        $recentVisits = $this->accessLogService->getRecentVisits(10);
        $userAgentStats = $this->accessLogService->getUserAgentStats($days, 10);

        adminView('access_logs', [
            'admin' => $this->adminUser,
            'dailyStats' => $dailyStats,
            'topPages' => $topPages,
            'recentVisits' => $recentVisits,
            'userAgentStats' => $userAgentStats,
            'periodDays' => $days,
        ]);
    }
}
