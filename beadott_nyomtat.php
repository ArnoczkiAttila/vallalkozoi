<?php require "db.php";?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table {
            border-collapse: collapse;
            
        }
        td,th {
            border:1px solid black;
            padding: 10px;
        }
        
        .vastag {
            border: 3px solid black;
        }
        @media print {
            .pagebreak { page-break-before: always; } /* page-break-after works, as well */
        }   
    </style>
</head>
<body>
    <?php
        $sql = "SELECT COUNT(csapat_id) AS C FROM csapat WHERE gameId = $gameDetails[gameId]";
        $result = $conn->query($sql);
        $temp = $result->fetch_assoc();
        $csapat_szam = $temp["C"];

        //$csapat_szam = $result["c"];
        
        $futasi_szam = 0;
        $sql = "SELECT csapat_nev FROM csapat WHERE gameId = $gameDetails[gameId]";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $futasi_szam++;
                kiir($row["csapat_nev"]);
            }
        }
        function kiir($csapat) {
            global $futasi_szam;
            global $csapat_szam;
            global $conn;
            global $gameDetails;
            $sql = "";
            if (isset($_GET["osszes"])) {
                $sql = "SELECT * FROM ((vegso_allapot INNER JOIN termek ON termek.termek_id = vegso_allapot.termek_id) INNER JOIN csapat ON csapat.csapat_id = vegso_allapot.csapat_id) WHERE csapat.csapat_nev = '$csapat' AND csapat.gameId = $gameDetails[gameId]";
            } else {
                $sql = "SELECT * FROM (((ajanlat INNER JOIN csapat ON csapat.csapat_id = ajanlat.csapat_id)INNER JOIN termek ON termek.termek_id = ajanlat.termek_id)INNER JOIN elnyert_ajanlat ON elnyert_ajanlat.ajanlat_id = ajanlat.ajanlat_id) WHERE csapat.csapat_nev = '$csapat' AND csapat.gameId = $gameDetails[gameId]";
            }
            $result = $conn->query($sql);
            
            if (isset($_GET["osszes"])) {
                echo "<h3 style='text-align: center;'>Elnyert megrendelés</h3>";
            } else {
                echo "<h3 style='text-align: center;'>Elnyert mennyiség</h3>";
            }
            echo "<h3 style='text-align: center;'>".$csapat."</h3>";
            echo "<p><b>Forduló: $gameDetails[forduloSzam]</b></p>";
            echo "<p><b>Menet: $gameDetails[menetSzam]</b></p>";
            
            if ($result->num_rows > 0) {
                echo "<table>";
                if (isset($_GET["osszes"])) {
                    echo "<tr><th>Termék</th><th>Ajánlott ár</th><th>Elfogadott mennyiség</th></tr>";
                } else {
                    echo "<tr><th>Termék</th><th>Ajánlott mennyiség</th><th>Ajánlott ár</th><th>Elfogadott mennyiség</th></tr>";
                }  
                while($row = $result->fetch_assoc()) {   

                    if (isset($_GET["osszes"])) {
                        echo "<tr><td>".$row["termek_nev"]."</td><td>".$row["elfogadott_ar"]."</td><td>".$row["elfogadott_mennyiseg"]."</td></tr>";
                    } else {
                        echo "<tr><td>".$row["termek_nev"]."</td><td>".$row["mennyiseg"]."</td><td>".$row["ar"]."</td><td>".$row["elfogadott_mennyiseg"]."</td></tr>";
                    }      
                }
                echo "</table>";
            } else {
                echo "<h3>Nem nyert el semmit!</h3>";
            }
            if ($futasi_szam!=$csapat_szam) {
                echo "<div class='pagebreak'> </div>";
            }
        }
        
    ?>
</body>
</html>