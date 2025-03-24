<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <?php require "assets/bootstrap.php"?>
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/alert.css">
    <script src="scripts/alertclass.js"></script>
    <script src="scripts/modifications.js"></script>
    <script src="scripts/nyomtat.js"></script>

    <style>
        button.btn.sbmt {
            height: calc(3.5rem + calc(var(--bs-border-width) * 2));
            min-height: calc(3.5rem + calc(var(--bs-border-width) * 2));
            line-height: 1.25;
            
        }
        
    </style>
    
</head>
<body>
    <? require "assets/nav.php"?>
    <div class="container">
        <div class="roundedBox">
            <h3>Létrehozott játékok</h3>
            <hr>
            <?
                require "db.php";
                $sql = "SELECT * FROM game ORDER BY created DESC";
                if ($result = $conn->execute_query($sql)) {
                    if ($result->num_rows>0) {
                        ?>
                            <table>
                                <thead>
                                    <tr style="width: 100%;">
                                        <th style="width: 33%;">Név</th>
                                        <th style="width: 33%;">Jelenlegi állás</th>
                                        <th style="width: 33%;">Létrehozva</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                        <?
                        foreach ($result as $row) {
                            ?>
                                <tr onclick="window.location.href='openGame?gameId=<?echo $row["gameId"]?>'">
                                    <td >
                                        <? echo $row["gameName"]?>
                                    </td>
                                    <td >
                                        Forduló: <? echo $row["forduloSzam"]?>, Menet: <? echo $row["menetSzam"]?>
                                    </td>
                                    <td >
                                        <? echo $row["created"]?>
                                    </td>
                                    <td>
                                        <button class="btn-close" onclick="del(<?echo $row["gameId"]?>)"></button>
                                    </td>
                                </tr>
                            <?
                        }
                        ?>
                        </tbody>
                        </table>
                        <?
                    } else {
                        ?>
                            <p><b>Nincs még létrehozott játéka!</b></p>
                            <button class="btn btn-secondary" type="button" onclick="window.location.href = 'newGame'">Létrehozás</button>
                        <?
                    }
                }
            ?>
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
    function del(id) {
        event.stopPropagation();
        if (confirm("Biztosan törölni szeretné a megadott játékot?")) {
            let params = [];
                params.push(id);
                deleteData(3,params).then(r=>{
                    if (r.status) {
                        window.location.href="index";
                    } else {
                        new Alert("Sikertelen törlés!","warning");
                    }                
                }).catch(r=>{
                    new Alert(r.message,"warning");
                });
        }
    }
</script>
</html>