<?php
$msgFolder = "../dxb/msg/";
$userFile = "../dxb/up/abcdefdryz.txt"; // Needed for username lookup

if (!file_exists($msgFolder)) {
    mkdir($msgFolder, 0777, true);
}

$chat = isset($_POST["chat"]) ? basename($_POST["chat"]) : "";
$fromId = isset($_POST["from"]) ? trim($_POST["from"]) : "";
$message = isset($_POST["message"]) ? trim($_POST["message"]) : "";

if ($chat && $fromId && $message) {
    // Find username from ID
    $username = "";
    if (file_exists($userFile)) {
        $lines = file($userFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode(":", $line);
            if (count($parts) >= 5 && trim($parts[4]) === $fromId) {
                $username = trim($parts[0]);
                break;
            }
        }
    }
    
    // Use ID if username not found
    if (empty($username)) $username = $fromId;
    
    // Write in new format
    $file = $msgFolder . $chat . ".txt";
    $content = $username . PHP_EOL . $message . PHP_EOL . PHP_EOL;
    file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    echo "Message sent";
} else {
    echo "Error";
}
?>