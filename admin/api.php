
<?php declare(strict_types=1);

require_once("auth.php");
require_once("database.php");

header('Content-Type: application/json');

function sendResponse(int $status, array $data): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function validateApiKey(): bool {
    $headers = getallheaders();
    $apiKey = $headers['X-API-Key'] ?? '';
    
    if (empty($apiKey)) {
        return false;
    }
    
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE api_key = ? AND is_admin = 1 LIMIT 1");
    $stmt->execute([$apiKey]);
    
    return (bool)$stmt->fetch();
}

if (!validateApiKey()) {
    sendResponse(401, ['error' => 'Unauthorized']);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'words') {
            $pdo = Database::getInstance();
            $stmt = $pdo->query("SELECT * FROM dictionary ORDER BY id DESC");
            sendResponse(200, ['data' => $stmt->fetchAll()]);
        }
        break;
        
    case 'POST':
        if ($action === 'add_word') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['ename']) || !isset($data['aname'])) {
                sendResponse(400, ['error' => 'Missing required fields']);
            }
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("INSERT INTO dictionary (ename, aname, translator) VALUES (?, ?, ?)");
            $stmt->execute([$data['ename'], $data['aname'], $data['translator'] ?? 'API']);
            
            sendResponse(201, ['message' => 'Word added successfully']);
        }
        break;
        
    default:
        sendResponse(405, ['error' => 'Method not allowed']);
}
