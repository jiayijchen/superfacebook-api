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

// Read all heroes
function readAllHeroes()
{
    global $conn;
    $sql = "SELECT * FROM heroes";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // output data of each row
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
            // echo "id: " . $row["id"] . "<br>
            //       name: <b>" . $row["name"] . "</b><br>
            //       about: <i>" . $row["about_me"] . "</i><br>
            //       biography: <i>" . $row["biography"] . "</i><br><br>";
            //print json_encode($row);
        }
        toJson($rows);
        // echo json_encode($rows);
        
    } else {
        echo "0 results";
    }
}

// Create a new hero
function createHero()
{
    global $conn;

    // Check for correct parameters to create
    if (!isset($_POST["name"]) || !isset($_POST["about_me"]) || !isset($_POST["biography"]) || !isset($_POST["ability"])) {
        echo "Error: Missing parameter(s) to create hero.";
        return;
    }

    // Set values
    $name = $_POST["name"];
    $aboutme = $_POST["about_me"];
    $biography = $_POST["biography"];
    $ability = $_POST["ability"];

    // Set initial SQL query
    $sql = "INSERT INTO heroes (name, about_me, biography) VALUES ('$name', '$aboutme', '$biography')";

    // Test query, if succesful, pull created id
    if ($conn->query($sql) === TRUE) {
        $hero_id = $conn->insert_id;
        $ability_id = -1;
        $sql = "SELECT id FROM ability_type WHERE ability='$ability';";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $ability_id = $row["id"];
        } else { 
            // ability_type doesn't exist, so insert
            $sql = "INSERT INTO ability_type (ability) VALUES ('$ability')";
            if ($conn->query($sql) === TRUE) {
                $ability_id = $conn->insert_id;
            } else { 
                // error: pre-existing ability (should never happen with out if-statement)
                echoError($sql, $conn->error);
            }
        }

        $sql = "INSERT INTO abilities (hero_id, ability_id) VALUES ('$hero_id', '$ability_id');";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else { 
            // error: pre-existing hero-ability-relationship (should never happen)
            echoError($sql, $conn->error);
        }
    } else { 
        // error: duplicate entry
        echoError($sql, $conn->error);
    }
}

// Search a hero
function searchHero()
{
    global $conn;
    if (!isset($_GET["name"])) {
        echo "Error: Missing name parameter to search for a hero.";
    }
    $name = $_GET["name"];

    $sql = "SELECT heroes.name, heroes.about_me, heroes.biography, GROUP_CONCAT(ability_type.ability) AS abilities
            FROM abilities
            INNER JOIN heroes ON heroes.id=abilities.hero_id
            LEFT JOIN ability_type ON ability_type.id=abilities.ability_id
            WHERE heroes.name='$name'
            GROUP BY heroes.id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        toJson($rows);
    } else {
        echo "<h3> 0 search results </h3>";
    }
}

// Update a hero
function updateHero()
{
    global $conn;

    if (!isset($_POST["name"])) {
        echo "Error: Missing name parameter to update a hero.";
        return;
    } 

    if (!isset($_POST["about_me"]) && !isset($_POST["biography"]) && !isset($_POST["ability"])) {
        echo "Error: Missing parameter(s) to update hero.";
    }
    
    
    $sql = "SELECT * FROM heroes WHERE";
}

function toJson($jsonstring) {
    echo "<pre style='word-wrap: break-word; white-space: pre-wrap;'>" . json_encode($jsonstring) . "</pre>";
}

function echoError($sql, $error) {
    echo "Error with SQL query: <br><br>" . $sql . "<br><br>" . $error . ".";
}
// function updateHero($id, $name, $tagline)
// {

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

if (isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "create":
            createHero();
            break;
        case "search":
            searchHero();
            break;
        case "readall":
            readAllHeroes();
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
            echo "Error 420: There is no action.";
    }
} else {
    return;
}

// 
$conn->close();
