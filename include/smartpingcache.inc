<?php
 
namespace Mping\CoreBundle\Cache;
 
use Doctrine\DBAL\Schema\Table;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Connection;
 
/**
 * @author VincentBab vincentbab@gmail.com
 */
class DatabaseCache implements Cache
{
    private $connection;
    private $tableName;
 
    public function __construct(Connection $connection, $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
 
        if (!$this->connection->getSchemaManager()->tablesExist($this->tableName)) {
            $table = new Table($tableName);
            $table->addColumn('id', 'string');
            $table->addColumn('data', 'text');
            $table->addColumn('expire', 'integer');
            $table->setPrimaryKey(array('id'));
            $table->addIndex(array('expire'));
 
            $this->connection->getSchemaManager()->createTable($table);
        }
    }
 
    public function fetch($id)
    {
        $result = $this->doFetch($id);
 
        if ($result) {
            return unserialize($result['data']);
        }
 
        return false;
    }
 
    public function contains($id)
    {
        $result = $this->doFetch($id);
 
        return (bool)$result;
    }
 
    public function save($id, $data, $lifeTime = 0)
    {
        $expire = time() + $lifeTime;
        $data = serialize($data);
 
        $this->connection->beginTransaction();
        $this->connection->setTransactionIsolation(Connection::TRANSACTION_SERIALIZABLE);
        try {
            $this->connection->delete($this->tableName, array('id' => $id));
 
            $this->connection->insert($this->tableName, array(
                'id' => $id,
                'data' => $data,
                'expire' => $expire,
            ));
 
            $this->connection->commit();
        } catch(\Exception $e) {
            $this->connection->rollback();
 
            return false;
        }
 
        return true;
    }
 
    public function delete($id)
    {
        $this->connection->delete($this->tableName, array('id' => $id));
 
        return true;
    }
 
    public function getStats()
    {
        return array(
            Cache::STATS_HITS               => null,
            Cache::STATS_MISSES             => null,
            Cache::STATS_UPTIME             => null,
            Cache::STATS_MEMORY_USAGE       => null,
            Cache::STATS_MEMORY_AVAILABLE   => null,
        );
    }
 
    private function doFetch($id)
    {
        $result = $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE id = :id AND expire > :expire", array(
            'id' => $id,
            'expire' => time(),
        ));
 
        return isset($result[0]) ? $result[0] : false;
    }
}