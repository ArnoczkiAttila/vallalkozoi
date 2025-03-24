<?php
    $x = 0;
	
            $sql = "SELECT * FROM ajanlat";
            $ajanlat_result = $conn->query($sql);
            $ajanlatok = array();

            if ($ajanlat_result->num_rows > 0)
            {
                while($_row = $ajanlat_result->fetch_assoc())
                {
                    $ajanlatok[] = $_row;
                }
            }
			
    if (isset($_POST['ment'])) {
        foreach($_POST["data"] as $row)
        {
            if ($row["darab"] !='' && $row["ar"] !='' && $row["darab"] > 0 && $row["ar"] > 0)
            {
                $temp = explode("-", $row["id"]);
                $csapat_id = $temp[0];
                $termek_id = $temp[1];
                $sql = "INSERT INTO ajanlat (termek_id, csapat_id, mennyiseg, ar) VALUES ('$termek_id', '$csapat_id', '". $row["darab"]. "', '". $row["ar"]. "')";
				
				foreach ($ajanlatok as $i)
                    {
						if ($i["termek_id"] == $termek_id && $i["csapat_id"] == $csapat_id)
						{
							$sql = "UPDATE ajanlat SET mennyiseg = ". $row["darab"] .", ar = ". $row["ar"] ." WHERE termek_id='$termek_id' AND csapat_id='$csapat_id'";
							break;
                        }
                    }
                $result = $conn->query($sql);
            }
        }
        
	header("Location: menet?stage4");
    }
?>

    <h2>Ajánlatok szerkesztése</h2>
    <hr>
    <form method="post">
    <table>
        <tr>
            <th rowspan="2">Termék</th>
            <th colspan="2">Felhívás</th>
            <th colspan="2">Ajánlat</th>
        </tr>
        <tr>
            <th>Mennyiség</th>
            <th>Ár</th>
            <th>Mennyiség</th>
            <th>Ár</th>
        </tr>
        <?php
            $sql = "SELECT * FROM csapat WHERE gameId = $gameDetails[gameId]";
            $result = $conn->query($sql);
            $sql = "SELECT * FROM termek WHERE termek_felhivas > 0 AND termek_ar > 0";
            $termek_result = $conn->query($sql);
            $termek = array();

            if ($termek_result->num_rows > 0)
            {
                while($_row = $termek_result->fetch_assoc())
                {
                    $termek[] = $_row;
                }
            }
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr><th colspan='5' style='background-color:#b0aca0;'>". $row["csapat_nev"] ."</th></tr>";
                    foreach ($termek as $_row) 
                    {
                        if (!is_null($_row["termek_ar"]) || !is_null($_row["termek_felhivas"]) && ($_row["termek_felhivas"] > 0 && $_row["termek_ar"] > 0))
                        {
                            $darabval = "";
                            $darabar = "";
                            foreach ($ajanlatok as $i)
                            {
                                if ($i["termek_id"] == $_row["termek_id"] && $i["csapat_id"] == $row["csapat_id"])
                                {
                                    $darabval = $i["mennyiseg"];
                                    $darabar = $i["ar"];
                                }
                            }
                            echo "<tr><input type='hidden' style='display: none;' name='data[$x][id]' value='".$row["csapat_id"]."-".$_row["termek_id"]."'><td>". $_row["termek_nev"] ."</td><td>". $_row["termek_felhivas"] ."</td><td>". $_row["termek_ar"] ."</td> <td><input type='number' min='0' value='$darabval' name='data[$x][darab]'></td> <td><input type='number' min='0' name='data[$x][ar]' value='$darabar'></td> </tr>";
                            $x++;
                        }
                    }
                }
        }
            $conn->close();
        ?>
    </table>
    <br>
    <button class="btn btn-secondary" type="button" onclick='window.location.href = "menet?stage2"'>Vissza</button>
    <button class="btn btn-secondary" id="ment" name="ment">Tovább</button>
    </form>
