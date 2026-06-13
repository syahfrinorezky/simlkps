<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "BIGINT", "constraint" => 20, "unsigned" => true, "auto_increment" => true],
            "role_id" => ["type" => "BIGINT", "constraint" => 20, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36, "null" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36, "null" => true],
            "nama_lengkap" => ["type" => "VARCHAR", "constraint" => 200],
            "email" => ["type" => "VARCHAR", "constraint" => 150, "unique" => true],
            "password" => ["type" => "VARCHAR", "constraint" => 255],
            "telepon" => ["type" => "VARCHAR", "constraint" => 20, "null" => true],
            "is_active" => ["type" => "BOOLEAN", "default" => true],
            "last_login_at" => ["type" => "DATETIME", "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
            "deleted_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("role_id", "roles", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("users");
    
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
