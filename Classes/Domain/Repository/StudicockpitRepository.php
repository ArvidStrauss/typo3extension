<?php
namespace AppsTeam\HtwddStudicockpit\Domain\Repository;

/**
 *@package	Studicockpit
 *@author   Apps-Team HTW Dresden <apps@htw-dresden.de>
 *@license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * This file is part of the "SC-Utility" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Apps-Team <apps@htw-dresden.de>, HTW Dresden
 *
 ***/

/**
 * The repository for Sc-Utility
 */
class StudicockpitRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     * Database name for database connection
     *
     * @var string
     */
    private $database = null;

    /**
     * Server name for database connection
     *
     * @var string
     */
    private $server = null;

    /**
     * Username for database connection
     *
     * @var string
     */
    private $username = null;

    /**
     * Password for database connection
     *
     * @var string
     */
    private $password = null;
    

    public function setDatabase($database) {
        $this->database = $database;
    }

    public function setServer($server) {
        $this->server = $server;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getServer() {
        return $this->server;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    /**
     * Establishs a connection to the database
     *
     * @return PDO $connection Database connection
     */
    public function connect() {
        $dsn = "informix:host=" . $this->server . ";service=ids_ids1;database=" . $this->database . ";server=ids6;protocol=onsoctcp;EnableScrollableCursors=1;CLIENT_LOCALE=de_DE.utf8";
        try {
            $connection = new \PDO($dsn, $this->username, $this->password);
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        catch(\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage() . '<br>';
        }

        return $connection;
    }

    /**
     * Get record(s) by SQL-Query
     *
     * @param string $query Query
     * @return array $records Array of records
     */
    public function getRecords($query) {
        if($query) {
            $connection = $this->connect();
            $getOffers = $connection->prepare($query);
            $getOffers->execute();

            while($row = $getOffers->fetch(\PDO::FETCH_ASSOC))
                $records[] = $row;

            $getOffers->closeCursor();
            return $records;
        } 
        else {
            return false;
        }
    }

    /**
     * Insert record(s) by SQL-Query
     *
     * @param string $query Query
     * @param string $idColumn Column with Serial/id
     * @return integer last inserted serial/id
     */
    public function insertMainRecords($query, $idColumn) {
        if($query) {
            $connection = $this->connect();
            $insertOffers = $connection->prepare($query);
            $isSuccessful = $insertOffers->execute();

            $error = $connection->errorInfo();
            $returnArray = array('status' => $isSuccessful, 'message' => $error[2], 'id' => $connection->lastInsertId($idColumn));
            $insertOffers->closeCursor();
            return $returnArray;
        }
        else {
            return false;
        }
    }

    /**
     * Get record(s) by SQL-Query
     *
     * @param string $query Query
     * @return array $records Array of records
     */
    public function executeManipulationQuery($query) {
        if($query) {
            $connection = $this->connect();
            $executeQuery = $connection->prepare($query);
            $isSuccessful = $executeQuery->execute();

            $executeQuery->closeCursor();
            return $isSuccessful;
        }
        else {
            return false;
        }
    }
}