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
    $sql = "
    SELECT *
    FROM csapat 
    INNER JOIN ajanlat ON ajanlat.csapat_id = csapat.csapat_id
    INNER JOIN termek ON termek.termek_id = ajanlat.termek_id
    WHERE termek.termek_felhivas IS NOT NULL
    AND gameId = {$gameDetails['gameId']}
";

    $result = $conn->query($sql);

    // Build results and offers arrays
    $results = [];
    $offers = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row; // Needed for display + DB insert

        $termekId = $row['termek_id'];
        if (!isset($offers[$termekId])) {
            $offers[$termekId] = [
                'felhivas' => (int)$row['termek_felhivas'],
                'ajanlatok' => [],
            ];
        }

        $offers[$termekId]['ajanlatok'][] = [
            'csapat_id' => $row['csapat_id'],
            'mennyiseg' => (int)$row['mennyiseg'],
            'ar' => (float)$row['ar']
        ];
    }

    // === PROCESS WINNING OFFERS ===
    $elnyert = [];

    foreach ($offers as $termekId => $data) {
        $felhivas = $data['felhivas'];
        $ajanlatok = $data['ajanlatok'];

        // Process offers until the call is fulfilled
        while ($felhivas > 0) {
            // Find lowest price
            $minAr = null;
            foreach ($ajanlatok as $ajanlat) {
                if ($ajanlat['mennyiseg'] > 0) {
                    if ($minAr === null || $ajanlat['ar'] < $minAr) {
                        $minAr = $ajanlat['ar'];
                    }
                }
            }

            if ($minAr === null) {
                break; // No more offers
            }

            // Get winners with that price
            $nyertesek = [];
            foreach ($ajanlatok as $key => $ajanlat) {
                if ($ajanlat['ar'] == $minAr && $ajanlat['mennyiseg'] > 0) {
                    $nyertesek[] = &$ajanlatok[$key];
                }
            }

            $nyertesekSzama = count($nyertesek);
            if ($nyertesekSzama === 0) break;

            $egyCsapatra = floor($felhivas / $nyertesekSzama);
            $maradek = $felhivas % $nyertesekSzama;

            foreach ($nyertesek as $index => &$nyertes) {
                $adhato = min($nyertes['mennyiseg'], $egyCsapatra);
                if ($index == $nyertesekSzama - 1) {
                    $adhato += $maradek;
                }

                if ($adhato > 0) {
                    $elnyert[] = [
                        'csapat_id' => $nyertes['csapat_id'],
                        'termek_id' => $termekId,
                        'elnyert_mennyiseg' => $adhato,
                        'ar' => $nyertes['ar']
                    ];

                    $felhivas -= $adhato;
                    $nyertes['mennyiseg'] -= $adhato;
                }
            }
        }
    }

    // === DISPLAY AND INSERT RESULTS ===
    foreach ($results as $row) {
        $elutasitva = true;
        echo "<tr>
        <td>{$row['csapat_nev']}</td>
        <td>{$row['termek_nev']}</td>
        <td>{$row['termek_felhivas']}</td>
        <td>{$row['termek_ar']}</td>
        <td>{$row['mennyiseg']}</td>
        <td>{$row['ar']}</td>";

        foreach ($elnyert as $nyertes) {
            if (
                $nyertes['csapat_id'] == $row['csapat_id'] &&
                $nyertes['termek_id'] == $row['termek_id'] &&
                $nyertes['ar'] == $row['ar']
            ) {
                $elutasitva = false;

                echo "<td>{$nyertes['elnyert_mennyiseg']}</td>
                  <td style='color:green;'>&#10003;</td>";

                // Insert if not already exists
                $ajanlatId = $row['ajanlat_id'];
                $checkSql = "SELECT 1 FROM elnyert_ajanlat WHERE ajanlat_id = '$ajanlatId' LIMIT 1";
                $checkResult = $conn->query($checkSql);

                if ($checkResult->num_rows === 0) {
                    $insertSql = "
                    INSERT INTO elnyert_ajanlat (ajanlat_id, elfogadott_mennyiseg, elfogadott_ar)
                    VALUES ('$ajanlatId', '{$nyertes['elnyert_mennyiseg']}', '{$nyertes['ar']}')
                ";
                    $conn->query($insertSql);
                }

                break;
            }
        }

        if ($elutasitva) {
            echo "<td>0</td><td style='color:red;'>&#10060;</td>";
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