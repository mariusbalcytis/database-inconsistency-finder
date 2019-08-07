<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencedColumn;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencesConfiguration;
use Maba\DatabaseInconsistencyFinder\Entity\TableReferences;
use PHPUnit\Framework\TestCase;

abstract class FunctionalTestCase extends TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var ReferencesConfiguration
     */
    protected $referencesConfiguration;

    protected function setUp()
    {
        parent::setUp();

        $this->initializeDatabaseStructure();
        $this->setUpReferenceConfiguration();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->dropDatabase();
    }

    protected function initializeDatabaseStructure()
    {
        $this->connection = DriverManager::getConnection([
            'user' => 'root',
            'password' => '',
            'host' => $_ENV['TEST_DATABASE_HOST'] ?? 'localhost',
            'driver' => 'pdo_mysql',
        ]);
        $this->connection->executeQuery('DROP DATABASE IF EXISTS test');
        $this->connection->executeQuery('CREATE DATABASE test');
        $this->connection->executeQuery('USE test');

        $this->connection->executeQuery('
            CREATE TABLE files (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                reference_count INT NOT NULL
            )
            ENGINE=InnoDB
        ');
        $this->connection->executeQuery('
            CREATE TABLE profiles (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                avatar_file_id INT NOT NULL,
                cv_file_id INT NOT NULL
            )
            ENGINE=InnoDB
        ');
    }

    protected function setUpReferenceConfiguration()
    {
        $this->referencesConfiguration = (new ReferencesConfiguration())
            ->setReferencedColumn(
                (new ReferencedColumn())
                ->setConnection($this->connection)
                ->setTableName('files')
                ->setIdColumnName('id')
                ->setReferenceNumberColumnName('reference_count')
            )
            ->addTableReferences(
                (new TableReferences())
                ->setConnection($this->connection)
                ->setTableName('profiles')
                ->setColumnNames(['avatar_file_id', 'cv_file_id'])
            )
        ;
    }

    protected function dropDatabase()
    {
        if ($this->connection !== null) {
            $this->connection->executeQuery('DROP DATABASE IF EXISTS test');
        }
    }

    public function prepareWithValidData($count = 100)
    {
        $fileId = 0;
        for ($profileId = 1; $profileId <= $count; $profileId++) {
            $this->connection->executeQuery('INSERT INTO files VALUES (:id, :reference_count)', [
                ':id' => $fileId + 1,
                ':reference_count' => 1,
            ]);
            $this->connection->executeQuery('INSERT INTO files VALUES (:id, :reference_count)', [
                ':id' => $fileId + 2,
                ':reference_count' => 1,
            ]);
            $this->connection->executeQuery('INSERT INTO profiles VALUES (NULL, :avatar_file_id, :cv_file_id)', [
                ':avatar_file_id' => $fileId + 1,
                ':cv_file_id' => $fileId + 2,
            ]);

            $fileId += 2;
        }
    }

    public function prepareWithOrphan($count = 100)
    {
        $this->prepareWithValidData($count);
        $this->connection->executeQuery('INSERT INTO files VALUES (NULL, 3)');
    }

    public function prepareWithInvalidReference($count = 100)
    {
        $this->prepareWithValidData($count);

        $invalidId = $count * 3;

        $this->connection->executeQuery('INSERT INTO profiles VALUES (NULL, :avatar_file_id, :cv_file_id)', [
            ':avatar_file_id' => $invalidId + 1,
            ':cv_file_id' => $invalidId + 2,
        ]);
    }

    public function prepareWithInvalidReferenceCount($count = 100)
    {
        $this->prepareWithValidData($count);

        $this->connection->executeQuery('UPDATE files SET reference_count = reference_count + 1 WHERE id = 1');
    }
}
