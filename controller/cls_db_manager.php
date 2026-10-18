<?php
class DB_manager {

    private $db_config_file;
    private $host;
    private $port;
    private $charset;
    private $database;
    private $user;
    private $password;
    private $dsn;
    public $con;

    public function __construct($conf_file) {
        $this->db_config_file = $conf_file;
        $this->read_config_file();  
    }

    private function read_config_file(){
        if (file_exists($this->db_config_file)) {
            $handle = fopen($this->db_config_file,"r");
            if ($handle) {
                while ($row = fgets($handle)) {
                    $segment = explode(":", $row);
                    $attribute = mb_strtolower($segment[0], 'UTF-8');
                    $this->$attribute = trim($segment[1]);
                };
                $hst = $this->host.($this->port===null?"":":".$this->port);
                $this->dsn = "mysql:host=$hst;dbname=$this->database;charset=$this->charset";
                fclose($handle);
            }
        }
    }

    public function connect(){
        try {
            return new PDO($this->dsn, $this->user, $this->password);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function run($qry) {
        try{
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            die();
        }
    }

    public function check($con) {
    
        // 1. Kontrollera om variabeln överhuvudtaget är ett PDO-objekt
        if (!$con instanceof PDO) {
            return [
                'ansluten' => false,
                'databas'  => null,
                'meddelande' => 'Variabeln är inte ett giltigt PDO-objekt.'
            ];
        }

        try {
            // 2. Pinga servern genom att hämta serverinfo. 
            // Om anslutningen dött ("Gone away") kastas ett undantag här.
            $con->getAttribute(PDO::ATTR_SERVER_INFO);

            // 3. Kör en lätt SQL-fråga för att få namnet på den aktiva databasen
            // (Fungerar i MySQL/MariaDB)
            $db_name = $con->query('SELECT DATABASE()')->fetchColumn();

            return [
                'ansluten' => true,
                'databas'  => $db_name ?: 'Ingen databas vald (men ansluten till server)',
                'meddelande' => 'Anslutningen är aktiv.'
            ];

        } catch (PDOException $e) {
            // Hit kommer vi om servern har stängts ner eller tapat kontakten
            return [
                'ansluten' => false,
                'databas'  => null,
                'meddelande' => 'Anslutningen har brutits: ' . $e->getMessage()
            ];
        }
    }

    public function dump(){
        echo "<hr>DB Manager Dump";
        echo "<br>Config file: "; var_dump($this->db_config_file);
        echo "<br>Host: "; var_dump($this->host);
        echo "<br>Port: "; var_dump($this->port);
        echo "<br>Charset: "; var_dump($this->charset);
        echo "<br>Database: "; var_dump($this->database);
        echo "<br>User: "; var_dump($this->user);
        echo "<br>Password: "; var_dump($this->password);
        echo "<br>DSN: "; var_dump($this->dsn);
        echo "<hr>";
    }
}