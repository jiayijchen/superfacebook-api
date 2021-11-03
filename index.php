<?php
// header("Access-Control-Allow-Origin: *");

// DB server credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "superfacebook_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get action
$action = $_GET["action"];

// IF action, handle case
if ($action != "") {
    switch ($action) {
        case "readall":
            // echo "<h1>READ ALL HEROES</h1>";
            readAllHeroes();
            break;
        case "create":
            echo "Create a hero";
            // createHero($_GET["name"], $_GET["tagline"]);
            break;
        case "update":
            echo "Update hero";
            // updateHero($_GET["id"], $_GET["name"], $_GET["tagline"]);
            break;
        case "delete":
            echo "delete hero";
            // deleteHero($_GET["id"]);
            break;
        default:
    }
}

// Read all heroes
function readAllHeroes()
{
    global $conn;
    $sql = "SELECT * FROM heroes";
    $result = $conn->query($sql);
    $rows = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
            echo "id: " . $row["id"] . "<br>
                  name: <b>" . $row["NAME"] . "</b><br>
                  about: <i>" . $row["about_me"] . "</i><br>
                  biography: <i>" . $row["biography"] . "</i><br><br>";
        }
    } else {
        echo "0 results";
    }
    echo json_encode($rows);
}

// Create a new hero
function createHero($name, $tagline) {
    global $conn;
    $sql = "INSERT INTO heroes (nickname, tagline)
            VALUES ('$name', '$tagline')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// read
// function readAllHeroes()
// {
//     // output heroes from the array

//     $heroes = $dbConn->getAllHeroes();

//     //echo "<pre>" . print_r($heroes, 1) . "</pre>";

//     $json = json_encode($heroes);
//     echo $json;
// }

// function updateHero($id, $name, $tagline)
// {
//     //
//     //array_splice($_SESSION["heroes"],$index,1,[[$name, $tagline]]);
//     $servername = "localhost";
//     $username = "root";
//     $password = "";
//     $dbname = "heroes_db";

//     // Create connection
//     $conn = new mysqli($servername, $username, $password, $dbname);
//     // Check connection
//     if ($conn->connect_error) {
//         die("Connection failed: " . $conn->connect_error);
//     }

//     $sql = "UPDATE heroes SET tagline='$tagline', nickname='$name' WHERE id=$id";

//     if ($conn->query($sql) === TRUE) {
//         echo "Record updated successfully";
//     } else {
//         echo "Error updating record: " . $conn->error;
//     }
//     $conn->close();
// }

// function deleteHero($id)
// {
//     $servername = "localhost";
//     $username = "root";
//     $password = "";
//     $dbname = "heroes_db";

//     // Create connection
//     $conn = new mysqli($servername, $username, $password, $dbname);
//     // Check connection
//     if ($conn->connect_error) {
//         die("Connection failed: " . $conn->connect_error);
//     }

//     // sql to delete a record
//     $sql = "DELETE FROM heroes WHERE id=$id";

//     if ($conn->query($sql) === TRUE) {
//         echo "Record deleted successfully";
//     } else {
//         echo "Error deleting record: " . $conn->error;
//     }
// }


// class HeroObject {
//     public $nickname = "";
//     public $tagline = "";

//     function __construct($name, $tag) {
//         $this->nickname = $name;
//         $this->tagline = $tag;
//     }

//     function get_name() {
//         return $this->nickname;
//     }
//     function get_tagline() {
//         return $this->tagline;
//     }

// }

// 
$conn->close();
