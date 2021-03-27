<?php

namespace QuinenLib\Db;

trait MysqlTrait
{
    private $db;
    private $host = '127.0.0.1';
    private $port = 3306;
    private $dbname;
    private $charset = 'utf8mb4';
    private $options = [
        // a la moindre erreur, exception remontée
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        // pas de prepare
        \PDO::ATTR_EMULATE_PREPARES => false,
        // nom des champs uniquement
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
    ];

    public function getDsn()
    {
        $dsn = 'mysql:';
        $dsn .= 'host=' . $this->getHost() . ';';
        $dsn .= 'port=' . $this->getPort() . ';';
        $dsn .= 'charset=' . $this->getCharset() . ';';

        // dbname
        // mysql_connect + mysql_select_db = __construct + $db->exec('USE DB;')
        $dbname = $this->getDbname();

        if ($dbname !== null) {
            $dsn .= 'dbname=' . $dbname . ';';
        }

        return $dsn;
    }

    /**
     * @return \PDO
     */
    public function getDb()
    {
        if (!$this->db) {

            $this->setDb();
        }
        return $this->db;
    }

    public function setDb($username = null, $passwd = null, $options = null)
    {
        if ($options !== null) {
            $this->setOptions($options + $this->getOptions());
        }
        try {
            $this->db = new \PDO($this->getDsn(), $username, $passwd, $this->getOptions());
        } catch (\PDOException $e) {
            if ($e->getCode() === 2002) {
                // iloveswenew.mysql.db
                echo $this->getHost() . ':' . $this->getPort() . ' inconnu';
            } else {
                debug_lite($e);
            }
            die();
        }
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     * @return MysqlTrait
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * @param mixed $dbname
     * @return MysqlTrait
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return MysqlTrait
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     * @return MysqlTrait
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return MysqlTrait
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}