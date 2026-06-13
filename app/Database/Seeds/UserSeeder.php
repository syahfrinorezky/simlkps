<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        $users = [
            [
                'role'         => 'dosen',
                'email'        => '2415323031@pnb.ac.id',
                'nama_lengkap' => 'Dosen',
                'password'     => password_hash('dosen123', PASSWORD_DEFAULT),
            ],
            [
                'role'         => 'asesor',
                'email'        => 'syahfrino.rezky28@sma.belajar.id',
                'nama_lengkap' => 'Asesor',
                'password'     => password_hash('asesor123', PASSWORD_DEFAULT),
            ],
        ];

        foreach ($users as $user) {
            $role = $db->table('roles')->where('nama', $user['role'])->get()->getRow();
            if (!$role) {
                echo "Role '{$user['role']}' tidak ditemukan, lewati user {$user['email']}.\n";
                continue;
            }

            $db->table('users')->where('email', $user['email'])->delete();

            $db->table('users')->insert([
                'nama_lengkap' => $user['nama_lengkap'],
                'email'        => $user['email'],
                'password'     => $user['password'],
                'role_id'      => $role->id,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            echo "User '{$user['email']}' berhasil dibuat dengan role '{$user['role']}'.\n";
        }
    }
}
