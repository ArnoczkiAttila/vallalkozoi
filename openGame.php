<?
    require "db.php";
    if (isset($_GET["gameId"])) {
        $sql = "SELECT * FROM game WHERE gameId = ?";
        if ($result = $conn->execute_query($sql,[$_GET["gameId"]])) {
            if ($result->num_rows == 1) {
                $result = $result->fetch_assoc();
                session_start();
                $_SESSION["gameId"] = $_GET["gameId"];
                header("Location: menet?stage$result[stageNumber]");
            } else {
                header("Location: index");
            }
        }else {
            header("Location: index");
        }
    } else {
        header("Location: index");
    }
    