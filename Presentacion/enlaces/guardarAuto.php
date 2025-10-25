<?php
$tipo = $_POST['tipo'] ?? '0km';

// Seleccionar archivo según tipo
if ($tipo === 'usado') {
  $JSON_FILE = __DIR__ . "/autosUsados.json";
} else {
  $JSON_FILE = __DIR__ . "/autos.json";
}

$IMG_DIR = __DIR__ . "/img/";

// Crear carpeta img si no existe
if (!is_dir($IMG_DIR)) {
  @mkdir($IMG_DIR, 0775, true);
}

// Validar request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit("Método no permitido");
}

$marca  = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$desc   = trim($_POST['desc'] ?? '');
$precio = $_POST['precio'] ?? '';

if ($marca === '' || $modelo === '' || $desc === '' || $precio === '' || !isset($_FILES['imagen'])) {
  http_response_code(400);
  exit("Faltan datos obligatorios.");
}

$precioNum = is_numeric($precio) ? (int)$precio : 0;

// Subida de imagen
$imgTmp  = $_FILES['imagen']['tmp_name'];
$imgName = basename($_FILES['imagen']['name']);
$ext     = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

$permitidas = ['jpg','jpeg','png','webp','gif'];
if (!in_array($ext, $permitidas)) {
  http_response_code(400);
  exit("Formato de imagen no permitido.");
}

$destName = uniqid("auto_", true) . "." . $ext;
$destPath = $IMG_DIR . $destName;

if (!move_uploaded_file($imgTmp, $destPath)) {
  http_response_code(500);
  exit("No se pudo guardar la imagen.");
}

// Cargar JSON
$autos = [];
if (file_exists($JSON_FILE)) {
  $json = file_get_contents($JSON_FILE);
  $autos = json_decode($json, true);
  if (!is_array($autos)) $autos = [];
}

// Nuevo auto
$nuevo = [
  "marca"  => $marca,
  "modelo" => $modelo,
  "desc"   => $desc,
  "precio" => $precioNum,
  "img"    => "img/" . $destName
];

$autos[] = $nuevo;
$ok = file_put_contents($JSON_FILE, json_encode($autos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

if ($ok === false) {
  http_response_code(500);
  exit("No se pudo actualizar el archivo JSON.");
}

// Redirigir según tipo
if ($tipo === 'usado') {
  header("Location: catalogoUsados.html");
} else {
  header("Location: catalago0km.html");
}
exit;
