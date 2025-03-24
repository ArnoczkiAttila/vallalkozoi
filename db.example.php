<?php
    $hostname = "";
    $username = "";
    $password = "";
    $database = "";
    $conn = new mysqli($hostname,$username,$password,$database);
    $gameDetails = array();
    session_start();
    if (isset($_SESSION["gameId"])) {
        $sql = "SELECT * FROM game WHERE gameId = ?";
        if ($result = $conn->execute_query($sql,[$_SESSION["gameId"]])) {
            if ($result->num_rows == 1) {
                $gameDetails = $result->fetch_assoc();
            } 
        }
    }
    function updateGame($param1) {
        global $conn;
        global $gameDetails;
        try {
            $sql = "UPDATE game SET stageNumber = ? WHERE gameId = ?";
            $conn->execute_query($sql,[$param1,$gameDetails["gameId"]]);
        } catch (\Throwable $th) {}
    }
    function nextRound($param1) {
        global $conn;
        global $gameDetails;
        try {
            $sql = "UPDATE game SET menetszam = menetszam + $param1 WHERE gameId = ?";
            $conn->execute_query($sql,[$gameDetails["gameId"]]);
        } catch (\Throwable $th) {}
    } 