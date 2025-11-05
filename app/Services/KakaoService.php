<?php

namespace App\Services;

use RuntimeException;

class KakaoService
{
    private const AUTHORIZE_URL = 'https://kauth.kakao.com/oauth/authorize';
    private const TOKEN_URL = 'https://kauth.kakao.com/oauth/token';
    private const PROFILE_URL = 'https://kapi.kakao.com/v2/user/me';

    private string $clientId;
    private ?string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        global $config;

        $kakaoConfig = $config['kakao'] ?? null;
        $clientId = $kakaoConfig['rest_api_key'] ?? null;
        $redirectUri = $kakaoConfig['redirect_uri'] ?? null;
        $clientSecret = $kakaoConfig['client_secret'] ?? null;

        if (!$clientId || !$redirectUri) {
            throw new RuntimeException('Kakao OAuth configuration is missing.');
        }

        $this->clientId = $clientId;
        $this->redirectUri = $redirectUri;
        $this->clientSecret = $clientSecret ?: null;
    }

    public static function isConfigured(): bool
    {
        global $config;

        if (!isset($config['kakao']) || !is_array($config['kakao'])) {
            return false;
        }

        $kakaoConfig = $config['kakao'];

        return !empty($kakaoConfig['rest_api_key']) && !empty($kakaoConfig['redirect_uri']);
    }

    public function getAuthorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'state' => $state,
            'scope' => 'profile_nickname account_email',
        ], '', '&', PHP_QUERY_RFC3986);

        return self::AUTHORIZE_URL . '?' . $query;
    }

    public function fetchAccessToken(string $code): array
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ];

        if ($this->clientSecret) {
            $params['client_secret'] = $this->clientSecret;
        }

        $body = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        return $this->requestJson(self::TOKEN_URL, 'POST', [
            'Content-Type: application/x-www-form-urlencoded',
        ], $body);
    }

    public function fetchUserProfile(string $accessToken): array
    {
        return $this->requestJson(self::PROFILE_URL, 'GET', [
            'Authorization: Bearer ' . $accessToken,
        ]);
    }

    private function requestJson(string $url, string $method, array $headers = [], ?string $body = null): array
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            $response = curl_exec($ch);

            if ($response === false) {
                $errorMessage = curl_error($ch);
                curl_close($ch);
                throw new RuntimeException('cURL error while contacting Kakao API: ' . $errorMessage);
            }

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $headerString = implode("\r\n", $headers);
            if ($headerString !== '') {
                $headerString .= "\r\n";
            }

            $options = [
                'http' => [
                    'method' => $method,
                    'header' => $headerString,
                    'ignore_errors' => true,
                ],
            ];

            if ($body !== null) {
                $options['http']['content'] = $body;
                if (stripos($headerString, 'Content-Type:') === false) {
                    $options['http']['header'] .= "Content-Type: application/x-www-form-urlencoded\r\n";
                }
            }

            $context = stream_context_create($options);
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                $error = error_get_last();
                throw new RuntimeException('HTTP request to Kakao API failed: ' . ($error['message'] ?? 'unknown error'));
            }

            $statusLine = $http_response_header[0] ?? 'HTTP/1.1 500';
            if (preg_match('#\s(\d{3})\s#', $statusLine, $matches)) {
                $statusCode = (int) $matches[1];
            } else {
                $statusCode = 500;
            }
        }

        if ($statusCode >= 400) {
            throw new RuntimeException("Kakao API responded with HTTP {$statusCode}: {$response}");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Failed to decode Kakao API response: ' . json_last_error_msg());
        }

        return $data;
    }
}
