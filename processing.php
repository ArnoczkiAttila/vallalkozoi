<?php
    require "db.php";
    if (!isset($_GET["ujFordulo"])) {
        $sql = "SELECT * FROM elnyert_ajanlat INNER JOIN ajanlat ON ajanlat.ajanlat_id = elnyert_ajanlat.ajanlat_id WHERE ajanlat.csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $gameDetails[gameId])";
        $sql2 = "SELECT * FROM vegso_allapot WHERE csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $gameDetails[gameId])";
        $elnyert = $conn->execute_query($sql);
        $vegso = $conn->execute_query($sql2);
        foreach ($elnyert as $row) {
            $bekerult = false;
            if ($vegso->num_rows > 0) {
                foreach ($vegso as $row2) {
                    if ($row["elfogadott_ar"]==$row2["elfogadott_ar"]&&$row["csapat_id"]==$row2["csapat_id"]&&$row["termek_id"]==$row2["termek_id"]) {
                        $sql = "UPDATE vegso_allapot SET elfogadott_mennyiseg = elfogadott_mennyiseg + $row[elfogadott_mennyiseg] WHERE elfogadott_id = $row2[elfogadott_id]";
                        $conn->execute_query($sql);
                        $bekerult = true;
                        break;
                    }   
                }
            }
            if (!$bekerult) {
                $sql = "INSERT INTO vegso_allapot (elfogadott_mennyiseg,elfogadott_ar,csapat_id,termek_id) VALUES (?,?,?,?)";
                $conn->execute_query($sql,[$row["elfogadott_mennyiseg"],$row["elfogadott_ar"],$row["csapat_id"],$row["termek_id"]]);
            }
        }
    } else {
        try {
            $sql = "DELETE FROM ajanlat WHERE csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $gameDetails[gameId]);DELETE FROM vegso_allapot WHERE csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $gameDetails[gameId]);UPDATE game SET forduloSzam = forduloSzam + 1, menetSzam = 1 WHERE gameId = $gameDetails[gameId];DELETE FROM alapanyag_vasar WHERE csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $gameDetails[gameId])";
            $conn->multi_query($sql);
            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    if (isset($_GET["nextRound"])) {
        nextRound(1);
        $sql = "DELETE FROM ajanlat WHERE csapat_id IN (SELECT csapat_id FROM csapat WHERE gameId = $gameDetails[gameId])";
        $conn->execute_query($sql);
    }
    header("Location: $_GET[redirectTo]");

