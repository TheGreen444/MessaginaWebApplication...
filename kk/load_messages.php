<?php
$chat = isset($_GET["chat"]) ? basename($_GET["chat"]) : "";
$msgFolder = "../dxb/msg/";
$file = $msgFolder . $chat . ".txt";

if ($chat && file_exists($file)) {
    $content = file_get_contents($file);
    // Convert to HTML with line breaks
    echo nl2br(htmlspecialchars($content));
} else {
    echo "No messages yet. Start the conversation!";
}
?>