<?php
// Paths
$userFile = "../dxb/up/abcdefdryz.txt";
$dmFolder = "../dxb/dm/";

if (!isset($_COOKIE["abcdefdryz"])) {
    die("<p>Not logged in.</p>");
}

$decoded = base64_decode($_COOKIE["abcdefdryz"]);
if (!$decoded || strpos($decoded, ":") === false) {
    die("<p>Invalid session.</p>");
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
    die("<p>User not found.</p>");
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

// Output DM list
if (empty($dmList)) {
    echo "<p>No DMs yet.</p>";
} else {
    foreach ($dmList as $dm) {
        echo '<div class="dm-user" onclick="openChat(\'' .
             htmlspecialchars($dm['username']) . '\', \'' .
             htmlspecialchars($dm['userId']) . '\')">' .
             htmlspecialchars($dm['username']) . '</div>';
    }
}
?>
