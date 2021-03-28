<?php


namespace QuinenLib\Legacy;

use Cake\Database\Statement\PDOStatement;
use Cake\Utility\Hash;
use QuinenLib\Db\MysqlTrait;

/**
 * Class Mysql destiné a remplacer les anciens appel mysql_*
 * @package QuinenLib\Legacy
 */
class Mysql
{
    use Singleton;
    use MysqlTrait;

    public $executed;

    /**
     * $db = QuinenLib\Legacy\Mysql::connect($dbhost, $dblogin, $dbpassword);
     */
    public static function connect($host, $username, $password)
    {
        $db = self::getInstance();
        $db->setHost($host);
        $db->setDb($username, $password);
        return $db->getDb();
    }

    public static function selectDb($dbname, $db = null)
    {
        if ($db === null) {
            $db = self::getInstance()->getDb();
        }
        $db->exec('USE ' . filter_var($dbname, FILTER_SANITIZE_STRING));
        return true;
    }

    public static function query($query, $input_parameters = [])
    {
        $db = self::getInstance()->getDb();
        try {
            $res = $db->prepare($query);
            $res->execute($input_parameters);
        } catch (\PDOException $e) {
            $res = false;
            if ($e->getCode() === '42S02') {
                echo $e->getMessage();
            } else {
                debug_lite($e);
            }
            die();
        }

        return $res;
    }

    public static function result(\PDOStatement $stmt, $row, $field = 0)
    {
        $all = $stmt->fetchAll();
        $stmt->execute();
        $res = Hash::get($all, $row . '.' . $field);

        if ($res === null) {
            //debug_lite([$all, $row . '.' . $field]);
            //debug_lite(debug_backtrace());
        } else {
            //debug_lite($res);
        }
        return $res;
    }

    public static function close(\PDO $db = null)
    {
        if ($db === null) {
            $db = self::getInstance()->getDb();
        }
        $db = null;
    }

    public static function numRows(\PDOStatement $stmt)
    {
        return $stmt->rowCount();
    }

    public static function fetchArray(\PDOStatement $stmt)
    {
        return $stmt->fetch(\PDO::FETCH_BOTH);
    }

    public static function insertId(\PDO $db = null)
    {
        if ($db === null) {
            $db = self::getInstance()->getDb();
        }
        return $db->lastInsertId();
    }


}