<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\LecturerModel;
use App\Models\StudyProgramModel;

class UserController extends BaseController
{
    private function adminOnly()
    {
        if (session()->get('userRole') !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses tidak diizinkan.');
        }
        return null;
    }

    // index
    public function index()
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $db = db_connect();
        
        $db->table('users')
           ->where('deleted_at <', date('Y-m-d H:i:s', strtotime('-7 days')))
           ->delete();

        $users = $db->table('users u')
            ->select('u.id, u.nama_lengkap, u.email, u.telepon, u.is_active, u.last_login_at, u.created_at, r.nama as role_name, sp.nama_prodi, l.nama as lecturer_name, l.nidn')
            ->join('roles r', 'r.id = u.role_id')
            ->join('study_programs sp', 'sp.id = u.study_program_id', 'left')
            ->join('lecturers l', 'l.id = u.lecturer_id', 'left')
            ->where('u.deleted_at', null)
            ->orderBy('u.created_at', 'DESC')
            ->get()->getResultArray();

        $trash = $db->table('users u')
            ->select('u.id, u.nama_lengkap, u.email, u.telepon, u.is_active, u.deleted_at, r.nama as role_name, sp.nama_prodi, l.nama as lecturer_name, l.nidn')
            ->join('roles r', 'r.id = u.role_id')
            ->join('study_programs sp', 'sp.id = u.study_program_id', 'left')
            ->join('lecturers l', 'l.id = u.lecturer_id', 'left')
            ->where('u.deleted_at !=', null)
            ->orderBy('u.deleted_at', 'DESC')
            ->get()->getResultArray();

        $now = new \DateTime();
        foreach ($trash as &$t) {
            $deletedAt = new \DateTime($t['deleted_at']);
            $expiresAt = (clone $deletedAt)->modify('+7 days');
            $diff = $now->diff($expiresAt);
            if ($diff->invert) {
                $t['countdown'] = 'Kedaluwarsa';
            } else {
                $days = $diff->days;
                $hours = $diff->h;
                if ($days > 0) {
                    $t['countdown'] = "$days hari $hours jam";
                } else {
                    $t['countdown'] = "$hours jam";
                }
            }
        }

        $roles         = model('RoleModel')->findAll();
        $lecturers     = model('LecturerModel')->where('status_dosen', 'tetap')->orderBy('nama')->findAll();
        $studyPrograms = model('StudyProgramModel')->where('status_aktif', 1)->orderBy('nama_prodi')->findAll();

