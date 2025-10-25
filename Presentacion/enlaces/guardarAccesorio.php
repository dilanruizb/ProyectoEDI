<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivo = 'accesorios.json';

    // Crear archivo si no existe
    if (!file_exists($archivo)) {
        file_put_contents($archivo, json_encode([]));
    }

    $accesorios = json_decode(file_get_contents($archivo), true);

    // Manejar imagen
    $directorio = "img/accesorios/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = basename($_FILES["imagen"]["name"]);
    $rutaDestino = $directorio . $nombreArchivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $imagen = $rutaDestino;
    } else {
        $imagen = "img/accesorios/default.png";
    }

    // Nuevo accesorio con tipo incluido
    $nuevo = [
        "marca" => $_POST['marca'],
        "modelo" => $_POST['nombre'],
        "desc" => $_POST['descripcion'],
        "precio" => floatval($_POST['precio']),
        "img" => $imagen,
        "tipo" => $_POST['tipo'] // 👈 agregado
    ];

    $accesorios[] = $nuevo;

    file_put_contents($archivo, json_encode($accesorios, JSON_PRETTY_PRINT));

    // Redirigir al catálogo
    header("Location: ../enlaces/catalogoAccesorios.html");
    exit();
}
?>
