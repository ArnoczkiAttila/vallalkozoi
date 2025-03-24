<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termékek</title>
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
            <h2>Termékek</h2>
            <hr>
            <p style="color:red;">* A módosításhoz válasszon ki egyet!</p>
            <table>
                <thead>
                    <tr>
                        <th>Név</th>
                        <th>Leiírás</th>
                        <th>Normaidő</th>
                        <th>Bonyolultság</th>
                        <th>Felhívás</th>
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
                            <input type="text" required class="form-control" name="nev" id="nev" placeholder="Megnevezés">
                            <label for="nev">Név</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <textarea class="form-control" placeholder="Leave a comment here" id="leiras" name="leiras"></textarea>
                            <label for="leiras">Leírás</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="number" required class="form-control" name="normaido" id="normaido" placeholder="Ár">
                            <label for="normaido">Normaidő</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="number" required class="form-control" name="bony" id="bony" placeholder="Ár">
                            <label for="bony">Bonyolultság</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="number" required class="form-control" name="felhivas" id="felhivas" placeholder="Ár">
                            <label for="felhivas">Felhívás</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="number" required class="form-control" name="ar" id="ar" placeholder="Ár">
                            <label for="ar">Ár</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
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
                    <input type="text" required class="form-control" name="modifyNev" id="modifyNev" placeholder="Megnevezés">
                    <label for="modifyNev">Név</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Leave a comment here" id="modifyLeiras" name="modifyLeiras"></textarea>
                    <label for="modifyLeiras">Leírás</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" required class="form-control" name="modifyNormaido" id="modifyNormaido" placeholder="Ár">
                    <label for="modifyNormaido">Normaidő</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" required class="form-control" name="modifyBony" id="modifyBony" placeholder="Ár">
                    <label for="modifyBony">Bonyolultság</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" required class="form-control" name="modifyFelhivas" id="modifyFelhivas" placeholder="Ár">
                    <label for="modifyFelhivas">Felhívás</label>
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
        fetchData(2).then(r=>{
            if (r.status) {
                if (r.numrows>0) {
                    document.getElementById("tb").innerHTML ="";
                    r.data.forEach(i => {  
                        document.getElementById("tb").innerHTML += `<tr data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="modifyData(${i.termek_id},this)"><td>${i.termek_nev}</td><td>${i.termek_leiras}</td><td>${i.termek_normaido}</td><td>${i.termek_bony}</td><td>${i.termek_felhivas}</td><td>${i.termek_ar}</td></tr>`;

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
            insertData(2,params).then(r=>{
                console.log(r);
                if (r.status) {
                    new Alert(r.message,"success");
                    refreshData();
                    for (let [key,value] of fd.entries()) {
                        document.getElementsByName(""+key)[0].value = "";
                    }
                } else {
                    console.log(r.message);
                    new Alert(r.message,"warning");
                }                
            }).catch(r=>{
                new Alert(r.message,"warning");
            });
        }
    }

    function modifyData(id,element) {
        document.getElementById("modifyNev").value = element.childNodes[0].innerHTML;
        document.getElementById("modifyLeiras").value = element.childNodes[1].innerHTML;
        document.getElementById("modifyNormaido").value = element.childNodes[2].innerHTML;
        document.getElementById("modifyBony").value = element.childNodes[3].innerHTML;
        document.getElementById("modifyFelhivas").value = element.childNodes[4].innerHTML;
        document.getElementById("modifyAr").value = element.childNodes[5].innerHTML;
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
            updateData(2,params).then(r=>{
                if (r.status) {
                    new Alert(r.message,"success");
                    refreshData();
                } else {
                    new Alert(r.message,"warning");
                }                
            }).catch(r=>{
                new Alert("Hiba!","warning");
            });
        }
    });

    document.getElementById("delete").addEventListener("click",(e)=>{
        if (confirm("Biztosan törölné szeretné az adott rekordot?")) {
            let params = [];
            params.push(modalId);

            deleteData(2,params).then(r=>{
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