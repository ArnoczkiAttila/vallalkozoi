
    <h2>Elnyert ajánlatok</h2>
    <hr>

    <table>
        <tr>
            <th rowspan='2'>Vállalatok</th>
            <th rowspan='2'>Termék</th>
            <th colspan='2'>Felhivás</th>
            <th colspan='2'>Ajánlat</th>
            <th rowspan='2'>Elnyert mennyiség</th>
            <th rowspan='2'>Státusz</th>
        </tr>
        <tr>
            <th>Mennyiség</th>
            <th>Ár</th>
            <th>Mennyiség</th>
            <th>Ár</th>
        </tr>
        <?php
            $sql = "SELECT * FROM ((csapat INNER JOIN ajanlat ON ajanlat.csapat_id = csapat.csapat_id) INNER JOIN termek ON termek.termek_id = ajanlat.termek_id) WHERE (termek_felhivas IS NOT NULL) AND gameId = $gameDetails[gameId]";
            $result = $conn->query($sql);
            //Csinálok egy 2D-s tömböt az eredményekből, hogy tudjak benne mászálni és számolni
            $results = array();
            while($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $results[] = $line;
            }
            $num_rows = mysqli_num_rows($result);
            //Termékek kigyűjtése ismétlődés nélkül és hozzá a felhívásban szereplő mennyiség
            $termekek = array();
            $termek_maradt = array();
            for ($i=0; $i < $num_rows; $i++) 
            {
                if (!in_array($results[$i]["termek_id"],$termekek))
                {
                    array_push($termekek, $results[$i]["termek_id"]);
                    array_push($termek_maradt, $results[$i]["termek_felhivas"]);
                }
            }
            
            //Lemásolom a tömböt, hogy bele tudjak írni
            $results_copy = $results;
            $elnyert = array();
            //Megyek végig a termékeken
            for ($t=0; $t < count($termekek); $t++)
            {
                //Amíg még van az adott termékből
                while ($termek_maradt[$t]!=0)
                {
                    //Azért kell tömb, mert lehet holtverseny van, azaz több csapat is ugyazat az árat adta a termékre
                    $nyertesek = array();
                    $mennyisegek = array();
                    //Keresem a legjobb árat az adott termékre
                    $min=10000000;
                    for ($i=0; $i < $num_rows; $i++) 
                    { 
                        if ($results[$i]["ar"] <= $min && $results[$i]["termek_id"]==$termekek[$t])
                        {
                            $min = $results[$i]["ar"];
                        }
                    }
                    //Kigyűjtöm azt vagy azokat akik a legjobb árat adták és hogy mennyit készítenének
                    for ($i=0; $i < $num_rows; $i++) 
                    { 
                        if ($results[$i]["ar"] == $min && $results[$i]["termek_id"]==$termekek[$t])
                        {
                            array_push($nyertesek,$results[$i]["csapat_id"]);
                            array_push($mennyisegek, $results[$i]["mennyiseg"]);
                            //Hülye értéket állítok be, hogy többször ne vegye figyelembe ezt a rekordot
                            $results[$i]["termek_id"]=0;
                        }
                    }
                    //Ha egy legjobb ajánlat van
                    if (count($nyertesek)==1)
                    {
                        //Ha van még keret a termékből akkor megkapja amit vállalt
                        if ($mennyisegek[0]<=$termek_maradt[$t])
                        {
                            array_push($elnyert,array($nyertesek[0],$mennyisegek[0],$termekek[$t]));
                            $termek_maradt[$t]-=$mennyisegek[0];
                        }
                        //Ha nincs keret akkor csak a rendelkezésre állót kapja
                        else
                        {
                            array_push($elnyert,array($nyertesek[0],$termek_maradt[$t],$termekek[$t]));
                            $termek_maradt[$t]=0;
                        }
                        
                    }
                    //Ha több egyforma ajánlat van akkor a rendelkezésre álló mennyiséget szétosztjuk
                    else if (count($nyertesek)>1)
                    {
                        $egy_csapatra_eso = floor($termek_maradt[$t] / count($nyertesek));
                        $maradek = $termek_maradt[$t] % count($nyertesek); // Remaining units after division

                        for ($j = 0; $j < count($nyertesek); $j++) { 
                            $adott_mennyiseg = ($mennyisegek[$j] <= $egy_csapatra_eso) ? $mennyisegek[$j] : $egy_csapatra_eso;
                            
                            if ($j == count($nyertesek) - 1) {
                                $adott_mennyiseg += $maradek;
                            }

                            array_push($elnyert, array($nyertesek[$j], $adott_mennyiseg, $termekek[$t]));
                            $termek_maradt[$t] -= $adott_mennyiseg;
                        }
                    }
                    //Ha már nem találunk a termékre ajánlatot de még lenne belőle akkor is abbahagyjuk a keresést
                    else if (count($nyertesek)==0)
                    {
                        break;
                    }
                    //!!!!!!!!!!!!!
                }
            }
            
            
            for ($i=0; $i < $num_rows; $i++) 
            { 
                $elutasitva = true;
                echo "<tr><td>".$results_copy[$i]["csapat_nev"]."</td><td>".$results_copy[$i]["termek_nev"]."</td><td>".$results_copy[$i]["termek_felhivas"]."</td><td>".$results_copy[$i]["termek_ar"]."</td><td>".$results_copy[$i]["mennyiseg"]."</td><td>".$results_copy[$i]["ar"]."</td>";
                for ($j=0; $j < count($elnyert) ; $j++) 
                { 
                    if ($elnyert[$j][0]==$results_copy[$i]["csapat_id"] && $elnyert[$j][2]==$results_copy[$i]["termek_id"])
                    {
                        $elutasitva = false;
                        echo "<td>" . $elnyert[$j][1] . "</td>";
                        echo "<td style='color:green;'> &#10003; </td>";


                        $sql = "SELECT ajanlat_id FROM elnyert_ajanlat";
                        $result2 = $conn->query($sql);
                        $b = false;
                        if ($result2->num_rows > 0) {
                            while($row2 = $result2->fetch_assoc()) {
                                if ($row2["ajanlat_id"]==$results_copy[$i]["ajanlat_id"]) {
                                    $b = true;
                                    break;
                                }
                            }
                        }
                        if (!$b) {
                            $sql = "INSERT INTO elnyert_ajanlat (ajanlat_id, elfogadott_mennyiseg, elfogadott_ar) VALUES ('".$results_copy[$i]["ajanlat_id"]."','".$elnyert[$j][1]."','".$results_copy[$i]["ar"]."')";
                            $conn->query($sql);
                        }
                        
                    }
                }
                if ($elutasitva)
                {
                    echo "<td> 0 </td><td style='color:red;'> &#10060;</td>";
                }    
                echo "</tr>";
            }
        ?>
    </table>
    <br><br>
    <button class="btn btn-secondary " onclick="window.location.href = 'menet?stage3'">Vissza</button>
    <button class="btn btn-secondary " onclick="window.location.href = 'processing?redirectTo=menet?stage5'">Teljesítések elkezdése</button>
    <button class="btn btn-secondary " onclick="window.location.href = 'processing?nextRound&redirectTo=menet?stage10'">Pótfelhívás létrehozása</button>
    
    <button class="btn btn-info" style="float: right;" onclick="nyomtat('beadott_nyomtat.php')">Nyomtatás</button>


</script>
