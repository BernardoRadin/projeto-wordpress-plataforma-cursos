<?php
require_once('../../../wp-load.php');

if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}


$curso_id   = isset($_GET['curso_id']) ? intval($_GET['curso_id']) : 0;
$usuario_id = get_current_user_id();
$video      = isset($_GET['video']) ? basename($_GET['video']) : '';

if (!$curso_id || !$usuario_id || !$video) {
    http_response_code(400);
    exit('Parâmetros inválidos.');
}

if (!user_has_purchased_course($usuario_id, $curso_id)) {
    http_response_code(403);
    exit('Acesso negado.');
}

$caminho = WP_CONTENT_DIR . '/uploads/cursos/' . $curso_id . '/' . $video;

if (!file_exists($caminho)) {
    http_response_code(404);
    exit('Vídeo não encontrado.');
}

$size = filesize($caminho);
$length = $size;
$start = 0;
$end = $size - 1;
$headers = getallheaders();

if (isset($headers['Range'])) {
    preg_match('/bytes=(\d+)-(\d*)/', $headers['Range'], $matches);
    $start = intval($matches[1]);
    if (isset($matches[2]) && $matches[2] !== '') $end = intval($matches[2]);
    $length = $end - $start + 1;
    header("HTTP/1.1 206 Partial Content");
}

header("Content-Type: video/mp4");
header("Accept-Ranges: bytes");
header("Content-Length: $length");
header("Content-Range: bytes $start-$end/$size");

$fp = fopen($caminho, 'rb');
fseek($fp, $start);
$buffer = 1024 * 8;
while (!feof($fp) && ($pos = ftell($fp)) <= $end) {
    if ($pos + $buffer > $end) {
        $buffer = $end - $pos + 1;
    }
    echo fread($fp, $buffer);
    flush();
}
fclose($fp);
exit;

