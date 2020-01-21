<?php
    class Database {
        const DATABASE_CONNECTED = TRUE;
        const DATABASE_NOT_CONNECTED = FALSE;

        const ONE_RESULT = TRUE;

        const QUERY_TYPE_SELECT = 1;
        const QUERY_TYPE_UPDATE = 2;
        const QUERY_TYPE_INSERT = 3;
        const QUERY_TYPE_DELETE = 4;

        private $_databaseState = self::DATABASE_NOT_CONNECTED;
        private $_pdo_connector;
        private $_driver;
        private $_host;
        private $_name;
        private $_charset;
        

        private static $instance;

        public function __construct(string $driver, string $host, string $name, string $charset){
            if($driver == ""){
                throw new Exception("No driver detected. Please, put a valid driver in order to be able to connect yourself to your database.");
            } else {
                $this->_driver = $driver;
            }
            if($host == ""){
                throw new Exception("No host detected. Please, put a valid host in order to be able to connect yourself to your database.");
            } else {
                $this->_host = $host;
            }
            if($name == ""){
                throw new Exception("No name detected. Please, put a valid name in order to be able to connect yourself to your database.");
            } else {
                $this->_name = $name;
            }
            if($charset == ""){
                throw new Exception("No charset detected. Please, put a valid charset in order to be able to connect yourself to your database.");
            } else {
                $this->_charset = $charset;
            }
        }

        public function startConnection(){
            if($this->_databaseState == self::DATABASE_NOT_CONNECTED){
                
            }
        }

        public static function getInstance(){
            if(!isset(self::$instance)){
                self::$instance = new self;
            }
            self::$instance->startConnection();

            return self::$instance;
        }

        public function startConnectionOLD(){
            if($this->databaseState == self::DATABASE_NOT_CONNECTED){
                try{
                    if(Config::EXEC_MOD == Config::MOD_DEV){
                        $dsn =  Config::DATABASE_DRIVER_DEV.':'
                                .'host='.Config::DATABASE_HOST_DEV.';'
                                .'dbname='.Config::DATABASE_NAME_DEV.';'
                                .'charset='.Config::DATABASE_CHARSET_DEV;
                        $this->pdo_connector = new PDO($dsn, Config::DATABASE_USERNAME_DEV, Config::DATABASE_PASSWORD_DEV);
                        $this->databaseState = self::DATABASE_CONNECTED;
                    } elseif(Config::EXEC_MOD == Config::MOD_PROD){
                        $dsn =  Config::DATABASE_DRIVER_PROD.':'
                                .'host='.Config::DATABASE_HOST_PROD.';'
                                .'dbname='.Config::DATABASE_NAME_PROD.';'
                                .'charset='.Config::DATABASE_CHARSET_PROD;
                        $this->pdo_connector = new PDO($dsn, Config::DATABASE_USERNAME_PROD, Config::DATABASE_PASSWORD_PROD);
                        $this->databaseState = self::DATABASE_CONNECTED;
                    }                    
                } catch (PDOException $e){
                    die('Error DatabaseEC::startConnection() : ' . $e->getMessage());
                }
            }
            return $this->databaseState;
        }

        public function runSQL(string $query, array $args = array(), int $queryType, bool $unique_result = false){
            if($this->databaseState == self::DATABASE_CONNECTED){
                try{
                    $nbArgs = mb_substr_count($query , '?');
                    if($nbArgs != count($args)) throw new Exception('Error DatabaseEC::runSQL() : Number of `?` must be the same as the number of arguments in $args');
                    $result = $this->pdo_connector->prepare($query);

                    switch($queryType){
                        case self::QUERY_TYPE_SELECT:
                            $result->execute($args);
                            $result = $result->fetchAll();
                            if($unique_result && count($result) > 0) $result = $result[0];
                            return $result;
                            break;
                        case self::QUERY_TYPE_UPDATE:
                            $result->execute($args);
                            return $result->rowCount();
                            break;
                        case self::QUERY_TYPE_INSERT:
                            if($result->execute($args)){
                                return $this->pdo_connector->lastInsertId();
                            } else {
                                return FALSE;
                            }
                            break;
                        case self::QUERY_TYPE_DELETE:
                            $result->execute($args);
                            break;
                        default:
                            throw new Exception('Error DatabaseEC::runSQL() : Unknown query type.');
                            break;
                    }
                } catch(Exception $e){
                    if(Config::GLOBAL_DEATH_ENABLED){
                        die($e->getMessage());
                    } else {
                        return FALSE;
                    }
                }
            } else {
                throw new Exception('Error DatabaseEC::runSQL() : The database is not connected');
            }
        }
    }
?>