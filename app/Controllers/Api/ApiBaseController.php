<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class ApiBaseController extends ResourceController
{
    protected $format = 'json';
    protected ?int $userId = null;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->userId = $this->extractUserIdFromToken();
    }

    protected function extractUserIdFromToken(): ?int
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header) || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = substr($header, 7);
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $secret = env('JWT_SECRET', getenv('JWT_SECRET') ?: 'lifeknob_secret_key_change_in_production');
        $expectedSignature = hash_hmac('sha256', "$parts[0].$parts[1]", $secret);
        if (!hash_equals($expectedSignature, $parts[2])) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        if (isset($payload['expires_at']) && $payload['expires_at'] < time()) {
            return null;
        }

        return (int) $payload['user_id'];
    }

    protected function requireAuth()
    {
        if ($this->userId === null) {
            return $this->failUnauthorized('Authentication required');
        }
        return null;
    }

    protected function getUserId(): int
    {
        return $this->userId ?? (int) ($this->input('user_id') ?? 0);
    }

    protected function input(?string $key = null)
    {
        if ($key === null) {
            $contentType = $this->request->getHeaderLine('Content-Type');
            if (strpos($contentType, 'application/json') !== false) {
                return $this->request->getJSON(true) ?? [];
            }
            return array_merge(
                $this->request->getGet() ?? [],
                $this->request->getPost() ?? []
            );
        }

        $value = $this->request->getVar($key);
        if ($value !== null) return $value;

        $json = $this->request->getJSON(true);
        if (is_array($json) && isset($json[$key])) {
            return $json[$key];
        }

        return null;
    }
}
