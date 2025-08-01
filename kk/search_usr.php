<?php
$file = __DIR__ . "/../dxb/up/abcdefdryz.txt";
$query = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : "";

if ($query === "") {
    exit;
}

if (!file_exists($file)) {
    echo "User database not found.";
    exit;
}

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$matches = [];

foreach ($lines as $line) {
    $parts = explode(":", $line);
    if (count($parts) >= 5) {
        $username = $parts[0];
        $userId = trim($parts[4]);
        if (stripos($username, $query) !== false) {
            $matches[] = [
                'username' => htmlspecialchars($username),
                'userId' => htmlspecialchars($userId)
            ];
        }
    }
}

if ($matches) {
    echo "<ul>";
    foreach ($matches as $match) {
        echo "<li>" . $match['username'] . 
             " <button onclick=\"createDM('" . $match['username'] . "', '" . $match['userId'] . "')\">Create DM</button></li>";
    }
    echo "</ul>";
} else {
    echo "No matches found.";
}
?>
