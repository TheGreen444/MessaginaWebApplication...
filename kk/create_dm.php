<?php
$filePath = "../dxb/up/abcdefdryz.txt";
$dmFolder = "../dxb/dm/";

if (!file_exists($dmFolder)) {
    mkdir($dmFolder, 0777, true);
}

if (!isset($_COOKIE["abcdefdryz"])) {
    echo "Not logged in.";
    exit;
}

$decoded = base64_decode($_COOKIE["abcdefdryz"]);
if (!$decoded || strpos($decoded, ":") === false) {
    echo "Invalid session.";
    exit;
}

list($myUsername, $myPassword) = explode(":", $decoded, 2);
$targetUsername = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$targetId = isset($_POST["targetId"]) ? trim($_POST["targetId"]) : "";

// Find my userId
$myId = "";
if (file_exists($filePath)) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(":", $line);
        if (count($parts) >= 5 && $parts[0] === $myUsername && $parts[1] === $myPassword) {
            $myId = trim($parts[4]);
            break;
        }
    }
}

if ($myId === "" || $targetId === "" || $targetUsername === "") {
    echo "Failed to create DM.";
    exit;
}

// Add DM entry in my file
$myFile = $dmFolder . $myUsername . ".txt";
$entry1 = $myId . ":" . $targetId;
if (!file_exists($myFile) || strpos(file_get_contents($myFile), $entry1) === false) {
    file_put_contents($myFile, $entry1 . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Add DM entry in target's file
$targetFile = $dmFolder . $targetUsername . ".txt";
$entry2 = $targetId . ":" . $myId;
if (!file_exists($targetFile) || strpos(file_get_contents($targetFile), $entry2) === false) {
    file_put_contents($targetFile, $entry2 . PHP_EOL, FILE_APPEND | LOCK_EX);
}

echo "DM created successfully!";

?>
