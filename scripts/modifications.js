//updateData
async function updateData(tableIndex,params) {
    return new Promise((resolve,reject)=>{
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                resolve(xhttp.response);
            } else if (xhttp.readyState == 4) {
                reject("Szerver hiba!");
            }
        }
        let url = "../assets/updateData.php?tableIndex="+tableIndex;
        for (let i = 1; i <= params.length; i++) {
            url += ("&value"+i+"="+params[i-1]);            
        }
        xhttp.open("GET",url,true);
        xhttp.send();
    }).then((message)=>{
        return JSON.parse(message);
    }).catch((message)=>{
        new Alert(message,"warning");
        return {};
    });
}

//insertData
async function insertData(tableIndex,params) {
    return new Promise((resolve,reject)=>{
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                resolve(xhttp.response);
            } else if (xhttp.readyState == 4) {
                reject("Szerver hiba!");
            }
        }
        let url = "../assets/insertData.php?tableIndex="+tableIndex;
        for (let i = 1; i <= params.length; i++) {
            url += ("&value"+i+"="+params[i-1]);            
        }
        xhttp.open("GET",url,true);
        xhttp.send();
    }).then((message)=>{
        return JSON.parse(message);
    }).catch((message)=>{
        new Alert(message,"warning");
        return {};
    });
}

//fetchData
async function fetchData(tableIndex) {
    return new Promise((resolve,reject)=>{
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                resolve(xhttp.response);
            } else if (xhttp.readyState == 4) {
                reject("Szerver hiba!");
            }
        }
        xhttp.open("GET","../assets/selectData.php?tableIndex="+tableIndex,true);
        xhttp.send();
    }).then((message)=>{
        return JSON.parse(message);
    }).catch((message)=>{
        new Alert(message,"warning");
        return {};
    });
}

//deleteData
async function deleteData(tableIndex,params) {
    return new Promise((resolve,reject)=>{
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                resolve(xhttp.response);
            } else if (xhttp.readyState == 4) {
                reject("Szerver hiba!");
            }
        }
        let url = "../assets/deleteData.php?tableIndex="+tableIndex;
        for (let i = 1; i <= params.length; i++) {
            url += ("&value"+i+"="+params[i-1]);            
        }
        xhttp.open("GET",url,true);
        xhttp.send();
    }).then((message)=>{
        return JSON.parse(message);
    }).catch((message)=>{
        new Alert(message,"warning");
        return {};
    });
}

//ráfordítások adatbázisba írása
async function rafordit(tableIndex,params) {
    return new Promise((resolve,reject)=>{
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                resolve(xhttp.response);
            } else if (xhttp.readyState == 4) {
                reject("Szerver hiba!");
            }
        }
        let url = "../assets/deleteData.php?tableIndex="+tableIndex;
        for (let i = 1; i <= params.length; i++) {
            url += ("&value"+i+"="+params[i-1]);            
        }
        xhttp.open("GET",url,true);
        xhttp.send();
    }).then((message)=>{
        return JSON.parse(message);
    }).catch((message)=>{
        new Alert(message,"warning");
        return {};
    });
}