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

// Create a new hero
function createHero()
{
    global $conn;

    // Check for correct parameters to create
    if (!isset($_POST["name"]) || !isset($_POST["about_me"]) || !isset($_POST["biography"]) || !isset($_POST["ability"])) {
        echo "<h3>Error 422: Missing parameter(s) to create hero.</h3>";
        return;
    }

    // Set values
    $name = $_POST["name"];
    $about_me = $_POST["about_me"];
    $biography = $_POST["biography"];
    $ability = $_POST["ability"];

    // Set initial SQL query
    $sql = "INSERT INTO heroes (name, about_me, biography) VALUES ('$name', '$about_me', '$biography')";

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
            echo "<h3>Success: New record created successfully.</h3>";
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
        echo "<h3>Error 422: Missing name parameter to search for a hero.</h3>";
        return;
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
        echo "<h3>0 search results </h3>";
    }
}

// Update a hero
function updateHero()
{
    global $conn;

    if (!isset($_GET["name"])) {
        echo "<h3>Error 422: Missing name parameter to update a hero.</h3>";
        return;
    }
    $name = $_GET["name"];

    if (!isset($_POST["about_me"]) && !isset($_POST["biography"]) && !isset($_POST["ability"])) {
        echo "<h3>Error 422: Missing parameter(s) to update hero.</h3>";
        return;
    }

    $updateValues = array();
    if (isset($_POST["about_me"])) {
        $about_me = $_POST["about_me"];
        $updateValues[] = "about_me='$about_me'";
    }
    if (isset($_POST["biography"])) {
        $biography = $_POST["biography"];
        $updateValues[] = "biography='$biography'";
    }

    // if (isset($_POST["ability"])) {
    //     $ability = $_POST["ability"];
    // }

    $sql = "UPDATE heroes SET " . implode(', ', $updateValues) . "WHERE name='$name'";
    if ($conn->query($sql) === TRUE) {
        echo "<h3>Success: Record updated successfully.</h3>";
    } else {
        echoError($sql, $conn->error);
    }
}

//
function deleteHero()
{
    global $conn;

    if (!isset($_GET["name"])) {
        echo "<h3>Error 422: Missing name parameter to delete a hero.</h3>";
        return;
    }
    $name = $_GET["name"];

    $sql = "DELETE FROM heroes WHERE name='$name'";
    if ($conn->query($sql) === TRUE) {
        echo "<h3>Success: Record deleted successfully.</h3>";
    } else {
        echoError($sql, $conn->error);
    }
}

// Read all heroes
function readAllHeroes()
{
    global $conn;

    $sql = "SELECT heroes.id, heroes.name, heroes.about_me, heroes.biography, GROUP_CONCAT(ability_type.ability) AS abilities
            FROM abilities
            INNER JOIN heroes ON heroes.id=abilities.hero_id
            LEFT JOIN ability_type ON ability_type.id=abilities.ability_id
            GROUP BY heroes.id";
    // $sql = "SELECT heroes.name, 
    //                heroes.about_me, 
    //                heroes.biography, 
    //                GROUP_CONCAT(ability_type.ability) AS abilities, 
    //                GROUP_CONCAT(relationship_types.type) AS relationships
    //             FROM abilities
    //             INNER JOIN heroes ON heroes.id=abilities.hero_id
    //             LEFT JOIN ability_type ON ability_type.id=abilities.ability_id
    //             INNER JOIN relationships ON heroes.id=relationships.hero1_id
    //             LEFT JOIN relationship_types ON relationship_types.id=relationships.type_id
    //             GROUP BY heroes.id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
            echo "id: " . $row["id"] . "<br>
                  name: <b>" . $row["name"] . "</b><br>
                  about: <i>" . $row["about_me"] . "</i><br>
                  biography: <i>" . $row["biography"] . "</i><br>
                  abilities: <i>" . $row["abilities"] . "</i><br><br>";
        }
        //toJson($rows);

    } else {
        echo "<h3>There are no heroes.</h3>";
    }
}

function empowerHero()
{
    global $conn;

    // Check for correct parameters to create
    if (!isset($_GET["name"])) {
        echo "<h3>Error 422: Missing name parameter to empower a hero.</h3>";
        return;
    }
    $name = $_GET["name"];

    if (!isset($_POST["ability"])) {
        echo "<h3>Error 422: Missing ability parameter to empower hero with.</h3>";
        return;
    }

    // Set values
    $name = $_GET["name"];
    $ability = $_POST["ability"];
    $hero_id = -1;
    $ability_id = -1;

    $sql = "SELECT id FROM heroes WHERE name='$name';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hero_id = $row["id"];
    } else {
        echoError($sql, $conn->error);
        return;
    }

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

    // Test query, if succesful, pull created id
    $sql = "INSERT INTO abilities (hero_id, ability_id) VALUES ('$hero_id', '$ability_id');";
    if ($conn->query($sql) === TRUE) {
        echo "<h3>Success: Hero empowered. </h3>";
    } else {
        // error: pre-existing hero-ability-relationship (should never happen)
        echoError($sql, $conn->error);
    }
}

// Convert output to parsable JSON
function toJson($jsonstring)
{
    echo "<pre style='word-wrap: break-word; white-space: pre-wrap;'>" . json_encode($jsonstring) . "</pre>";
}

// Format error
function echoError($sql, $error)
{
    echo "<h3>Error 422: <br><br>" . $sql . "<br><br>" . $error . ".</h3>";
}

if (isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "create":
            createHero();
            break;
        case "search":
            searchHero();
            break;
        case "update":
            updateHero();
            break;
        case "delete":
            deleteHero();
            break;
        case "readall":
            readAllHeroes();
            break;
        case "empower":
            empowerHero();
            break;
        default:
            echo "<h3>Error 404: Invalid action.</h3>";
    }
} else {
    echo "<h3>Error 404: There is no action.</h3>";
    return;
}

// Close Connection
$conn->close();
