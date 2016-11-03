<?php

namespace Solohin\ToptalExam\Services;

class UsersService extends BaseService
{

    public function getOne($id)
    {
        return $this->db->fetchAssoc("SELECT id, username, password_hash as password, token FROM users WHERE id=?", [(int)$id]);
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
        return $this->db->fetchAssoc("SELECT id, username, password_hash as password, token FROM users WHERE username=?", [$username]);
    }

    public function insert($user)
    {
        if(!isset($user['roles']) || is_null($user['roles'])){
            $user['roles'] = '';
        }
        $user = $this->prepareToSave($user);
        $this->db->insert("users", $user);
        return $this->db->lastInsertId();
    }

    /**
     * @param $id
     * @param $user array ['username', 'password', 'token'], password will be hashed
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
