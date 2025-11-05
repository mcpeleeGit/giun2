<?php

namespace App\Services;

use App\Repositories\AccessLogRepository;
use DateInterval;
use DateTimeImmutable;
use Throwable;

class AccessLogService
{
    private $accessLogRepository;

    public function __construct()
    {
        $this->accessLogRepository = new AccessLogRepository();
    }

    public function logRequest(string $path, string $method): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        try {
            $user = function_exists('\\current_user') ? \current_user() : null;
        } catch (Throwable $e) {
            $user = null;
        }

        if (function_exists('mb_substr')) {
            $path = mb_substr($path, 0, 255, 'UTF-8');
        } else {
            $path = substr($path, 0, 255);
        }
        $method = strtoupper(substr($method, 0, 10));
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        if ($ipAddress !== null) {
            $ipAddress = substr($ipAddress, 0, 45);
        }

        if ($userAgent !== null) {
            $userAgent = substr($userAgent, 0, 500);
        }

        if ($referer !== null) {
            $referer = substr($referer, 0, 500);
        }

        try {
            $this->accessLogRepository->logAccess([
                'user_id' => $user ? ($user->id ?? null) : null,
                'path' => $path,
                'method' => $method,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'referer' => $referer,
            ]);
        } catch (Throwable $e) {
            error_log('AccessLogService::logRequest failed: ' . $e->getMessage());
        }
    }

    public function getDailyStats(int $days = 7): array
    {
        $days = max(1, $days);
        $startDate = $this->calculateStartDate($days);

        $rows = $this->accessLogRepository->getDailyCounts($startDate->format('Y-m-d H:i:s'));
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['visit_date']] = (int) $row['visit_count'];
        }

        $labels = [];
        $counts = [];
        $current = $startDate;
        for ($i = 0; $i < $days; $i++) {
            $dateKey = $current->format('Y-m-d');
            $labels[] = $dateKey;
            $counts[] = $indexed[$dateKey] ?? 0;
            $current = $current->add(new DateInterval('P1D'));
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'total' => array_sum($counts),
        ];
    }

    public function getTopPages(int $days = 7, int $limit = 5): array
    {
        $days = max(1, $days);
        $limit = max(1, $limit);
        $startDate = $this->calculateStartDate($days);
        $rows = $this->accessLogRepository->getTopPaths($startDate->format('Y-m-d H:i:s'), $limit);

        $total = array_sum(array_map(static function ($row) {
            return (int) ($row['visit_count'] ?? 0);
        }, $rows));

        return array_map(static function ($row) use ($total) {
            $count = (int) ($row['visit_count'] ?? 0);
            return [
                'path' => (string) ($row['path'] ?? ''),
                'count' => $count,
                'ratio' => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
            ];
        }, $rows);
    }

    public function getRecentVisits(int $limit = 10): array
    {
        $limit = max(1, $limit);
        $rows = $this->accessLogRepository->getRecentVisits($limit);

        return array_map(static function ($row) {
            return [
                'path' => (string) ($row['path'] ?? ''),
                'method' => (string) ($row['method'] ?? ''),
                'ip_address' => (string) ($row['ip_address'] ?? ''),
                'referer' => (string) ($row['referer'] ?? ''),
                'user_agent' => (string) ($row['user_agent'] ?? ''),
                'created_at' => (string) ($row['created_at'] ?? ''),
            ];
        }, $rows);
    }

    private function calculateStartDate(int $days): DateTimeImmutable
    {
        $days = max(1, $days);
        $start = new DateTimeImmutable('today');

        if ($days > 1) {
            $start = $start->sub(new DateInterval('P' . ($days - 1) . 'D'));
        }

        return $start;
    }
}
