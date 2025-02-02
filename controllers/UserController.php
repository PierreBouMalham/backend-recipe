<?php
 
function registerUser($pdo, $data)
{
 $username = $data['username'] ?? '';
 $email = $data['email'] ?? '';
 $password = $data['password'] ?? '';
 
 if (!$username || !$email || !$password) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  return;
 }
 
 $passwordHash = password_hash($password, PASSWORD_BCRYPT);
 
 $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
 try {
  $stmt->execute([$username, $email, $passwordHash]);
  http_response_code(201);
  echo json_encode(["status" => "success", "message" => "User registered successfully"]);
 } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Error registering user: " . $e->getMessage()]);
 }
}
 
function loginUser($pdo, $data)
{
 $email = $data['email'] ?? '';
 $password = $data['password'] ?? '';
 
 if (!$email || !$password) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  return;
 }
 
 $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
 $stmt->execute([$email]);
 $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
 if (!$user || !password_verify($password, $user['password_hash'])) {
  http_response_code(401);
  echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
  return;
 }
 
 http_response_code(200);
 echo json_encode([
  "status" => "success",
  "message" => "Login successful",
  "data" => ["userId" => $user['id']]
 ]);
}
 
function updateUserPreferences($pdo, $data)
{
 $userId = $data['userId'] ?? null;
 $preferredCuisine = $data['preferred_cuisine'] ?? null;
 $preferredDifficulty = $data['preferred_difficulty'] ?? null;
 $dietaryRestriction = $data['dietary_restriction'] ?? null;
 
 if (!$userId) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "User ID is required"]);
  return;
 }
 
 // Use COALESCE to prevent null overwrites
 $stmt = $pdo->prepare("UPDATE users SET
        preferred_cuisine = COALESCE(?, preferred_cuisine),
        preferred_difficulty = COALESCE(?, preferred_difficulty),
        dietary_restriction = COALESCE(?, dietary_restriction)
        WHERE id = ?");
 try {
  $stmt->execute([$preferredCuisine, $preferredDifficulty, $dietaryRestriction, $userId]);
  http_response_code(200);
  echo json_encode(["status" => "success", "message" => "Preferences updated successfully"]);
 } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Error updating preferences: " . $e->getMessage()]);
 }
}
 
function getUserPreferences($pdo, $userId)
{
 $stmt = $pdo->prepare("SELECT
        COALESCE(preferred_cuisine, 'Any') AS preferred_cuisine,
        COALESCE(preferred_difficulty, 'Any') AS preferred_difficulty,
        COALESCE(dietary_restriction, 'Any') AS dietary_restriction
        FROM users WHERE id = ?");
 $stmt->execute([$userId]);
 $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
 
 if ($preferences) {
  http_response_code(200);
  echo json_encode(["status" => "success", "data" => $preferences]);
 } else {
  http_response_code(404);
  echo json_encode(["status" => "error", "message" => "User preferences not found"]);
 }
}