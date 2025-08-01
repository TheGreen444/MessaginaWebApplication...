<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Live Username Search</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #0d131d;
        color: white;
        margin: 0;
        padding: 20px;
    }
    h2 {
        margin-top: 0;
    }
    #search-box {
        width: 100%;
        max-width: 400px;
        padding: 10px;
        border: none;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 16px;
    }
    #results ul {
        list-style: none;
        padding: 0;
    }
    #results li {
        background: #1a1f2b;
        margin-bottom: 8px;
        padding: 10px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s ease;
    }
    #results li:hover {
        background: #232a3b;
    }
    .dm-btn {
        background: #03f484;
        color: black;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    .dm-btn:hover {
        background: #00c970;
    }
</style>
<script>
function searchUser(str) {
    const results = document.getElementById("results");
    if (str.trim() === "") {
        results.innerHTML = "Start typing...";
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "search_usr.php?q=" + encodeURIComponent(str), true);
    xhr.onload = function() {
        if (this.status === 200 && this.responseText.trim() !== "") {
            results.innerHTML = this.responseText;
        } else {
            results.innerHTML = "No matches found.";
        }
    };
    xhr.onerror = function() {
        results.innerHTML = "Error fetching results.";
    };
    xhr.send();
}

//    old one...
// function createDM(username, targetId) {
//     const xhr = new XMLHttpRequest();
//     xhr.open("POST", "create_dm.php", true);
//     xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//     xhr.onload = function() {
//         alert(this.responseText);
//     };
//     xhr.send("username=" + encodeURIComponent(username) + "&targetId=" + encodeURIComponent(targetId));
// }

function createDM(username, targetId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "create_dm.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        const msg = this.responseText.trim();
        if (msg === "DM created successfully!") {
            alert(msg);
            location.href = "/kk/"; // redirect
        } else {
            alert("Failed: " + msg);
        }
    };
    xhr.send("username=" + encodeURIComponent(username) + "&targetId=" + encodeURIComponent(targetId));
}

</script>
</head>
<body>
<h2>Search Username</h2>
<input type="text" id="search-box" onkeyup="searchUser(this.value)" placeholder="Type a username..." autocomplete="off">
<div id="results">Start typing...</div>
</body>
</html>
