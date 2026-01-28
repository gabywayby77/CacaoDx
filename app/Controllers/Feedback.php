<?php

namespace App\Controllers;

use App\Models\FeedbackModel;
use CodeIgniter\Controller;

class Feedback extends Controller
{
    protected $feedbackModel;

    public function __construct()
    {
        $this->feedbackModel = new FeedbackModel();
        helper('auth'); // Load auth helper
    }

    /**
     * Show feedback page
     * Admin: See all feedback
     * User: Submit feedback form
     */
    public function index()
    {
        $session = session();

        // Get user info
        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        $userId = $session->get('user_id');
        $isAdmin = is_admin();

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $data = [
            'userName' => $userName,
            'isAdmin'  => $isAdmin,
        ];

        if ($isAdmin) {
            // Admin view: Get all feedback with user names
            $data['feedbacks'] = $this->feedbackModel->getAllWithUserNames();
            $data['userFeedbacks'] = []; // Admin doesn't need their own feedback
        } else {
            // User view: Get only their feedback
            $data['feedbacks'] = []; // Users don't see all feedback
            $data['userFeedbacks'] = $this->feedbackModel->getByUserId($userId);
        }

        return view('feedback', $data);
    }

    /**
     * Submit feedback (USER ONLY)
     */
    public function submit()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        // Prevent admin from submitting feedback through form
        if (is_admin()) {
            return redirect()->to('/feedback')
                ->with('error', 'Admins cannot submit feedback.');
        }

        $rules = [
            'rating'   => 'required|in_list[1,2,3,4,5]',
            'comments' => 'required|min_length[10]|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide a rating and comments (10-500 characters).');
        }

        $this->feedbackModel->insert([
            'user_id'  => $userId,
            'rating'   => $this->request->getPost('rating'),
            'comments' => $this->request->getPost('comments'),
        ]);

        return redirect()->to('/feedback')
            ->with('success', 'Thank you for your feedback! We appreciate your input.');
    }

    /**
     * Delete feedback (ADMIN ONLY)
     */
    public function delete()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()
                ->with('error', 'Invalid feedback ID.');
        }

        $this->feedbackModel->delete($id);

        return redirect()->to('/feedback')
            ->with('success', 'Feedback deleted successfully!');
    }
}