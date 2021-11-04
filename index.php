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

// IF there is an action, get it anc handle case
if (isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "search":
            echo "READ $text HEROES";
            readAllHeroes();
            break;
        case "create":
            // echo "CREATE A HERO";
            createHero();
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
                  name: <b>" . $row["name"] . "</b><br>
                  about: <i>" . $row["about_me"] . "</i><br>
                  biography: <i>" . $row["biography"] . "</i><br><br>";
        }
    } else {
        echo "0 results";
    }
}

// Create a new hero
function createHero()
{
    global $conn;
    if (!isset($_POST["name"]) || !isset($_POST["about_me"]) || !isset($_POST["biography"]) || !isset($_POST["ability"])) {
        echo "Error: Missing parameters to create hero.";
        return;
    }
    $name = $_POST["name"];
    $aboutme = $_POST["about_me"];
    $biography = $_POST["biography"];
    $ability = $_POST["ability"];
    $hero_id = -1;
    $ability_id = -1;

    $sqlHeroes = "INSERT INTO heroes (name, about_me, biography) VALUES ('$name', '$aboutme', '$biography')";
    $sqlAbilities = "INSERT INTO ability_type (ability) VALUES ('$ability')";

    if ($conn->query($sqlHeroes) === TRUE) {
        $hero_id = $conn->insert_id;
        echo "$hero_id";

        if ($conn->query($sqlAbilities) === TRUE) {
            $ability_id = $conn->insert_id;
            echo "ability_id";
        } else { // Ability_type already exists, so select
            $sqlAbilities = "SELECT id FROM ability_type WHERE ability='$ability';";
            $result = ($conn->query($sqlAbilities))->fetch_assoc();
            $ability_id = $result["id"];
            echo "ability_id";
        }
        $sqlHeroAbility = "INSERT INTO abilities (hero_id, ability_id) VALUES ('$hero_id', '$ability_id');";
        if ($conn->query($sqlHeroAbility) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error with SQL query: <br><br>" . $sqlHeroAbility . "<br><br>" . $conn->error . ".";
        }
    } else {
        echo "Error with SQL query: <br><br>" . $sqlHeroes . "<br><br>" . $conn->error . ".";
    }
}

// Update a hero
function updateHero()
{
    global $conn;
    if (!isset($_POST["name"]) || !isset($_POST["about_me"]) || !isset($_POST["biography"])) {
        echo "Error: Missing parameters to create hero.";
        return;
    }
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

// 
$conn->close();
