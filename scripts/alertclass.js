class Alert {
    constructor(message,status) {
        document.getElementById("cosAlert").style.display = "none";
        switch (status) {
            case "success":
                document.getElementById("cosAlert").className = "alert alert-success alert-dismissible fade show";
                document.getElementById("cosAlert").style.backgroundColor = "var(--bs-success)";
                document.getElementById("cosAlert").style.color = "var(--bs-teal);";
                
                break;
            case "warning":
                document.getElementById("cosAlert").className = "alert alert-danger alert-dismissible fade show";
                document.getElementById("cosAlert").style.backgroundColor = "var(--bs-danger)";
                document.getElementById("cosAlert").style.color = "black";
                break;
            default:
                break;
        }
        document.getElementById("alertMessage").innerHTML = message;
        document.getElementById("cosAlert").style.display = "block";
        setTimeout(() => {
            document.getElementById("cosAlert").style.display = "none";
        }, 3000);
    }
}
