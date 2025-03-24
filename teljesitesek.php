<?php 
    if (isset($_POST['ment'])) {

        $data=$_POST['data'];

        foreach ($data as $row) 
        { 
            if ($row['elso']!='' && $row['masodik']!='')
            {
                $sql = "UPDATE vegso_allapot SET elso='".$row["elso"]."', masodik='".$row["masodik"]."' WHERE elfogadott_id='".$row["id"]."'";
                $conn->query($sql); 
            } else if ($row['elso']==''&& $row['masodik']!='') {
                $sql = "UPDATE vegso_allapot SET elso=NULL, masodik='".$row["masodik"]."' WHERE elfogadott_id='".$row["id"]."'";
                $conn->query($sql); 
            } else if ($row['masodik']==''&& $row['elso']!='') {
                $sql = "UPDATE vegso_allapot SET elso='".$row["elso"]."', masodik=NULL WHERE elfogadott_id='".$row["id"]."'";
                $conn->query($sql); 
            } else {
                $sql = "UPDATE vegso_allapot SET elso=NULL, masodik=NULL WHERE elfogadott_id='".$row["id"]."'";
                $conn->query($sql); 
            }
        }
        header("Location: menet?stage6");
    }
?>

    <h2>Teljesítések</h2>
    <hr>
    <p style="color: green;">(Az első osztályú termék az árnak a 100%-a, A másod osztályú pedig a 70%-a.)</p>
    <form method="post">
    <table>
        <tr>
           <th>Vállalat</th>
           <th>Termék</th>
           <th>Vállalt mennyiség</th>
           <th>Termék ár</th>
           <th>Első osztályú</th> 
           <th>Másod osztályú</th>
        </tr>
        <?php 
            $sql = "SELECT * FROM ((vegso_allapot INNER JOIN csapat ON vegso_allapot.csapat_id = csapat.csapat_id) INNER JOIN termek ON vegso_allapot.termek_id = termek.termek_id) WHERE csapat.gameId = $gameDetails[gameId] ORDER BY csapat_nev ASC";
            $result = $conn->query($sql);
            $c = 0;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td><input type='hidden' name='data[$c][id]' value='".$row["elfogadott_id"]."'>".$row["csapat_nev"]."</td><td>".$row["termek_nev"]."</td><td>".$row["elfogadott_mennyiseg"]."</td><td>".$row["elfogadott_ar"]."</td><td><input type='number' name='data[$c][elso]' max='".$row["elfogadott_mennyiseg"]."' value='".$row["elso"]."'></td><td><input type='number' max='".$row["elfogadott_mennyiseg"]."' name='data[$c][masodik]' value='".$row["masodik"]."'></td></tr>";
                    $c++;
                }
            }
        ?>
    </table>
    <br><br>
    <?
        if ($gameDetails["menetSzam"]>1) {
            ?>
                <button class="btn btn-info ms-3" style="float: right;" onclick="nyomtat('beadott_nyomtat.php?osszes')">Nyomtatás (Összegzett)</button>

            <?
        }
    ?>
    <button class="btn btn-secondary " name="ment">Tovább</button>
    </form>
