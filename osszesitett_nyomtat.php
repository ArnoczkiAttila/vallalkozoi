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
    <?
        $sql = "SELECT fordulo FROM `eredmenyek` WHERE gameId = $gameDetails[gameId] GROUP BY fordulo";
        $fordulok = $conn->execute_query($sql);
        foreach ($fordulok as $sor) {
            $c = 0;
            $sql = "SELECT * FROM eredmenyek INNER JOIN csapat ON eredmenyek.csapat_id = csapat.csapat_id WHERE eredmenyek.gameId = $gameDetails[gameId] AND eredmenyek.fordulo = $sor[fordulo] ORDER BY arbevetel-raforditasok-kotber DESC";
            $result = $conn->execute_query($sql);
            ?>
            <h2>Forduló: <? echo $sor["fordulo"]?></h2>
                    <hr>
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
            foreach ($result as $row) {
                $c++;?>
                
                        <tr>
                            <td><? echo $c?></td>
                            <td><? echo $row["csapat_nev"]?></td>
                            <td><? echo $row["arbevetel"]?></td>
                            <td><? echo $row["raforditasok"]+$row["kotber"]?></td>
                            <td><? echo $row["arbevetel"]-$row["raforditasok"]-$row["kotber"]?></td>
                        </tr>
                                
                    <?       
            }   
            ?>
                        </tbody>
                    </table>
                    <br>
            <?  
        }
        $sql = "SELECT csapat_id,csapat_nev FROM csapat WHERE gameId = $gameDetails[gameId]";
        $csapatok = $conn->execute_query($sql);
        $tempArray = array();
        foreach ($csapatok as $csapat) {
            $sql = "SELECT SUM(arbevetel-raforditasok-kotber) AS sum FROM `eredmenyek` WHERE gameId = $gameDetails[gameId] AND csapat_id = $csapat[csapat_id]";
            $result = $conn->execute_query($sql);
            foreach ($result as $row) {
                array_push($tempArray,[$csapat["csapat_nev"],intval($row["sum"])]);
            }
        }
        usort($tempArray, function($b, $a) {
            return $a[1] - $b[1];
        });
        ?>
            <h2>Fordulók összesítve</h2>
            <hr>
            <br>
            <table>
                <thead>
                    <tr>
                        <th>Nr.</th>
                        <th>Vállalat</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                        $c = 0;
                        foreach ($tempArray as $row) {
                            $c++?>
                                <tr>
                                    <td><? echo $c?></td>
                                    <td><? echo $row[0]?></td>
                                    <td><? echo $row[1]?></td>
                                </tr>
                            <?
                        }
                    ?>
                </tbody>
            </table>
        <?
    ?>
</body>
</html>