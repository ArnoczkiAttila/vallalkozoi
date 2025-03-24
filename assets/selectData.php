<?php
    require "../db.php";
    
    $response = array();
    $response["numrows"] = 0;
    $response["status"] = false;
    $response["message"] = "A művelet sikertelen volt!";
    $response["data"] = array();
    $tables = [
        "alapanyag",
        "csapat",
        "termek",
        "keszul"
    ];
    $tableIndex = $_GET["tableIndex"];
    $sql = "SELECT * FROM ".$tables[$tableIndex];
    if (($tableIndex!=0)&&($tableIndex!=2)&&($tableIndex!=3)) {
        $sql .= " WHERE gameId=$gameDetails[gameId]";
    }
    if ($tableIndex == 2) {
        $sql.= " WHERE termek_felhivas > 0 AND termek_ar > 0";
    }
    if ($result = $conn->execute_query($sql)) {
        $response["status"] = true;
        $response["message"] = "A művelet sikeresen végre lett hajtva!";
        foreach ($result as $row) {
            array_push($response["data"],$row);
            $response["numrows"]++;
        }
    }
    echo json_encode($response);