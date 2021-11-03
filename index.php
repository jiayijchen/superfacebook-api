<?php
session_start();
// Session to store information
// store an array, push, pop, splice

$action = $_GET["action"];

//echo "<pre>" . print_r($action, 1) . "</pre>";

if($action != ""){
    switch ($action) {
        case "create":
            createHero($_GET["name"], $_GET["tagline"]);
            break;
        case "read":
            //readAllHeroes();
            break;
        case "update":
            updateHero($_GET["id"], $_GET["name"], $_GET["tagline"]);
            break;
        case "delete":
            deleteHero($_GET["id"]);
            break;
        default:
        
            init();
        }
    
}
readAllHeroes();

function init (){
    unset($_SESSION["heroes"]);
    $_SESSION["heroes"] = [];
}

// create
function createHero ($name, $tagline){
    //echo "<h1>CREATE</h1><pre>" . print_r([$name, $tagline], 1) . "</pre>";
    // push to the hero array
    array_push($_SESSION["heroes"], [$name, $tagline]); // indexed array [0]

    // add hero to db

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "heroes_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO heroes (nickname, tagline)
    VALUES ('$name', '$tagline')";

    if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
    } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// read
function readAllHeroes (){
    // output heroes from the array
    $dbConn = new DatabaseConnector();

    $heroes = $dbConn->getAllHeroes();

    //echo "<pre>" . print_r($heroes, 1) . "</pre>";

    $json = json_encode($heroes);
    echo $json;
}

function updateHero($id, $name, $tagline){
    //
    //array_splice($_SESSION["heroes"],$index,1,[[$name, $tagline]]);
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "heroes_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE heroes SET tagline='$tagline', nickname='$name' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
    } else {
    echo "Error updating record: " . $conn->error;
    }
    $conn->close();

}

function deleteHero($id){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "heroes_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    // sql to delete a record
    $sql = "DELETE FROM heroes WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
    } else {
    echo "Error deleting record: " . $conn->error;
    }
}

function createNewDatabase (){
    $servername = "localhost";
    $username = "root";
    $password = "";

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Create database
    $sql = "CREATE DATABASE " . $_GET["dbname"];
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    $conn->close();
}

function createTableInDB($db){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = $db;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Create database
    $sql = "CREATE TABLE heroes (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nickname VARCHAR(30) NOT NULL,
        tagline VARCHAR(30) NOT NULL,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    $conn->close();
}


class DatabaseConnector {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "heroes_db";
    private $conn;
    private $heroes = []; // array

    // connect to the db
    protected function connect(){
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db);
    }

    protected function disconnect(){
        $this->conn->close();
    }

    function getAllHeroes(){
        // get the heroes, return the heroes
       
        try {
            $this->connect();
            $sql = "SELECT * FROM heroes";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
            // output data of each row
                while($row = $result->fetch_assoc()) {
                    // create an object 
                    // append it to the heroes array
                    $heroObj = new HeroObject($row["nickname"], $row["tagline"]);
                    array_push($this->heroes, $heroObj);
                }
            } else {
                $this->heroes = [];
            }
            $this->disconnect();
        }
        //catch exception
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
        return $this->heroes;
    }

}

class HeroObject {
    public $nickname = "";
    public $tagline = "";

    function __construct($name, $tag) {
        $this->nickname = $name;
        $this->tagline = $tag;
    }

    function get_name() {
        return $this->nickname;
    }
    function get_tagline() {
        return $this->tagline;
    }

}

?>