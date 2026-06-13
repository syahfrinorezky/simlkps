<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        $email    = 'syahfrino.27@gmail.com';
        $password = password_hash('admin123', PASSWORD_DEFAULT);

        $db->table('users')->where('email', $email)->delete();

        $roles = ['admin', 'prodi', 'dosen', 'asesor'];
        foreach ($roles as $roleName) {
            $existingRole = $db->table('roles')->where('nama', $roleName)->get()->getRow();
            if (!$existingRole) {
                $db->table('roles')->insert([
                    'nama' => $roleName,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $adminRole = $db->table('roles')->where('nama', 'admin')->get()->getRow();
        $roleId = $adminRole->id;

        $db->table('users')->insert([
            'nama_lengkap' => 'Rino',
            'email'        => $email,
            'password'     => $password,
            'role_id'      => $roleId,
            'is_active'    => 1,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
