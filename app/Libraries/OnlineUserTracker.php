<?php

namespace App\Libraries;

class OnlineUserTracker
{
    public static function activeUsers(): array
    {
        $db = db_connect();
        if (! $db->tableExists('admin_online_sessions')) {
            log_message('error', 'The admin_online_sessions table is missing. Run pending migrations to enable online-user tracking.');

            return [];
        }

        $activeSince = date('Y-m-d H:i:s', time() - 300);
        $expiredSessionsRemoved = $db->table('admin_online_sessions')
            ->where('last_seen_at <', date('Y-m-d H:i:s', time() - 7200))
            ->delete();
        if (! $expiredSessionsRemoved) {
            log_message('error', 'Could not remove expired online-session records.');
        }

        $sessions = $db->table('admin_online_sessions s')
            ->select('u.user_id, u.display_name, u.username, u.email, u.role, s.logged_in_at, s.last_seen_at')
            ->join('auth_users u', 'u.user_id = s.user_id')
            ->where('s.last_seen_at >=', $activeSince)
            ->where('u.is_verified', 1)
            ->orderBy('s.last_seen_at', 'DESC')
            ->get()
            ->getResultArray();

        $users = [];
        foreach ($sessions as $session) {
            $userId = (int) $session['user_id'];
            if (! isset($users[$userId])) {
                $users[$userId] = $session;
            }
            if ($session['logged_in_at'] < $users[$userId]['logged_in_at']) {
                $users[$userId]['logged_in_at'] = $session['logged_in_at'];
            }
        }

        return array_values($users);
    }

    public static function touch(): void
    {
        $user = session()->get('authUser');
        $sessionId = session_id();
        $userId = (int) ($user['user_id'] ?? 0);
        if ($userId < 1 || $sessionId === '') {
            return;
        }

        $db = db_connect();
        if (! $db->tableExists('admin_online_sessions')) {
            log_message('error', 'The admin_online_sessions table is missing. Run pending migrations to enable online-user tracking.');

            return;
        }

        $existing = $db->table('admin_online_sessions')
            ->select('session_id')
            ->where('session_id', $sessionId)
            ->get()
            ->getRowArray();
        $now = date('Y-m-d H:i:s');

        if ($existing !== null) {
            $saved = $db->table('admin_online_sessions')
                ->where('session_id', $sessionId)
                ->update(['user_id' => $userId, 'last_seen_at' => $now]);
        } else {
            $saved = $db->table('admin_online_sessions')->insert([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'logged_in_at' => $now,
                'last_seen_at' => $now,
            ]);
        }

        if (! $saved) {
            log_message('error', 'Could not update active-session information for user {userId}.', [
                'userId' => $userId,
            ]);
        }
    }

    public static function removeCurrent(): void
    {
        $sessionId = session_id();
        if ($sessionId === '') {
            return;
        }

        $db = db_connect();
        if (! $db->tableExists('admin_online_sessions')) {
            log_message('error', 'The admin_online_sessions table is missing. Run pending migrations to enable online-user tracking.');

            return;
        }

        $deleted = $db->table('admin_online_sessions')
            ->where('session_id', $sessionId)
            ->delete();
        if (! $deleted) {
            log_message('error', 'Could not remove active-session information during logout.');
        }
    }
}
