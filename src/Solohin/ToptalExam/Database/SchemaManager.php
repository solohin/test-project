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
        $this->createNotesTable($flush);
    }

    private function createUserTable($flush = false)
    {
        $schema = $this->conn->getSchemaManager();
        $tableName = 'users';

        if ($flush && $schema->tablesExist($tableName)) {
            $this->drop($tableName);
        }
        //TODO add indexes

        if (!$schema->tablesExist($tableName)) {
            $users = new Table($tableName);
            $users->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
            $users->setPrimaryKey(['id']);
            $users->addColumn('username', 'string', ['length' => 32]);
            $users->addUniqueIndex(['username']);
            $users->addColumn('password_hash', 'string', ['length' => 255]);
            $users->addColumn('roles', 'string', ['length' => 255]);
            $users->addColumn('token', 'string', ['length' => 255]);
            $users->addColumn('daily_normal', 'integer', ['unsigned' => true, 'default' => 2000]);

            $schema->createTable($users);
        }
    }

    private function createNotesTable($flush = false)
    {
        $schema = $this->conn->getSchemaManager();
        $tableName = 'notes';

        if ($flush && $schema->tablesExist($tableName)) {
            $this->drop($tableName);
        }
        //TODO add indexes

        if (!$schema->tablesExist($tableName)) {
            $users = new Table($tableName);
            $users->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
            $users->setPrimaryKey(['id']);
            $users->addColumn('user_id', 'string', ['length' => 255]);
            $users->addColumn('calories', 'integer', ['unsigned' => true]);
            $users->addColumn('time', 'integer', ['unsigned' => true]);//count of seconds from 00:00
            $users->addColumn('date', 'integer', ['unsigned' => true]);//timestamp
            $users->addColumn('text', 'text');

            $schema->createTable($users);
        }
    }

    private function drop($tableName)
    {
        //We know that $tableName is safe from SQL injections
        $this->conn->query('DROP TABLE ' . $tableName);
    }
}
