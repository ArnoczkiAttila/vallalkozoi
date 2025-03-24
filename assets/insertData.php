<?php
    require "../db.php";
    $tableIndex = $_GET["tableIndex"];

    $response = array();
    $response["status"] = false;
    $response["message"] = "A művelet sikertelen volt!";

    $tables = [
        "alapanyag (alapanyag_nev,ar) VALUES (?,?)",
        "csapat (csapat_nev,csapat_letszam,gameId) VALUES (?,?,$gameDetails[gameId])",
        "termek (termek_nev,termek_leiras,termek_normaido,termek_bony,termek_felhivas,termek_ar) VALUES (?,?,?,?,?,?)",
        "game (gameName) VALUES (?)"
    ];

    $tableParamsNums = substr_count($tables[$tableIndex],"?");
    $params = array();

    for ($i=1; $i <= $tableParamsNums; $i++) { 
        array_push($params,$_GET["value$i"]);
    }

    $sql = "INSERT INTO ".$tables[$tableIndex];
    if ($result = $conn->execute_query($sql,$params)) {
        $response["status"] = true;
        $response["message"] = "A művelet sikerült!";
    }

    //minden csapat után kell egy alapanyag_vasar INSERT

    if ($tableIndex == 1 ) {
        $otherId = $conn->insert_id;

        $sql = "SELECT alapanyag_id AS v FROM alapanyag";
        $result = $conn->execute_query($sql);
        foreach ($result as $row) {
            $sql = "INSERT INTO alapanyag_vasar (csapat_id, alapanyag_id) VALUES ($otherId,?)";
            if ($conn->execute_query($sql,[$row["v"]])) {
                $response["status"] = true;
                $response["message"] = "A művelet sikerült!";
            } else {
                $response["status"] = false;
                $response["message"] = "A művelet sikertelen volt!";
                break;
            }
        }
    }

    //minden termék és alapanyag után kell egy INSERT a keszul táblába

    if ($tableIndex==0||$tableIndex==2) {
        $otherId = $conn->insert_id;
        $options = [
            ["SELECT alapanyag_id AS v FROM alapanyag","VALUES ($otherId,?,0)"],
            ["SELECT termek_id AS v FROM termek","VALUES (?,$otherId,0)"]
        ];
        $temp;
        switch ($tableIndex) {
            case 0:
                $temp = 1;
                break;
            case 2:
                $temp = 0;
                break;
            default:
                $temp = -1;
                break;
        }
        $sql = $options[$temp][0];
        if ($result = $conn->execute_query($sql)) {
            foreach ($result as $row) {
                $sql = "INSERT INTO keszul ".$options[$temp][1];
                if ($conn->execute_query($sql,[$row["v"]])) {
                    $response["status"] = true;
                    $response["message"] = "A művelet sikerült!";
                } else {
                    $response["status"] = false;
                    $response["message"] = "A művelet sikertelen volt!";
                    break;
                }
            }
        }


        //minden alapanyag után kell egy alapanyag_vasar INSERT

        if ($tableIndex == 0) {
            $sql = "SELECT csapat_id AS v FROM csapat";
            $result = $conn->execute_query($sql);
            foreach ($result as $row) {
                $sql = "INSERT INTO alapanyag_vasar (csapat_id, alapanyag_id) VALUES (?,$otherId)";
                if ($conn->execute_query($sql,[$row["v"]])) {
                    $response["status"] = true;
                    $response["message"] = "A művelet sikerült!";
                } else {
                    $response["status"] = false;
                    $response["message"] = "A művelet sikertelen volt!";
                    break;
                }
            }
        }
    }
    

    echo json_encode($response);