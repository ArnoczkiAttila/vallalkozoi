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
            <h2>Új játék létrehozása</h2>
            <hr>
            <form id="insertForm" autocomplete="off">
                <div class="row">
                    <div class="col-4">
                        <div class="form-floating mb-3">
                            <input type="text" name="gameName" class="form-control" id="gameName" placeholder="">
                            <label for="gameName">Játék megnevezése</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-secondary sbmt" type="button" onclick="insertIntoDatabase()">Létrehozás</button>
                    </div>
                </div>
            </form>
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
    function insertIntoDatabase() {
        let fd = new FormData(document.getElementById("insertForm"));
        let isValid = true;
        let params = [];
        for (let [key,value] of fd.entries()) {
            if (value == "") {
                isValid = false;
                new Alert("Minden mezőt ki kell tölteni!","warning");
                break;
            } 
            params.push(value);  
        }
        if (isValid) {
            insertData(3,params).then(r=>{
                if (r.status) {
                    new Alert(r.message,"success");
                    for (let [key,value] of fd.entries()) {
                        document.getElementsByName(""+key)[0].value = "";
                    }
                    setTimeout(()=>{
                        window.location.href="index";
                    },1000);
                } else {
                    new Alert(r.message,"warning");
                }                
            }).catch(r=>{
                new Alert(r.message,"warning");
            });
        }
    }
</script>
</html>