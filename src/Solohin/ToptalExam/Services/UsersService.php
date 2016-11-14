<?php

namespace Solohin\ToptalExam\Services;

use PDO;
use Solohin\ToptalExam\Security\UserRoles;

class UsersService extends BaseService
{
    public function getOne($id)
    {
        return $this->postFormatUser(
            $this->db->fetchAssoc("SELECT id, username, password_hash as password, token, roles, daily_normal FROM users WHERE id=?", [(int)$id])
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
            $this->db->fetchAssoc("SELECT id, username, password_hash as password, token, roles,daily_normal FROM users WHERE username=?", [$username])
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
        $user = $this->prepareToSave($user, true);
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
            if (defined('THIS_IS_PHPUNIT')) {
                return rand(0, 100000000);
            } else {
                $token = password_hash(random_bytes(1024), PASSWORD_DEFAULT);
            }

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

    public static function hashPassword($password)
    {
        $cost = 12;
        if (defined('THIS_IS_PHPUNIT')) {
            $cost = 4;
        }
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }

    private function prepareToSave($user, $insert = false)
    {
        //Only first role
        if (isset($user['roles']) && is_array($user['roles'])) {
            $user['roles'] = isset($user['roles'][0]) ? $user['roles'][0] : UserRoles::DEFAULT_ROLE;
        }

        //On insert
        if ($insert) {
            //no role = default role
            if (!isset($user['roles'])) {
                $user['roles'] = UserRoles::DEFAULT_ROLE;
            }

            //create token
            $user['token'] = $this->generateUniqueToken();
        }

        if (isset($user['daily_normal'])) {
            if ($user['daily_normal'] < 0) {
                $user['daily_normal'] = 0;
            }
            $user['daily_normal'] = intval($user['daily_normal']);
        }


        if (isset($user['password'])) {
            $user['password_hash'] = self::hashPassword($user['password']);
            unset($user['password']);
        }
        return $user;
    }

    public function hasMorePages($page)
    {
        throw new \Exception('hasMorePages not implemented');
    }

    public function getAll()
    {
        $sql = "SELECT id, username, password_hash as password, token, roles,daily_normal FROM users ";
        $params = [];

        //sql
        $statement = $this->db->prepare($sql);
        foreach ($params as $index => $param) {
            $statement->bindValue($index + 1, $param[0], $param[1]);
        }
        $statement->execute();
        $rows = $statement->fetchAll();

        $result = [];
        foreach ($rows as $line) {
            $result[] = $this->postFormatUser($line);
        }
        return $result;
    }
}
