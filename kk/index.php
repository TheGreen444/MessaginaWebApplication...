<?php
// Paths
$userFile = "../dxb/up/abcdefdryz.txt";
$dmFolder = "../dxb/dm/";
$msgFolder = "../dxb/msg/";

if (!file_exists($msgFolder)) {
    mkdir($msgFolder, 0777, true);
}

if (!isset($_COOKIE["abcdefdryz"])) {
    echo "<script>window.location.href = '../logout';</script>";
}

$decoded = base64_decode($_COOKIE["abcdefdryz"]);
if (!$decoded || strpos($decoded, ":") === false) {
     echo "<script>window.location.href = '../logout';</script>";
}
list($myUsername, $myPassword) = explode(":", $decoded, 2);

// Find my ID
$myId = "";
if (file_exists($userFile)) {
    $lines = file($userFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(":", $line);
        if (count($parts) >= 5 && $parts[0] === $myUsername && $parts[1] === $myPassword) {
            $myId = trim($parts[4]);
            break;
        }
    }
}

if ($myId === "") {
     echo "<script>window.location.href = '../logout';</script>";
}

// Get DM list
$myDMFile = $dmFolder . $myUsername . ".txt";
$dmList = [];
if (file_exists($myDMFile)) {
    $lines = file($myDMFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($myIdInFile, $partnerId) = explode(":", $line, 2);
        // Find partner username
        $partnerName = "";
        $userLines = file($userFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($userLines as $uLine) {
            $uParts = explode(":", $uLine);
            if (count($uParts) >= 5 && trim($uParts[4]) === trim($partnerId)) {
                $partnerName = $uParts[0];
                break;
            }
        }
        if ($partnerName !== "") {
            $dmList[] = ["username" => $partnerName, "userId" => trim($partnerId)];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #0d131d;
    color: #e4e6eb;
    display: flex;
    height: 100vh;
    overflow: hidden;
}

/* Sidebar */
#sidebar {
    width: 19%;
    background: #1c2230;
    padding: 12px;
    overflow-y: auto;
    overflow-x: hidden;
    border-right: 1px solid #2d3446;
    box-shadow: 2px 0 5px rgba(0,0,0,0.2);
}
#sidebar h3 {
    margin: 0 0 15px 0;
    font-size: 18px;
    font-weight: 600;
    /*text-align: center;*/
    color: #03f484;
}

/* DM list */
.dm-user {
    padding: 5px;
    padding-bottom: 12px;
    padding-right: 0;
    padding-top: 12px;
    cursor: pointer;
    /*text-align: center;*/
    background: #232a3b;
    margin-bottom: 8px;
    border-radius: 8px;
    transition: background 0.2s ease, transform 0.1s ease;
}
.dm-user:hover {
    background: #2f364d;
    transform: scale(1.02);
}

/* Chat section */
#chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 15px;
    background: #0f1622;
}

#messages {
    flex: 1;
    overflow-y: auto;
    background: #141a27;
    padding: 15px;
    border-radius: 8px;
    box-shadow: inset 0 0 10px rgba(0,0,0,0.3);
    scrollbar-width: thick;
    scrollbar-color: #665d5d #665d5d;
}

#messages::-webkit-scrollbar {
    width: 6px;
}
#messages::-webkit-scrollbar-thumb {
    background-color: #03f484;
    border-radius: 3px;
}

/* Input area */
#input-box {
    display: flex;
    margin-top: 12px;
    background: #1e2433;
    border-radius: 8px;
    padding: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
#input-box input {
    flex: 1;
    padding: 10px 12px;
    background: transparent;
    border: none;
    color: #e4e6eb;
    font-size: 15px;
    outline: none;
}
#input-box input::placeholder {
    color: #888;
}
#input-box button {
    margin-left: 8px;
    padding: 10px 16px;
    background: #03f484;
    color: #0d131d;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.1s ease;
}
#input-box button:hover {
    background: #00d974;
    transform: scale(1.05);
}

</style>
<script>
let currentChatId = "";
let partnerId = "";
let partnerUsername = "";
let myId = "<?php echo $myId; ?>";
let chatInterval = null;
let lastMessageLine = "";  // remember what was at the bottom

function refreshDMList() {
    fetch('/kk/load_dm_list.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('dm-list').innerHTML = data;
        })
        .catch(() => {
            console.error("Failed to refresh DM list");
        });
}

// Refresh every 3 seconds
setInterval(refreshDMList, 3000);


