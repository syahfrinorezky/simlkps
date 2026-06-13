<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $role = session()->get('userRole');

        if (!$role) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Dashboard'
        ];

        switch ($role) {
            case 'admin':
                return view('dashboard/admin', $data);
            case 'prodi':
                return view('dashboard/prodi', $data);
            case 'dosen':
                return view('dashboard/dosen', $data);
            case 'asesor':
                return view('dashboard/asesor', $data);
            default:
                return redirect()->to('/login')->with('error', 'Role tidak dikenali.');
        }
    }
}
