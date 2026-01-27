<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DiagnosisModel;
use App\Models\DiseaseModel;
use App\Models\FarmModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        $userModel      = new UserModel();
        $diagnosisModel = new DiagnosisModel();
        $diseaseModel   = new DiseaseModel();
        $farmModel      = new FarmModel();

        $newUsers = $userModel
            ->orderBy('registered_at', 'DESC')
            ->limit(5)
            ->find();

        $db = \Config\Database::connect();
        
        $farmerCount = $db->table('users')
            ->where('user_type_id', 2)
            ->countAllResults();
        
        $adminCount = $db->table('users')
            ->where('role', 'admin')
            ->countAllResults();
        
        $regularUserCount = $db->table('users')
            ->where('user_type_id', 1)
            ->where('role', 'user')
            ->countAllResults();

        $totalDiagnosisCount = $diagnosisModel->getTotalCount();

        $uniqueDiagnosisUsers = $db->table('diagnosis')
            ->select('user_id')
            ->distinct()
            ->countAllResults();

        $data = [
            'userName'         => $userName,
            'totalUsers'       => $userModel->countAll(),
            'totalDiagnosis'   => $totalDiagnosisCount,
            'totalDiseases'    => $diseaseModel->countAll(),
            'uniqueScanUsers'  => $uniqueDiagnosisUsers,
            'farms'            => $farmModel->findAll(),
            'newUsers'         => $newUsers,
            'farmerCount'      => $farmerCount,
            'adminCount'       => $adminCount,
            'regularUserCount' => $regularUserCount,
        ];

        return view('dashboard', $data);
    }

    public function profile()
    {
        $session = session();
        $db = \Config\Database::connect();
        
        $userId = $session->get('user_id'); // ✅ FIXED: Changed from 'id' to 'user_id'
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }
        
        $user = $db->table('users')
            ->select('users.*, user_type.user_type as user_type_name')
            ->join('user_type', 'users.user_type_id = user_type.id', 'left')
            ->where('users.id', $userId)
            ->get()
            ->getRowArray();
        
        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $diagnosisCount = $db->table('diagnosis')
            ->where('user_id', $userId)
            ->countAllResults();

        $farmsCount = $db->table('farms')
            ->where('user_id', $userId)
            ->countAllResults();

        $data = [
            'userName'       => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'firstName'      => $user['first_name'] ?? 'N/A',
            'lastName'       => $user['last_name'] ?? 'N/A',
            'userEmail'      => $user['email'] ?? 'N/A',
            'userPhone'      => $user['contact_number'] ?? 'Not provided',
            'userAddress'    => 'Negros Oriental, Philippines',
            'joinDate'       => date('F Y', strtotime($user['registered_at'] ?? 'now')),
            'userId'         => $user['id'],
            'userStatus'     => $user['status'],
            'userTypeName'   => $user['user_type_name'] ?? 'User',
            'diagnosisCount' => $diagnosisCount,
            'farmsCount'     => $farmsCount,
        ];

        return view('profile', $data);
    }

    public function settings()
    {
        $session = session();
        $db = \Config\Database::connect();
        
        $userId = $session->get('user_id'); // ✅ FIXED: Changed from 'id' to 'user_id'
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }
        
        $user = $db->table('users')
            ->select('users.*, user_type.user_type as user_type_name')
            ->join('user_type', 'users.user_type_id = user_type.id', 'left')
            ->where('users.id', $userId)
            ->get()
            ->getRowArray();
        
        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $data = [
            'userName' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'user'     => $user,
        ];

        return view('settings', $data);
    }

    public function updatePersonal()
    {
        $session = session();
        $db = \Config\Database::connect();
        $userId = $session->get('user_id'); // ✅ FIXED: Changed from 'id' to 'user_id'

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $rules = [
            'first_name'     => 'required|min_length[2]|max_length[50]',
            'last_name'      => 'required|min_length[2]|max_length[50]',
            'email'          => 'required|valid_email',
            'contact_number' => 'permit_empty|min_length[10]|max_length[15]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please check your input fields')->withInput();
        }

        $data = [
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
        ];

        $db->table('users')->where('id', $userId)->update($data);

        // Update session
        $session->set([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
        ]);

        return redirect()->to('settings')->with('success', 'Personal information updated successfully!');
    }

    public function updatePassword()
    {
        $session = session();
        $db = \Config\Database::connect();
        $userId = $session->get('user_id'); // ✅ FIXED: Changed from 'id' to 'user_id'

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please check your password fields');
        }

        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        // Update password
        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        $db->table('users')->where('id', $userId)->update(['password' => $newPassword]);

        return redirect()->to('settings')->with('success', 'Password updated successfully!');
    }

    public function updateNotifications()
    {
        // Handle notification preferences (you can store in database or session)
        return redirect()->to('settings')->with('success', 'Notification preferences updated!');
    }

    public function updatePreferences()
    {
        // Handle user preferences (theme, language, timezone)
        return redirect()->to('settings')->with('success', 'Preferences updated successfully!');
    }

    public function deleteAccount()
    {
        $session = session();
        $db = \Config\Database::connect();
        $userId = $session->get('user_id'); // ✅ FIXED: Changed from 'id' to 'user_id'

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        // Delete user account
        $db->table('users')->where('id', $userId)->delete();
        
        // Destroy session
        $session->destroy();

        return redirect()->to('/')->with('success', 'Your account has been deleted');
    }
}