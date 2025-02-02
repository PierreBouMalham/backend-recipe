<?php
 
// Add CORS headers
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Allow specific headers
 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
 
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
 
// Handle preflight OPTIONS request (used by browsers for CORS)
if ($requestMethod === 'OPTIONS') {
  http_response_code(200);
  exit();
}
 
// Parse query parameters
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '';
$queryParams = [];
if (!empty($parsedUrl['query'])) {
  parse_str($parsedUrl['query'], $queryParams);
}
 
// Routing logic
if ($path === '/api/recipes' && $requestMethod === 'GET') {
  getRecipes($pdo, $queryParams);
} elseif ($path === '/api/surprise' && $requestMethod === 'GET') {
  getSurpriseRecipe($pdo, $queryParams);
} elseif ($path === '/api/userPreferences' && $requestMethod === 'GET') {
  $userId = $queryParams['userId'] ?? null;
  if (!$userId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
    return;
  }
  getUserPreferences($pdo, $userId);
} elseif ($path === '/api/updatePreferences' && $requestMethod === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  updateUserPreferences($pdo, $input);
} elseif ($path === '/api/register' && $requestMethod === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  registerUser($pdo, $input);
} elseif ($path === '/api/login' && $requestMethod === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  loginUser($pdo, $input);
} else {
  http_response_code(404);
  echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
}
 
 