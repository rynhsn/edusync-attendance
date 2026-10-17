<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminNotificationsAndOnlineSessions extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('admin_notifications')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'type' => ['type' => 'VARCHAR', 'constraint' => 40],
                'title' => ['type' => 'VARCHAR', 'constraint' => 150],
                'message' => ['type' => 'VARCHAR', 'constraint' => 500],
                'target_url' => ['type' => 'VARCHAR', 'constraint' => 255],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['created_at'], false, false, 'idx_admin_notifications_created');
            $this->forge->createTable('admin_notifications');
        }

        if (! $this->db->tableExists('admin_notification_reads')) {
            $this->forge->addField([
                'notification_id' => ['type' => 'INT', 'unsigned' => true],
                'user_id' => ['type' => 'INT', 'unsigned' => true],
                'read_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey(['notification_id', 'user_id'], true);
            $this->forge->addKey(['user_id', 'read_at'], false, false, 'idx_admin_notification_reads_user');
            $this->forge->createTable('admin_notification_reads');
        }

        if (! $this->db->tableExists('admin_online_sessions')) {
            $this->forge->addField([
                'session_id' => ['type' => 'VARCHAR', 'constraint' => 128],
                'user_id' => ['type' => 'INT', 'unsigned' => true],
                'logged_in_at' => ['type' => 'DATETIME'],
                'last_seen_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('session_id', true);
            $this->forge->addKey(['user_id', 'last_seen_at'], false, false, 'idx_admin_online_sessions_user_seen');
            $this->forge->addKey(['last_seen_at'], false, false, 'idx_admin_online_sessions_seen');
            $this->forge->createTable('admin_online_sessions');
        }
    }

    public function down()
    {
        $this->forge->dropTable('admin_online_sessions', true);
        $this->forge->dropTable('admin_notification_reads', true);
        $this->forge->dropTable('admin_notifications', true);
    }
}
