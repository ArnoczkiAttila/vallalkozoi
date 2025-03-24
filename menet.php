
<?php require "db.php"?>
<?php 
    if (!isset($_SESSION["gameId"])) {
        header("Location: index");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><? echo $gameDetails["forduloSzam"]?>. Forduló <? echo $gameDetails["menetSzam"]?>. Menet</title>
    <?php require "assets/bootstrap.php"?>
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/alert.css">
    <script src="scripts/alertclass.js"></script>
    <script src="scripts/modifications.js"></script>
    <script src="scripts/nyomtat.js"></script>
    <script src="print.min.js"></script>
    <link rel="stylesheet" href="print.min.css">
    <style>
        button.btn.sbmt {
            height: calc(3.5rem + calc(var(--bs-border-width) * 2));
            min-height: calc(3.5rem + calc(var(--bs-border-width) * 2));
            line-height: 1.25;
            
        }
        <?php
        if (isset($_GET["stage3"])) { ?>
            tr:hover {
                cursor: default !important;
            }
        <?php }
        ?>
    </style>
    
</head>
<body>
    <? require "assets/nav.php"?>
    <div class="container">
        <div class="roundedBox">
            <h3><? echo $gameDetails["forduloSzam"]?>. Forduló <? echo $gameDetails["menetSzam"]?>. Menet</h3>
            <hr>
            <br>
 
            <!-- stage1 -->
            
            <?php if (isset($_GET["stage1"])) {
                updateGame(1);
                ?>
                
                <!-- Vállalatok rész -->
                <h2>Vállalatok</h2>
                <hr>
                <table>
                    <thead>
                        <tr>
                            <th>Vállalat név</th>
                            <th>Létszám</th>
                        </tr>
                    </thead>
                    <tbody id="tb">
                    </tbody>
                </table>
                <br>
                <button class="btn btn-secondary " onclick="window.location.href = 'csapat'">Vállalatok módosítása / hozzáadása</button>
                <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage2'">Tovább</button>
            <?php }?>


            <!-- stage2 -->


            <?php if (isset($_GET["stage2"])) {
                                updateGame(2);
                                ?>
                <!-- Felhívás -->
                <? if ($gameDetails["menetSzam"]>1) {
                    header("Location: menet?stage10&set");
                } ?>
                <h2>Felhívás elkészítése</h2>
                <hr>
                <table>
                    <thead>
                        <tr>
                            <th>Termék</th>
                            <th>Mennyiség</th>
                            <th>Ár</th>
                        </tr>
                    </thead>
                    <tbody id="tb">
                    </tbody>
                </table>
                <br>

                <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage1'">Vissza</button>
                <button class="btn btn-secondary " onclick="window.location.href = 'felhivas'">Felhívás szerkesztése</button>
                <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage3'">Tovább</button>
                <button class="btn btn-info" style="float: right;" onclick="nyomtat('felhiv_nyomtat.php')">Nyomtatás</button>
                
            <?php }?>

            
            <!-- Stage 3 -->


            <?php
                if (isset($_GET["stage3"])) {
                    updateGame(3);
            ?>      
                <?php require "ajanlatok_benyujtasa.php"?> 
            <?php 
                }
            ?>


            <!-- Stage 4 -->


            <?php
                if (isset($_GET["stage4"])) {
                    updateGame(4);
            ?>      
                <?php require "beadott_ajanlatok.php" ?>
            <?php 
                }
            ?>


            <!-- Stage 5 -->


            <?php
                if (isset($_GET["stage5"])) {
                    updateGame(5);

            ?>      
                 <?php require "teljesitesek.php" ?> 
            <?php 
                }
            ?>


            <!-- Stage 6 -->


            <?php
                if (isset($_GET["stage6"])) {
                    updateGame(6);

            ?>      
                 <?php require "alapanyag_vasar.php" ?> 
            <?php 
                }
            ?>


            <!-- Stage 7 -->


            <?php
                if (isset($_GET["stage7"])) {
                    updateGame(7);

                    include_once "jovedelem_nyomtat.php";
                    ?>
                        <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage6'">Vissza</button>
                        <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage8'">Összesítés</button>
                        <button class="btn btn-info" style="float: right;" onclick="nyomtat('jovedelem_nyomtat.php?inc')">Nyomtatás</button>
                    <?
                }
            ?>


            <!-- Stage 8 -->


            <?php
                if (isset($_GET["stage8"])) {
                    updateGame(8);
                    ?>
                        <h2>Összesítés</h2>
                        <hr>
                        <h4>Eredmény</h4>
                        <br>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nr.</th>
                                    <th>Vállalat</th>
                                    <th>Bevétel</th>
                                    <th>Kiadás</th>
                                    <th>Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    $sql = "SELECT * FROM `eredmenyek` INNER JOIN csapat ON eredmenyek.csapat_id = csapat.csapat_id WHERE eredmenyek.gameId = ? AND eredmenyek.fordulo = $gameDetails[forduloSzam] ORDER BY arbevetel-raforditasok-kotber DESC";
                                    if ($result = $conn->execute_query($sql,[$gameDetails["gameId"]])) {
                                        $c = 0;
                                        foreach ($result as $row) {
                                            $c++;
                                            ?>
                                                <tr>
                                                    <td><? echo $c?></td>
                                                    <td><? echo $row["csapat_nev"]?></td>
                                                    <td><? echo $row["arbevetel"]?></td>
                                                    <td><? echo $row["raforditasok"]+$row["kotber"]?></td>
                                                    <td><? echo $row["arbevetel"]-$row["raforditasok"]-$row["kotber"]?></td>
                                                </tr>
                                            <?
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage7'">Vissza</button>
                        <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage9'">Fordulók összegzése</button>

                        <button class="btn btn-secondary " onclick="window.location.href = 'processing?redirectTo=menet?stage2&ujFordulo'">Új forduló létrehozása</button>
                        <button class="btn btn-info" style="float: right;" onclick="nyomtat('jovedelem_nyomtat.php?inc')">Nyomtatás</button>
                    <?
                }
            ?>


            <!-- Stage 9 -->


            <?php
                if (isset($_GET["stage9"])) {
                    updateGame(9);
                    require "osszesitett_nyomtat.php";
                    ?>
                        <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage8'">Vissza</button>
                        <button class="btn btn-info" style="float: right;" onclick="nyomtat('osszesitett_nyomtat.php?inc')">Nyomtatás</button>
                    <?
                }
            ?>


            <!-- Stage 10 -->

            <?php if (isset($_GET["stage10"])) {
                updateGame(10);
                if (!isset($_GET["set"])) {
                    $sql = "UPDATE termek SET termek_felhivas = 0, termek_ar =0";
                    $conn->execute_query($sql);
                   
                }
                ?>
                <!-- Felhívás -->
                <h2>Pótfelhívás létrehozása</h2>
                <hr>
                <table>
                    <thead>
                        <tr>
                            <th>Termék</th>
                            <th>Mennyiség</th>
                            <th>Ár</th>
                        </tr>
                    </thead>
                    <tbody id="tb">
                    </tbody>
                </table>
                <br>

                <button class="btn btn-secondary " onclick="window.location.href = 'felhivas'">Pótfelhívás szerkesztése</button>
                <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage3'">Tovább</button>
                <button class="btn btn-info" style="float: right;" onclick="nyomtat('felhiv_nyomtat.php')">Nyomtatás</button>
                
            <?php }?>
            

        </div>
    </div>


    <!-- Alert -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert" id="cosAlert" style="display: none;">
        <strong id="alertMessage"></strong>
        <button type="button" class="btn-close" onclick="this.parentNode.style.display='none'"></button>
        <div class="bar"></div>
    </div>
    
</body>
<script>
    let openTeam = 0;
    <?php
        if (isset($_GET["stage1"])) {
    ?>

    
    function refreshData() {
            fetchData(1).then(r=>{
                if (r.status) {
                    document.getElementById("tb").innerHTML ="";
                    if (r.numrows>0) {
                        r.data.forEach(i => {  
                            document.getElementById("tb").innerHTML += `<tr data-bs-toggle="modal" data-bs-target="#exampleModal"><td>${i["csapat_nev"]}</td><td>${i.csapat_letszam}</td></tr>`;
                        });
                    } else {
                        document.getElementById("tb").innerHTML += `<tr><td colspan="2">Nincs egy vállalat se!</td></tr>`;
                    }
                } else {
                    new Alert("Szerver hiba!","warning");
                }
            });
        } 
        refreshData();


    <?php 
        }
    ?>
    
    <?php
        if (isset($_GET["stage2"])||isset($_GET["stage10"])) {
    ?>


    function refreshData() {
            fetchData(2).then(r=>{
                if (r.status) {
                    document.getElementById("tb").innerHTML ="";
                    if (r.numrows>0) {
                        r.data.forEach(i => {  
                            document.getElementById("tb").innerHTML += `<tr data-bs-toggle="modal" data-bs-target="#exampleModal"><td>${i.termek_nev}</td><td>${i.termek_felhivas}</td><td>${i.termek_ar}</td></tr>`;
                        });
                    } else {
                        document.getElementById("tb").innerHTML += `<tr><td colspan="3">Nincs még felhívás létrehozva!</td></tr>`;
                    }
                } else {
                    new Alert("Szerver hiba!","warning");
                }
            });
        } 
        refreshData();


    <?php 
        }
    ?>

    
</script>
</html>