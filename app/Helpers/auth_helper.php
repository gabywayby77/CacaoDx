<?php

if (!function_exists('is_admin')) {
    /**
     * Check if the current user is an admin
     * @return bool
     */
    function is_admin()
    {
        return session()->get('role') === 'admin';
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     * @return bool
     */
    function is_logged_in()
    {
        return session()->get('isLoggedIn') === true;
    }
}

if (!function_exists('user_name')) {
    /**
     * Get the current user's full name
     * @return string
     */
    function user_name()
    {
        $firstName = session()->get('first_name') ?? '';
        $lastName = session()->get('last_name') ?? '';
        return trim($firstName . ' ' . $lastName);
    }
}

if (!function_exists('user_id')) {
    /**
     * Get the current user's ID
     * @return int|null
     */
    function user_id()
    {
        return session()->get('user_id');
    }
}

if (!function_exists('user_email')) {
    /**
     * Get the current user's email
     * @return string|null
     */
    function user_email()
    {
        return session()->get('email');
    }
}

if (!function_exists('user_role')) {
    /**
     * Get the current user's role
     * @return string|null
     */
    function user_role()
    {
        return session()->get('role');
    }
}

if (!function_exists('get_user_role')) {
    /**
     * Get the current user's role (alias for user_role)
     * @return string|null
     */
    function get_user_role()
    {
        return session()->get('role');
    }
}