function openChat(username, targetId) {
    
let partnerUsername = username;

// Check if the partner's username has more than 10 characters
if (partnerUsername.length > 10) {
    partnerUsername = partnerUsername.substring(0, 10) + "...";  // Trim it to 10 characters
}

document.querySelector("#chat h1").innerHTML = partnerUsername + '​<button style="background: none; border: none; font-size: 24px; cursor: pointer;">🤙</button>';



    partnerId = targetId;
    currentChatId = [myId, targetId].sort().join("_");

    // Clear any previous interval
    if (chatInterval) {
        clearInterval(chatInterval);
    }

    // First immediate load
    loadMessages(username);

    // Start refreshing every 1 second
    chatInterval = setInterval(() => {
        loadMessages(username);
    }, 1000);
}

// Call this once per second after openChat()
function loadMessages(username) {
  fetch("/kk/load_messages.php?chat=" + currentChatId)
    .then(r => r.text())
    .then(html => {
      const messagesDiv = document.getElementById("messages");
      messagesDiv.innerHTML = html || "No messages yet.";

      // Grab the last line of text (split on newline)
      // innerText collapses the <br> into actual newlines
      const lines      = messagesDiv.innerText.trim().split("\n");
      const lastLine   = lines[lines.length - 1] || "";

 // Check if the user is at the bottom
            const atBottom = messagesDiv.scrollHeight - messagesDiv.scrollTop <= messagesDiv.clientHeight + 200; // 50px threshold

      // If the last line changed, scroll to bottom
      if (lastLine !== lastMessageLine && atBottom) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        lastMessageLine = lastLine;
      }

    })
    .catch(() => {
      document.getElementById("messages").innerHTML = "Error loading messages.";
    });
}


// function openChat(username, targetId) {
//     partnerId = targetId;
//     currentChatId = [myId, targetId].sort().join("_");
//     document.getElementById("messages").innerHTML = "Loading chat with " + username + "...";

//     fetch("/kk/load_messages.php?chat=" + currentChatId)
//         .then(response => response.text())
//         .then(data => {
//             document.getElementById("messages").innerHTML = data || "No messages yet.";
//         });
// }

function sendMessage() {
    const msg = document.getElementById("msg-input").value.trim();
    if (!msg || !currentChatId) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/kk/send_message.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status === 200) {
            // openChat("", partnerId); // reload messages
            document.getElementById("msg-input").value = "";
        }
    };
    xhr.send("chat=" + encodeURIComponent(currentChatId) + "&from=" + encodeURIComponent(myId) + "&message=" + encodeURIComponent(msg));
        setTimeout(() => {
        const messagesDiv = document.getElementById("messages");
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }, 336);
}
// Listen for keyboard events globally
document.addEventListener("keydown", function(event) {
    const input = document.getElementById("msg-input");

    // If "/" is pressed anywhere, focus input field and prevent "/" from appearing
    if (event.key === "/") {
        event.preventDefault();
        input.focus();
    }

    // If Enter is pressed while input is focused and not empty, call sendMessage()
    if (event.key === "Enter" && document.activeElement === input && input.value.trim() !== "") {
        event.preventDefault();
        sendMessage();
    }
});

</script>
</head>
<body>
<div id="sidebar">
    <h3 onclick="location.href = location.href;" style="cursor: pointer;">&nbsp;Ur DMs&nbsp;<a href="/kk/search4dm.php" style="color: #ff0000; font-family: bolder; font-size: 24px; text-decoration: none;">+</a></h3>
    <div id="dm-list">
        <?php if (empty($dmList)): ?>
            <p>No DMs yet.</p>
        <?php else: ?>
            <?php foreach ($dmList as $dm): ?>
                <div class="dm-user" onclick="openChat('<?php echo $dm['username']; ?>', '<?php echo $dm['userId']; ?>')">
                    <?php echo htmlspecialchars($dm['username']); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="chat">
        <h1 style="margin-top: -4px; font-family: Monospace;"></h1>
        <button onclick="logout()" style=" position: absolute; right: 14px; top: 10px; background:#03f484; border:none; padding:6px 11px; border-radius:5px; font-weight:600; cursor:pointer;">logout</button>

    <div id="messages"><span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select a user to chat...</span></div>
    <div id="input-box">
        <input type="text" id="msg-input" placeholder="Type your message...">
        <button onclick="sendMessage()">Send</button>
    </div>
</div>
</body>
<script>
function logout(){
window.location.href = "../logout";
}
</script>
</html>
