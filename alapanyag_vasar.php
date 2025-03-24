<?php 
    if (isset($_POST['ment'])) {

        $data=$_POST['data'];

        foreach ($data as $row) 
        { 
            if ($row['mennyiseg']!='')
            {
                $sql = "UPDATE alapanyag_vasar SET vasarolt_mennyiseg='".$row["mennyiseg"]."' WHERE csapat_id='".$row["cs_id"]."'";
                $conn->query($sql); 
            } else {
                $sql = "UPDATE alapanyag_vasar SET vasarolt_mennyiseg=NULL WHERE csapat_id='".$row["cs_id"]."'";
                $conn->query($sql); 
            }
        }
        header("Location: menet?stage7");
    }
?>

    <h2>Vásárolt alapanyagok</h2>
    <hr>
    <form method="post">
    <table>
        <tr>
            <th>Alapanyag</th>
            <th>Mennyiség</th>
        </tr>
        <?php 
            $sql = "SELECT * FROM ((csapat INNER JOIN alapanyag_vasar ON csapat.csapat_id = alapanyag_vasar.csapat_id) INNER JOIN alapanyag ON alapanyag.alapanyag_id = alapanyag_vasar.alapanyag_id) WHERE csapat.gameId = $gameDetails[gameId]";
            $result = $conn->query($sql);
            $c = 0;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr><tr><td colspan='2'></td></tr><th colspan='2'>".$row["csapat_nev"]."</th></tr>";
                    echo "<tr><td>".$row["alapanyag_nev"]."</td><td><input type='number' value='".$row["vasarolt_mennyiseg"]."' name='data[$c][mennyiseg]'><input type='hidden' name='data[$c][cs_id]' value='".$row["csapat_id"]."'><input type='hidden' name='data[$c][alap_id]' value='".$row["alapanyag_id"]."'></td></tr>";
                    $c++;
                }
            }
        ?>
    </table>
    <br><br>
    <button class="btn btn-secondary" type="button" onclick="window.location.href = 'menet?stage5'">Vissza</button>
    <button class="btn btn-secondary" name="ment" onclick="window.location.href = 'processing?redirectTo=menet?stage5'">Tovább</button>

    </form>
