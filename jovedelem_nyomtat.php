<?php if (isset($_GET["inc"])) require "db.php"?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @media print {
            .pagebreak { page-break-before: always; } /* page-break-after works, as well */
        }   
        table {
            border-collapse: collapse;
            
        }
        th {
            color: black;
            background-color: #d9d7d7;
        }
        td,th {
            border:1px solid black;
            padding: 5px;
        }
        * {
            -webkit-print-color-adjust: exact !important;   /* Chrome, Safari 6 – 15.3, Edge */
            color-adjust: exact !important;                 /* Firefox 48 – 96 */
            print-color-adjust: exact !important;           /* Firefox 97+, Safari 15.4+ */
        }
    </style>
</head>
<body>
    <?php 
        $sql = "SELECT csapat_id,csapat_nev FROM csapat WHERE csapat.gameId = $gameDetails[gameId]";
        if ($result = $conn->execute_query($sql)) {
            $csapat_szam = count($result->fetch_all());
            $futasi_szam = 0;
            foreach ($result as $csapat) {
                $futasi_szam++;
                $jovedelem = array();
                $hasKotber = false;
                $bevetelQuery;
                $sql = "SELECT termek_nev,elso,elfogadott_ar AS elsoAR,masodik,elfogadott_ar*0.7 AS masodikAR,(elfogadott_ar*elso) AS elsoSUM,(elfogadott_ar*0.7*masodik) AS masodikSUM, (elfogadott_mennyiseg-elso-masodik)*elfogadott_ar*0.3 AS kotber, elfogadott_mennyiseg, elfogadott_mennyiseg-elso-masodik AS isThereKotber FROM vegso_allapot INNER JOIN termek ON termek.termek_id = vegso_allapot.termek_id WHERE vegso_allapot.csapat_id = ?";
                $osszeg = 0;
                ?>
                <div style="text-align: center;">
                    <h3>Jövedelem kimutatása</h3>
                    <h3><? echo $csapat["csapat_nev"]?></h3>
                    <h3>Árvevétel</h3>
                </div>
                <?
                    if (isset($_GET["inc"])) {
                        ?>
                            <p><b>Forduló száma: <? echo $gameDetails["forduloSzam"]?>, Menet száma: <? echo $gameDetails["menetSzam"]?></b></p>
                        <?
                    }
                ?>
                <?
                    if ($result2 = $conn->execute_query($sql,[$csapat["csapat_id"]])) {
                        if ($result2->num_rows>0) {?>
                            <p><b>Árbevétel</b></p>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Termék</th>
                                        <th>Minőség</th>
                                        <th>Mennyiség</th>
                                        <th>Egységár</th>
                                        <th>Bevétel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $bevetelQuery = $result2;
                                        foreach ($result2 as $row) {
                                            $osszeg+=round($row["elsoSUM"])+round($row["masodikSUM"]);
                                            if ($row["kotber"]>0) $hasKotber = true;
                                            ?>
                                            <? if ($row["elso"]>0) {?>
                                                <tr><td><? echo $row["termek_nev"]?></td><td>I.o</td><td><? echo $row["elso"]?></td><td><? echo $row["elsoAR"]?></td><td><? echo $row["elsoSUM"]?></td></tr>
                                            <? }?>
                                            <? if ($row["masodik"]>0) {?>
                                                <tr><td><? echo $row["termek_nev"]?></td><td>II. o</td><td><? echo $row["masodik"]?></td><td><? echo $row["masodikAR"]?></td><td><? echo round($row["masodikSUM"])?></td></tr>
                                            <? }?>
                                            <?php
                                        }
                                    ?>  
                        <tr>
                            <th colspan="4" style="text-align: center;">Összesen</th>
                            <th style="text-align: left;"><? echo $osszeg?></th>
                        </tr>
                    </tbody>
                </table>
                <? }}
                array_push($jovedelem,$osszeg);?>
                <p><b>Ráfordítások</b></p>
                <table>
                    <thead>
                        <tr>
                            <th>Alapanyag</th>
                            <th>Mennyiség</th>
                            <th>Egységár</th>
                            <th>Költség</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?  
                            $sql = "SELECT * FROM ((alapanyag_vasar INNER JOIN csapat ON csapat.csapat_id = alapanyag_vasar.csapat_id) INNER JOIN alapanyag ON alapanyag.alapanyag_id = alapanyag_vasar.alapanyag_id) WHERE csapat.csapat_id = ?";
                            $osszeg = 0;
                            if ($result2 = $conn->execute_query($sql,[$csapat["csapat_id"]])) {
                                foreach ($result2 as $row) {
                                    $osszeg += $row["ar"]*$row["vasarolt_mennyiseg"];
                                    ?>
                                        <tr>
                                            <td><? echo $row["alapanyag_nev"]?></td>
                                            <td><? echo $row["vasarolt_mennyiseg"]?></td>
                                            <td><? echo $row["ar"]?></td>
                                            <td><? echo $row["vasarolt_mennyiseg"]*$row["ar"]?></td>
                                        </tr>                                  
                                    <?
                                }
                                array_push($jovedelem,$osszeg);
                            }
                        ?>
                        <tr>
                            <th colspan="3" style="text-align: center;">Összesen</th>
                            <th style="text-align: left;"><? echo $osszeg?></th>
                        </tr>
                    </tbody>
                </table>
                <?
                    $osszeg = 0;
                    if ($hasKotber) {?>
                        <p><b>Kötbér</b></p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Termék</th>
                                    <th>Vállalt mennyiség</th>
                                    <th>Átvett mennyiség</th>
                                    <th>Hiányzó mennyiség</th>
                                    <th>Kötbér egységenként</th>
                                    <th>Költség</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    
                                    foreach ($bevetelQuery as $row) {
                                        $osszeg+=round($row["kotber"]);
                                        if (($row["elfogadott_mennyiseg"]-$row["elso"]-$row["masodik"])>0) {
                                        ?>
                                            <tr>
                                                <td><? echo $row["termek_nev"]?></td>
                                                <td><? echo $row["elfogadott_mennyiseg"]?></td>
                                                <td><? echo $row["elso"]+$row["masodik"]?></td>
                                                <td><? echo $row["elfogadott_mennyiseg"]-$row["elso"]-$row["masodik"]?></td>
                                                <td><? echo ($row["elsoAR"]*0.3)?></td>
                                                <td><? echo round($row["kotber"])?></td>
                                            </tr>
                                        <?
                                        }
                                    }
                                    
                                ?>
                                <tr>
                                    <th colspan="5" style="text-align: center;">Összesen</th>
                                    <th style="text-align: left;"><? echo $osszeg?></th>
                                </tr>
                            </tbody>
                        </table>
                        <?}
                    array_push($jovedelem,$osszeg);
                ?>
                <p><b>Jövedelem</b></p>
                <table>
                    <thead>
                        <tr>
                            <th>Árbevétel</th>
                            <th>Ráfordítások</th>
                            <th>Kötbér</th>
                            <th>Profit</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><? echo $jovedelem[0]?></td>
                            <td><? echo $jovedelem[1]?></td>
                            <td><? echo $jovedelem[2]?></td>
                            <td><b><? echo $jovedelem[0]-$jovedelem[1]-$jovedelem[2]?><b></td>
                        </tr>
                    </tbody>
                </table>
            <?php 
                if (!isset($_GET["inc"])) {  
                    $sql = "SELECT * FROM eredmenyek WHERE gameId = ? AND fordulo = ? AND csapat_id = ?";
                    if ($result = $conn->execute_query($sql,[$gameDetails["gameId"],$gameDetails["forduloSzam"],$csapat["csapat_id"]])) {
                        if ($result->num_rows==0) {
                            $sql = "INSERT INTO eredmenyek VALUES (?,?,?,?,?,?)";
                            $conn->execute_query($sql,[$gameDetails["gameId"],$gameDetails["forduloSzam"],$csapat["csapat_id"],$jovedelem[0],$jovedelem[1],$jovedelem[2]]);
                        } else {
                            $sql = "UPDATE eredmenyek SET arbevetel = ? , raforditasok = ?, kotber = ? WHERE gameId = ? AND fordulo = ? AND csapat_id = ?";
                            $conn->execute_query($sql,[$jovedelem[0],$jovedelem[1],$jovedelem[2],$gameDetails["gameId"],$gameDetails["forduloSzam"],$csapat["csapat_id"]]);
                        }
                    }
                }
                if ($futasi_szam!=$csapat_szam) {
                    echo "<div class='pagebreak'> </div>";
                }
            }
        }

    ?>
</body>
</html>