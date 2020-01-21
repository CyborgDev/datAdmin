<?php
    /**
     * DBConnector est une classe créée dans le but de pouvoir se connecter à différentes bases de données, que ce soit en même temps ou une par une.
     * Pour faire cela, il suffit de creer des objets DBConnector avec les informations correspondantes.
     * 
     * @author Martin Crampon <cyborg.projectdev@gmail.com>
     */
    class DBConnector {
        const DATABASE_CONNECTED = TRUE;
        const DATABASE_NOT_CONNECTED = FALSE;
        const ONE_RESULT = TRUE;
        const QUERY_TYPE_SELECT = 1;
        const QUERY_TYPE_UPDATE = 2;
        const QUERY_TYPE_INSERT = 3;
        const QUERY_TYPE_DELETE = 4;

        private $databaseState = self::DATABASE_NOT_CONNECTED;
        private $pdo_connector;

        public function __construct(){
            
        }

        public static function getInstance(){
            if(!isset(self::$instance)){
                self::$instance = new self;
            }
            self::$instance->startConnection();

            return self::$instance;
        }

        public function startConnection(){
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
    }
?>