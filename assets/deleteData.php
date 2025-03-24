<?php
    require "../db.php";
    $tableIndex = $_GET["tableIndex"];

    $response = array();
    $response["status"] = false;
    $response["message"] = "A művelet sikertelen volt!";

    $tables = [
        "alapanyag WHERE alapanyag_id = ?",
        "csapat WHERE csapat_id = ?",
        "termek WHERE termek_id = ?",
        "game WHERE gameId = ?"
    ];

    $tableParamsNums = substr_count($tables[$tableIndex],"?");
    $params = array();

    for ($i=1; $i <= $tableParamsNums; $i++) { 
        array_push($params,$_GET["value$i"]);
    }
    if ($tableIndex == 3) {
        $sql = "DELETE FROM csapat WHERE gameId = $params[0];DELETE FROM eredmenyek WHERE gameId = $params[0];DELETE FROM vegso_allapot WHERE csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $params[0]);";
        $conn->multi_query($sql);
    }
    $sql = "DELETE FROM ".$tables[$tableIndex];
    if ($result = $conn->execute_query($sql,$params)) {
        $response["status"] = true;
        $response["message"] = "A művelet sikerült!";
    }
    echo json_encode($response);