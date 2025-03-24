<?php
    require "../db.php";
    $tableIndex = $_GET["tableIndex"];

    $response = array();
    $response["status"] = false;
    $response["message"] = "A művelet sikertelen volt!";

    $tables = [
        "alapanyag SET alapanyag_nev = ?, ar = ? WHERE alapanyag_id = ?",
        "csapat SET csapat_nev = ?, csapat_letszam = ? WHERE csapat_id = ?",
        "termek SET termek_nev = ?, termek_leiras = ?, termek_normaido = ?, termek_bony = ?, termek_felhivas = ?, termek_ar = ? WHERE termek_id = ?"
    ];

    $tableParamsNums = substr_count($tables[$tableIndex],"?");
    $params = array();

    for ($i=1; $i <= $tableParamsNums; $i++) { 
        array_push($params,$_GET["value$i"]);
    }

    $sql = "UPDATE ".$tables[$tableIndex];
    if ($result = $conn->execute_query($sql,$params)) {
        $response["status"] = true;
        $response["message"] = "A művelet sikerült!";
    }
    echo json_encode($response);