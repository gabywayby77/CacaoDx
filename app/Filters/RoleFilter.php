<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Get current URI
        $uri = service('uri');
        $segment = $uri->getSegment(1);
        
        // Get user role from session
        $userRole = $session->get('role');
        
        // Admin-only pages
        $adminOnlyPages = ['users', 'activity_log', 'pests'];
        
        // If user (not admin) tries to access admin pages
        if ($userRole !== 'admin' && in_array($segment, $adminOnlyPages)) {
            return redirect()
                ->to(base_url('dashboard'))
                ->with('error', 'Access denied. Admin privileges required.');
        }
        
        // Admin-only actions (edit/delete)
        $method = $request->getMethod();
        if ($userRole !== 'admin' && $method === 'post') {
            $path = $request->getPath();
            
            // Block disease edit/delete for regular users
            if (
                strpos($path, 'diseases/update') !== false || 
                strpos($path, 'diseases/delete') !== false ||
                strpos($path, 'diseases/store') !== false
            ) {
                return redirect()
                    ->back()
                    ->with('error', 'You do not have permission to modify diseases.');
            }
            
            // Block image uploads for regular users
            if (strpos($path, 'images/upload') !== false) {
                return redirect()
                    ->back()
                    ->with('error', 'You do not have permission to upload images.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}