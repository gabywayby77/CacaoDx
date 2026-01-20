<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiAuth implements FilterInterface
{
    private $key = "cacaodx1234567890";

    public function before(RequestInterface $request, $arguments = null)
    {
        // Get Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Missing Authorization header']);
        }

        // Extract Bearer token
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Decode JWT
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            
            // Store in request for controller to use
            $request->uid = $decoded->uid;
            
            return null; // Continue to controller
            
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Invalid or expired token']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}