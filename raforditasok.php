<?php
    require 'db.php';
    $alertTemp = false;
    if (isset($_POST['ment'])) {
        $alertTemp = true;
        $keys=array_keys($_POST);
        $i=-1;
        foreach ($_POST as $key => $value) 
        { 
            $i++;
            if ($keys[$i]!="ment")
            {
                $temp=explode("-",$keys[$i]);
                $alapanyagid=$temp[0];
                $termekid=$temp[1];
                $darabszam=$value;
                $sql = "UPDATE keszul SET db_kijon='$darabszam' WHERE termek_id='$termekid' AND alapanyag_id='$alapanyagid'";
                $result = $conn->query($sql); 
            }
            
        }
    }
	
	
	global $input_szam;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termékek</title>
    <?php require "assets/bootstrap.php"?>
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/alert.css">
    <script src="scripts/alertclass.js"></script>
    <script src="scripts/modifications.js"></script>

    <style>
        button.btn.sbmt {
            height: calc(3.5rem + calc(var(--bs-border-width) * 2));
            min-height: calc(3.5rem + calc(var(--bs-border-width) * 2));
            line-height: 1.25;
            
        }
        input {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <? require "assets/nav.php"?>

    <div class="container">
        <div class="roundedBox">
            <h2>Ráfordítások</h2>
            <hr>
            <form method='post'>
            <table>
                <tr>
                    <th>
                        Alapanyag
                    </th>
                    <?php 
                        $sql = "SELECT * FROM termek";
                        $result = $conn->query($sql);
                        $termek_szam = 0;
                        $termekek = [];
                        if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                                echo "<th>".$row["termek_nev"]."</th>";
                                $termek_szam++;
                                array_push($termekek,$row["termek_id"]);
                            }
                        } 
                    ?>
                </tr>
                <?php

                    $sql = "SELECT alapanyag.alapanyag_id, alapanyag_nev, termek.termek_id, db_kijon FROM alapanyag, keszul, termek WHERE alapanyag.alapanyag_id=keszul.alapanyag_id AND termek.termek_id=keszul.termek_id ORDER BY alapanyag.alapanyag_id, termek.termek_id";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $nev = $row['alapanyag_id']."-".$row['termek_id'];
                            echo "<tr id='sor_". $row["alapanyag_id"]."'><td>" . $row["alapanyag_nev"]. "</td> <td><input type='number' value='".$row['db_kijon']."' min=0 name='".$nev."'></td>";
                            for ($i = 1; $i < $termek_szam; $i++)
                            {
                                $row = $result->fetch_assoc();
                                $nev = $row['alapanyag_id']."-".$row['termek_id'];
                                echo "<td><input type='number' value='".$row['db_kijon']."' min=0 name='".$nev."'></td>";
                            }
                            echo "</tr>";
                        }
                    }
                    $conn->close();
                ?>
            </table>
            
            <button class="btn btn-secondary sbmt" name="ment">Mentés</button>
            </form>
            <span><a href="index"><button class="btn button-default">Mégsem</button></a></span>        
                    
        </div>
    </div>   

    <!-- Alert -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert" id="cosAlert" style="display: none;">
        <strong id="alertMessage"></strong>
        <button type="button" class="btn-close" onclick="this.parentNode.style.display='none'"></button>
        <div class="bar"></div>
    </div>
<script>
    
    if (<?php echo $alertTemp?>) {
        new Alert("A mentés sikerült!","success");
        if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    }
</script>
</html>