<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alapanyagok</title>
    <?php require "assets/bootstrap.php"?>
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/alert.css">
    <script src="scripts/alertclass.js"></script>
    <script src="scripts/modifications.js"></script>

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
            <h2>Alapanyagok</h2>
            <hr>
            <p style="color:red;">* A módosításhoz válasszon ki egyet!</p>
            <table>
                <thead>
                    <tr>
                        <th>Megnevezés</th>
                        <th>Ár</th>
                    </tr>
                </thead>
                <tbody id="tb">

                </tbody>
            </table>
            <h4>Hozzáadás</h4>
            <hr>
            <form id="insertForm" autocomplete="off">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="text" required class="form-control" name="megnevezes" id="megnevezes" placeholder="Megnevezés">
                            <label for="megnevezes">Megnevezés</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="number" required class="form-control" name="ar" id="ar" placeholder="Ár">
                            <label for="ar">Ár</label>
                        </div>
                    </div>
                    <div class="col">
                        <button class="btn btn-secondary sbmt" type="button" onclick="insertIntoDatabase()">Hozzáad</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Módosítás / Törlés</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form autocomplete="off" id="modalForm">
                <div class="form-floating mb-3">
                    <input type="text" required class="form-control" name="modifyMegnevezes" id="modifyMegnevezes" placeholder="Megnevezés">
                    <label for="modifyMegnevezes">Megnevezés</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" required class="form-control" name="modifyAr" id="modifyAr" placeholder="Ár">
                    <label for="modifyAr">Ár</label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="megse">Mégse</button>
            <button type="button" class="btn btn-primary" id="save">Mentés</button>
            <button type="button" class="btn btn-danger" id="delete">Törlés</button>
        </div>
        </div>
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
    let modalId = 0;
    function refreshData() {
        fetchData(0).then(r=>{
            if (r.status) {
                document.getElementById("tb").innerHTML ="";
                if (r.numrows>0) {
                    r.data.forEach(i => {  
                        document.getElementById("tb").innerHTML += `<tr data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="modifyData(${i.alapanyag_id},this)"><td>${i["alapanyag_nev"]}</td><td>${i.ar}</td></tr>`;
                    });
                }
            } else {
                new Alert("Szerver hiba!","warning");
            }
        });
    } 
    refreshData();
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
            insertData(0,params).then(r=>{
                if (r.status) {
                    new Alert(r.message,"success");
                    refreshData();
                    for (let [key,value] of fd.entries()) {
                        document.getElementsByName(""+key)[0].value = "";
                    }
                } else {
                    new Alert(r.message,"warning");
                }                
            }).catch(r=>{
                new Alert(r.message,"warning");
            });
        }
    }

    function modifyData(id,element) {
        document.getElementById("modifyMegnevezes").value = element.childNodes[0].innerHTML;
        document.getElementById("modifyAr").value = element.childNodes[1].innerHTML;
        modalId = id;
    }

    document.getElementById("save").addEventListener("click",(e)=>{
        let fd = new FormData(document.getElementById("modalForm"));
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
        params.push(modalId);
        if (isValid) {
            updateData(0,params).then(r=>{
                if (r.status) {
                    new Alert(r.message,"success");
                    refreshData();
                } else {
                    new Alert(r.message,"warning");
                }                
            }).catch(r=>{
                new Alert(r.message,"warning");
            });
        }
    });

    document.getElementById("delete").addEventListener("click",(e)=>{
        if (confirm("Biztosan törölné szeretné az adott rekordot?")) {
            let params = [];
            params.push(modalId);

            deleteData(0,params).then(r=>{
                if (r.status) {
                    new Alert(r.message,"success");
                    document.getElementById("megse").dispatchEvent(new Event("click"));
                    modalId = 0;
                    refreshData();
                } else {
                    new Alert(r.message,"warning");
                }                
            }).catch(r=>{
                new Alert(r.message,"warning");
            });
        }
    });
</script>
</html>