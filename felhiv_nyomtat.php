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
    </style>
</head>

<body>
    <?php 
        if($gameDetails["menetSzam"]>1){echo "<h3 style='text-align:center;'>Pótfelhívás</h3>";}
        else {echo '<h3 style="text-align: center;">Felhívás</h3>';}
    ?>
    
    <p><b>Forduló: <?php echo $gameDetails["forduloSzam"]?></b></p>
    <p><b>Menet: <?php echo $gameDetails["menetSzam"]?></b></p>
    <h3>Termék leírások</h3>
    <?php 
        $sql = "SELECT termek_leiras, termek_nev, termek_ar FROM termek WHERE termek_felhivas > 0 AND termek_ar > 0";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if ($row["termek_ar"] != NULL) {
                    echo "<h4>".$row["termek_nev"]."</h4>";
                    echo "<p>".$row["termek_leiras"]."</p>";
                }
                
            }
        }
    ?>
    <hr>
    <h3 style="text-align: center;">Ajánlat tétel</h3>
    <p><b>Cégnév:</b><br><input type="text" name="" id="" style="width: 50%; border: 3px solid black; height: 30px;"></p>
    <p><b>Ajánlat</b><br>
        <table>
            <tr>
                <th>Termék</th>
                <th>Szükséges Mennyiség</th>
                <th>Maximális ár</th>
                <th>Ajánlott mennyiség</th>
                <th>Ajánlott ár</th>
            </tr>
            <?php 
                $sql = "SELECT * FROM termek WHERE termek_felhivas > 0 AND termek_ar > 0";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        if ($row["termek_ar"] != NULL) {
                            echo "<tr><td>".$row["termek_nev"]."</td><td class='l'>".$row["termek_felhivas"]."</td><td class='l'>".$row["termek_ar"]."</td><td class='vastag'></td><td class='vastag'></td></tr>";
                        }
                       
                    }
                }
            ?>
        </table>
    </p>
</body>
<script>
</script>
</html>