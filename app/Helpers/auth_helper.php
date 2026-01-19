<?php

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin
     */
    function is_admin(): bool
    {
        return session()->get('role') === 'admin';
    }
}

if (!function_exists('can_edit')) {
    /**
     * Check if user can edit/delete content
     */
    function can_edit(): bool
    {
        return session()->get('role') === 'admin';
    }
}

if (!function_exists('get_user_role')) {
    /**
     * Get current user's role
     */
    function get_user_role(): string
    {
        return session()->get('role') ?? 'guest';
    }
}

if (!function_exists('user_name')) {
    /**
     * Get current user's full name
     */
    function user_name(): string
    {
        $session = session();
        return trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );
    }
}