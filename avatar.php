<?php
require_once __DIR__ . '/includes/users.php';
try {
    $username = input_text($_GET, 'user');
} catch (InvalidArgumentException) {
    http_response_code(400);
    exit;
}
$user = find_user($username);
$path = $user ? avatar_disk_path($user['hinhdaidien']) : null;
if ($path === null || !is_file($path)) {
    header('Location: assets/avatar.svg');
    exit;
}
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
header('Content-Type: ' . $mime);
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . filesize($path));
header('Cache-Control: no-cache');
readfile($path);
