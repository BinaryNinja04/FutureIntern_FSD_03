<?php
class Database {
    private $con;
    
    function __construct() {
        $this->con = $this->connect();
    }
    
    function connect() {
        $string = "mysql:host=localhost;dbname=mychat_db";
        try {
            $connection = new PDO($string, DBUSER, DBPASS);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch(PDOException $e) {
            echo $e->getMessage();
            die;
        }
        return false;
    }

    // Write to database
    public function write($query, $data_array = []) {
        $con = $this->connect();
        $statement = $con->prepare($query);

        foreach($data_array as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        try {
            $check = $statement->execute($data_array);
            if ($check) {
                return true;
            }
        } catch (PDOException $e) {
            // Log error for debugging
            file_put_contents('pdo_errors.log', $e->getMessage() . "\n", FILE_APPEND);
        }
        return false;
    }

    //read from database
    public function read($query, $data_array = []) {
        $con = $this->connect();
        $statement = $con->prepare($query);
    
        foreach ($data_array as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
    
        try {
            $result = $statement->execute();
            if ($result) {
                $results = $statement->fetchAll(PDO::FETCH_OBJ);
                if (is_array($results) && count($results) > 0) {
                    return $results;
                }
            }
        } catch (PDOException $e) {
            // Log error for debugging
            file_put_contents('pdo_errors.log', $e->getMessage() . "\n", FILE_APPEND);
        }
    
        return false;
    }

    public function get_user($userid) 
    {
        $con = $this->connect();
        $arr['userid'] = $userid;
        $query = "select * from users where userid = :userid limit 1";
        $statement = $con->prepare($query);
        $check = $statement->execute($arr);
    
        if ($check) {
            $result = $statement->fetchAll(PDO::FETCH_OBJ);
            if(is_array($result) && count($result) > 0) {
                return $result[0];
            }
            return false;
        }    
        return false;
    }
    
    public function generate_id($max) {
        $rand = "";
        $rand_count = rand(4, $max);
        for ($i = 0; $i < $rand_count; $i++) {
            $r = rand(0, 9);
            $rand .= $r;
        }
        return $rand;
    }
}