        return view('users/index', [
            'title'         => 'Manajemen User',
            'users'         => $users,
            'trash'         => $trash,
            'roles'         => $roles,
            'lecturers'     => $lecturers,
            'studyPrograms' => $studyPrograms,
        ]);
    }

    // store data
    public function store()
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $rules = [
            'nama_lengkap'     => 'required|min_length[3]|max_length[200]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'role_id'          => 'required|is_not_unique[roles.id]',
            'telepon'          => 'permit_empty|max_length[20]',
            'study_program_id' => 'permit_empty',
            'lecturer_id'      => 'permit_empty',
        ];

        $errors = [
            'nama_lengkap' => [
                'required' => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama lengkap minimal 3 karakter.',
                'max_length' => 'Nama lengkap maksimal 200 karakter.'
            ],
            'email' => [
                'required' => 'Alamat email wajib diisi.',
                'valid_email' => 'Format alamat email tidak valid.',
                'is_unique' => 'Alamat email ini sudah digunakan.'
            ],
            'password' => [
                'required' => 'Password wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'role_id' => [
                'required' => 'Hak akses role wajib dipilih.',
                'is_not_unique' => 'Role yang dipilih tidak valid.'
            ],
            'telepon' => [
                'max_length' => 'Nomor telepon maksimal 20 karakter.'
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_lengkap'     => $this->request->getPost('nama_lengkap'),
            'email'            => $this->request->getPost('email'),
            'password'         => $this->request->getPost('password'),
            'role_id'          => $this->request->getPost('role_id'),
            'telepon'          => $this->request->getPost('telepon') ?: null,
            'study_program_id' => $this->request->getPost('study_program_id') ?: null,
            'lecturer_id'      => $this->request->getPost('lecturer_id') ?: null,
            'is_active'        => 1,
        ];

        model('UserModel')->insert($data);

        return redirect()->to('/users')->with('success', 'User berhasil ditambahkan.');
    }

    // edit data
    public function edit($id)
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $db   = db_connect();
        $user = $db->table('users u')
            ->select('u.*, r.nama as role_name')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.deleted_at', null)
            ->where('u.id', $id)
            ->get()->getRowArray();

        if (!$user) {
            return $this->response->setJSON(['error' => 'User tidak ditemukan.'])->setStatusCode(404);
        }

        unset($user['password']);
        return $this->response->setJSON($user);
    }

    // update data
    public function update($id)
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $emailRule = "required|valid_email|is_unique[users.email,id,{$id}]";

        $rules = [
            'nama_lengkap'     => 'required|min_length[3]|max_length[200]',
            'email'            => $emailRule,
            'password'         => 'permit_empty|min_length[6]',
            'role_id'          => 'required|is_not_unique[roles.id]',
            'telepon'          => 'permit_empty|max_length[20]',
            'study_program_id' => 'permit_empty',
            'lecturer_id'      => 'permit_empty',
        ];

        $errors = [
            'nama_lengkap' => [
                'required' => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama lengkap minimal 3 karakter.',
                'max_length' => 'Nama lengkap maksimal 200 karakter.'
            ],
            'email' => [
                'required' => 'Alamat email wajib diisi.',
                'valid_email' => 'Format alamat email tidak valid.',
                'is_unique' => 'Alamat email ini sudah digunakan.'
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'role_id' => [
                'required' => 'Hak akses role wajib dipilih.',
                'is_not_unique' => 'Role yang dipilih tidak valid.'
            ],
            'telepon' => [
                'max_length' => 'Nomor telepon maksimal 20 karakter.'
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_lengkap'     => $this->request->getPost('nama_lengkap'),
            'email'            => $this->request->getPost('email'),
            'role_id'          => $this->request->getPost('role_id'),
            'telepon'          => $this->request->getPost('telepon') ?: null,
            'study_program_id' => $this->request->getPost('study_program_id') ?: null,
            'lecturer_id'      => $this->request->getPost('lecturer_id') ?: null,
            'is_active'        => $this->request->getPost('is_active') ?? 1,
        ];

        $newPassword = $this->request->getPost('password');
        if ($newPassword) {
            $data['password'] = $newPassword;
        }

        model('UserModel')->update($id, $data);

        return redirect()->to('/users')->with('success', 'User berhasil diperbarui.');
    }

    // toggle status
    public function toggleActive($id)
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $user = model('UserModel')->find($id);
        if (!$user) {
            return $this->response->setJSON(['error' => 'User tidak ditemukan.'])->setStatusCode(404);
        }

        model('UserModel')->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);

        return $this->response->setJSON(['success' => true, 'is_active' => !$user['is_active']]);
    }

    // destroy data
    public function destroy($id)
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $currentUserId = session()->get('userId');
        if ($id == $currentUserId) {
            return redirect()->to('/users')->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        model('UserModel')->delete($id);

        return redirect()->to('/users')->with('success', 'User berhasil dihapus.');
    }

    // restore data
    public function restore($id)
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $db = db_connect();
        $db->table('users')->where('id', $id)->update(['deleted_at' => null]);

        return redirect()->to('/users')->with('success', 'User berhasil dikembalikan.');
    }

    // purge data
    public function purge($id)
    {
        $redirect = $this->adminOnly();
        if ($redirect) return $redirect;

        $currentUserId = session()->get('userId');
        if ($id == $currentUserId) {
            return redirect()->to('/users')->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        model('UserModel')->delete($id, true);

        return redirect()->to('/users')->with('success', 'User berhasil dihapus secara permanen.');
    }
}
