<?php
    /**
     * DBC (DataBase Connector) est une classe créée dans le but de pouvoir se connecter à un **serveur MySQL** et lister les bases de données présentes et s'y connecter et interagir avec.
     * 
     * @author Martin Crampon <cyborg.projectdev@gmail.com>
     */
    class DBC {
        const DBC_SET_GC = 'setting : GENERAL CONNECTOR'; // set to be connected only to the MySQL server, not to be connected to a database
        const DBC_SET_DC = 'setting : DATABASE CONNECTOR'; // set to be connected to a unique database
        const DBC_STATUS_CONNECTED = TRUE;
        const DBC_STATUS_UNCONNECTED = FALSE;

        const ERR_ALR_CON = 1; // Error : Already Connected
        const ERR_UKN_SET = 2; // Error : Unknown Setting

        const UNIQUE_RESULT = TRUE;
        const QUERY_TYPE_SELECT = 1;
        const QUERY_TYPE_UPDATE = 2;
        const QUERY_TYPE_INSERT = 3;
        const QUERY_TYPE_DELETE = 4;


        private $_setting;
        private $_status;

        private $_host;
        private $_user;
        private $_password;
        private $_database;
        private $_charset;

        private $_pdo_connector;

        /**
         * Generate a DBC object
         * 
         * @param string $host Put here the URL or the name of your database host (ex: http://example.com, localhost)
         * @param string $user 
         * @param string $password
         * @param string $database (Optionnal) Put here the **name** of the database you to connect. If nothing is given, the DBC will only connect the MySQL server.
         * @param string $charset (Default UTF-8)
         * 
         * @throws Exception when a non optionnal param is missing.
         * 
         * @return DBC object (not connected)
         */
        public function __construct(string $host, string $user, string $password, string $database = null, string $charset = 'utf8') {
            if($host == '') throw new Exception("Host empty, an hostname is needed.");

            if($database == null): $this->_setting = self::DBC_SET_GC;
            else: $this->_setting = self::DBC_SET_DC;endif;

            $this->_status = self::DBC_STATUS_UNCONNECTED;
            $this->_host = $host;
            $this->_user = $user;
            $this->_password = $password;
            $this->_database = $database;
            $this->_charset = $charset;
        }

        /**
         * Start a connection between the DBC object and the server/database targetted
         * 
         * @throws Exception
         * 
         * @return bool $this->_status
         */
        public function startConnection(){
            if($this->_status == self::DBC_STATUS_UNCONNECTED){
                if($this->_setting == self::DBC_SET_GC){
                    $dsn = "mysql:host=".$this->_host;
                } elseif($this->_setting == self::DBC_SET_DC) {
                    if($this->_database == null) throw new Exception("No database detected with non general DBC");
                    $dsn = "mysql:host=".$this->_host.";dbname=".$this->_database.";charset=".$this->_charset;
                } else {
                    throw new Exception("Unknown DBC setting.", self::ERR_UKN_SET);
                }
                $this->_pdo_connector = new PDO($dsn, $this->_user, $this->_password);
                $this->_status = self::DBC_STATUS_CONNECTED;

                return $this->_status;
            } else {
                throw new Exception("DBC already connected.", self::ERR_ALR_CON);
            }
        }
    }
?>