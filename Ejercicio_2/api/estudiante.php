<?php
// Estudiantes API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}
$pdo = new PDO('sqlite:' . __DIR__ . '/../data/estudiantes.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS estudiantes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    email TEXT NOT NULL,
    ciudad TEXT NOT NULL
)");

function respond_error($message, $code) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

function validate($data) {
    if (!isset($data['nombre'], $data['email'], $data['ciudad'])) {
        respond_error('Datos incompletos', 400);
    }
    if (trim($data['nombre']) === '') {
        respond_error('Nombre vacío', 400);
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        respond_error('Email inválido', 400);
    }
    if (trim($data['ciudad']) === '') {
        respond_error('Ciudad vacía', 400);
    }
}

function fetchAll($pdo, $q = null) {
    if ($q) {
        $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE nombre LIKE :q OR email LIKE :q");
        $stmt->execute([':q' => "%" . $q . "%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM estudiantes");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

switch ($method) {
    case 'GET':
        $q = $_GET['q'] ?? null;
        echo json_encode(fetchAll($pdo, $q));
        http_response_code(200);
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        validate($data);
        $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre,email,ciudad) VALUES (:n,:e,:c)");
        $stmt->execute([':n' => $data['nombre'], ':e' => $data['email'], ':c' => $data['ciudad']]);
        $id = $pdo->lastInsertId();
        http_response_code(201);
        echo json_encode(['id' => $id, ...$data]);
        break;
    case 'DELETE':
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!isset($payload['id'])) {
            respond_error('ID requerido', 400);
        }
        $stmt = $pdo->prepare("DELETE FROM estudiantes WHERE id=:id");
        $stmt->execute([':id' => $payload['id']]);
        http_response_code(200);
        echo json_encode(['ok' => true]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        validate($data);
        $stmt = $pdo->prepare("UPDATE estudiantes SET nombre=:n,email=:e,ciudad=:c WHERE id=:id");
        $stmt->execute([':n' => $data['nombre'], ':e' => $data['email'], ':c' => $data['ciudad'], ':id' => $data['id']]);
        http_response_code(200);
        echo json_encode(['ok' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}
?>
