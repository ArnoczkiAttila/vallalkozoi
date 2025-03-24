<?php 
    require "db.php";
    $csapat_letszam;
    if (isset($_POST['ment'])) {

        $data=$_POST['data'];

        foreach ($data as $row) 
        { 
            if ($row['darab']!='' && $row['ar']!='')
            {
                $sql = "UPDATE termek SET termek_felhivas='".$row["darab"]."', termek_ar='".$row["ar"]."' WHERE termek_id='".$row["id"]."'";
                $result = $conn->query($sql); 
            } else {
                $sql = "UPDATE termek SET termek_felhivas=NULL, termek_ar=NULL WHERE termek_id='".$row["id"]."'";
                $result = $conn->query($sql); 
            }
         }
         header("Location: menet?stage2");
    }
    if (isset($_POST['megse'])) {
        header("Location: menet?stage2");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhívás</title>
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
        .roundedBox {
            min-width: 1200px;
        }
        table input[type=number] {
            width: 100px;
            
        }
        tbody tr:hover {
            background-color: white;
            cursor: default;
        }
    </style>
</head>

<body onload='szamit()'>
    <? require "assets/nav.php"?>

    <div class="container">
        <div class="roundedBox">
            <h1>Felhívás összeállítása</h1>
            <hr>
            <br>
            <h3>Résztvevő vállalatok</h3>
            <table style="max-width: 500px;">
                <tr>
                    <th>Név</th>
                    <th>Létszám</th>
                </tr>
                <?php 
                    $sql = "SELECT csapat_nev, csapat_letszam FROM csapat WHERE gameId = $gameDetails[gameId]";
                    $result = $conn->query($sql);
                    $v_szam = 0;
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr><td>". $row["csapat_nev"] ."</td><td>". $row["csapat_letszam"] ."</td></tr>";
                            $v_szam++;
                        }
                    }
                ?>
            </table>
            <h3>Termelési adatok</h3>
            <table style="max-width: 500px;">
                <tr>
                    <th>Vállalatok száma</th>
                    <td id="v_szam"><?php echo $v_szam;?></td>
                </tr>
                <tr>
                    <th>Termelési idő (perc)</th>
                    <td><input type="number" name="" id="" onchange="munkaido_szamit(this.value)"></td>
                </tr>
            </table>
            <br>
            <h3>Termékek</h3>
            <form method="post">
            <table>
                <tr>
                    <th>Név</th>
                    <th>Normaidő (mp)</th>
                    <th>Tervezett darabszám</th>
                    <th>Bonyolultság</th>
                    <th>Nyersár</th>
                    <th>Felhívásár</th>
                </tr>
                <?php 
                    $sql = "SELECT * FROM termek INNER JOIN keszul ON termek.termek_id = keszul.termek_id INNER JOIN alapanyag ON alapanyag.alapanyag_id = keszul.alapanyag_id WHERE db_kijon > 0";
                    $result = $conn->query($sql);
                    $c = 0;
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {    
                            $nyersar_szamit = floor((intval($row["termek_normaido"])+((intval($row["termek_bony"])/100))*intval($row["termek_normaido"])+(intval($row["ar"])/intval($row["db_kijon"]))));                
                            echo "<tr><input type='hidden' value='".$row["termek_id"]."' name='data[$c][id]'><td><b>".$row["termek_nev"]."</b></td><td class='norma'>".$row["termek_normaido"]."</td><td><input type='number' class='darab' oninput='szamit()' name='data[$c][darab]' value='".$row['termek_felhivas']."'></td><td>".$row["termek_bony"]."</td><td><b>".$nyersar_szamit."</b></td><td><input type='number' name='data[$c][ar]' value='".$row['termek_ar']."'></td></tr>";
                            $c++;
                        }
                    }
                ?>
            </table>
            <br>
            <table style="max-width: 500px;">
                <tr>
                    <th>Gyártási idő szükséglet (perc)</th>
                </tr>
                <tr>
                    
                    <td id='gyar_ido'>
                        
                    </td>
                </tr>
            </table>
            <br><br>
            <button name='ment' role="button" class="btn btn-success">Mentés</button>
            <button name='megse' role="button" class="btn btn-danger">Mégse</button>
            </form>
        </div>
        
    </div>
</body>
<script>
    /*
function munkaido_szamit(t_ido) {
    if (t_ido !='') {
        t_ido = parseInt(t_ido);
        let v_szam = parseInt(document.getElementById('v_szam').innerHTML);
        let letszam = parseInt(document.getElementById('letszam').innerHTML);
        document.getElementById("munkaido").innerHTML = t_ido*v_szam*letszam;
    } else {
        document.getElementById("munkaido").innerHTML ='';
    }
}*/
function szamit() {
    let data = document.getElementsByClassName("norma");
    let data2 = document.getElementsByClassName("darab");
    let osszeg = 0;
    for (let i = 0; i < data.length; i++) {
        if (data2[i].value !="") {

            osszeg+=((data2[i].value*1)*(data[i].innerHTML*1));
            console.log(osszeg);
        } 
    }
    document.getElementById("gyar_ido").innerHTML = Math.floor(osszeg/60);
    document.documentElement.clientHeight;
}
</script>
</html>