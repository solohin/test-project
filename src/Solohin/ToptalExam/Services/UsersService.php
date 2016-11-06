<?php

namespace Solohin\ToptalExam\Services;

class UsersService extends BaseService
{
    public function getOne($id)
    {
        return $this->postFormatUser(
            $this->db->fetchAssoc("SELECT id, username, password_hash as password, token, roles FROM users WHERE id=?", [(int)$id])
        );
    }

    public function isTokenExists($token)
    {
        $result = $this->db->fetchAssoc('SELECT 1 AS token_exists FROM users WHERE token = ?', [$token]);
        return isset($result['token_exists']);
    }

    /**
     * @param $username
     * @return array|false
     */
    public function getByUsername($username)
    {
        return $this->postFormatUser(
            $this->db->fetchAssoc("SELECT id, username, password_hash as password, token, roles FROM users WHERE username=?", [$username])
        );
    }

    private function postFormatUser($user)
    {
        if (!$user) {
            return $user;
        } else {
            $user['roles'] = explode(',', $user['roles']);
            return $user;
        }
    }

    public function insert($user)
    {
        if (!isset($user['roles']) || is_null($user['roles'])) {
            $user['roles'] = '';
        }
        $user['token'] = $this->generateUniqueToken();

        $user = $this->prepareToSave($user);
        $this->db->insert("users", $user);
        return $this->db->lastInsertId();
    }

    public function getUserByToken($token)
    {
        $result = $this->db->fetchAssoc("SELECT id, username, password_hash as password, token, roles FROM users WHERE token=?", [$token]);
        $result['roles'] = explode(',', $result['roles']);
        return $result;
    }

    public function generateUniqueToken($userId = null)
    {
        do {
            $token = password_hash(random_bytes(1024), PASSWORD_DEFAULT);
        } while ($this->isTokenExists($token));
        return $token;
    }

    /**
     * @param $id
     * @param $user array ['username', 'password', 'token'], password will be hashed. empty token will be generated
     * @return int
     */
    public function update($id, $user)
    {
        $user = $this->prepareToSave($user);
        return $this->db->update('users', $user, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete("users", array("id" => $id));
    }

    public static function hashPassword($password_hash)
    {
        return password_hash($password_hash, PASSWORD_DEFAULT);
    }

    private function prepareToSave($user)
    {
        if (isset($user['password'])) {
            $user['password_hash'] = self::hashPassword($user['password']);
            unset($user['password']);
        }
        return $user;
    }

}
