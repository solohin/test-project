<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 02/11/16
 * Time: 15:42
 */
namespace Solohin\ToptalExam\Database;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Connection;

class SchemaManager
{
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function flushDatabase()
    {
        $this->createTables(true);
    }

    public function createTables($flush = false)
    {
        $this->createUserTable($flush);
    }

    private function createUserTable($flush = false)
    {
        $schema = $this->conn->getSchemaManager();
        $tableName = 'users';

        if($flush && $schema->tablesExist($tableName)){
            $this->drop($tableName);
        }

        if (!$schema->tablesExist($tableName)) {
            $users = new Table($tableName);
            $users->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $users->setPrimaryKey(array('id'));
            $users->addColumn('username', 'string', array('length' => 32));
            $users->addUniqueIndex(array('username'));
            $users->addColumn('password_hash', 'string', array('length' => 255));
            $users->addColumn('roles', 'string', array('length' => 255));
            $users->addColumn('token', 'string', array('length' => 255));

            $schema->createTable($users);
        }
    }

    private function drop($tableName)
    {
        $this->conn->query('DROP TABLE ' . $this->conn->quote($tableName, \PDO::PARAM_STR));
    }
}
