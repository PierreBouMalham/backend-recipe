<?php

function getRecipes($pdo, $queryParams)
{
 try {
  $userId = $queryParams['userId'] ?? null;
  if (!$userId) {
   http_response_code(400);
   echo json_encode(["status" => "error", "message" => "User ID is required"]);
   return;
  }

  // Fetch user preferences
  $userStmt = $pdo->prepare("SELECT preferred_cuisine, preferred_difficulty, dietary_restriction FROM users WHERE id = ?");
  $userStmt->execute([$userId]);
  $userPreferences = $userStmt->fetch(PDO::FETCH_ASSOC);

  if (!$userPreferences) {
   http_response_code(404);
   echo json_encode(["status" => "error", "message" => "User preferences not found"]);
   return;
  }

  $query = "SELECT * FROM recipes WHERE 1=1";
  $params = [];

  // Filter by cuisine if it's not null or "Any"
  if (!empty($userPreferences['preferred_cuisine']) && $userPreferences['preferred_cuisine'] !== 'Any') {
   $query .= " AND cuisine = ?";
   $params[] = $userPreferences['preferred_cuisine'];
  }

  // Filter by difficulty if it's not null or "Any"
  if (!empty($userPreferences['preferred_difficulty']) && $userPreferences['preferred_difficulty'] !== 'Any') {
   $query .= " AND difficulty = ?";
   $params[] = $userPreferences['preferred_difficulty'];
  }

  // Filter by dietary restriction if it's not null or "Any"
  if (!empty($userPreferences['dietary_restriction']) && $userPreferences['dietary_restriction'] !== 'Any') {
   $query .= " AND dietary_restriction = ?";
   $params[] = $userPreferences['dietary_restriction'];
  }

  // Execute query with prepared parameters
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($recipes) {
   http_response_code(200);
   echo json_encode(["status" => "success", "data" => $recipes]);
  } else {
   http_response_code(404);
   echo json_encode(["status" => "error", "message" => "No recipes found"]);
  }
 } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Error fetching recipes: " . $e->getMessage()]);
 }
}
function getSurpriseRecipe($pdo, $queryParams)
{
 try {
  $userId = $queryParams['userId'] ?? null;
  if (!$userId) {
   http_response_code(400);
   echo json_encode(["status" => "error", "message" => "User ID is required"]);
   return;
  }

  // Fetch user preferences
  $userStmt = $pdo->prepare("SELECT preferred_cuisine, preferred_difficulty, dietary_restriction FROM users WHERE id = ?");
  $userStmt->execute([$userId]);
  $userPreferences = $userStmt->fetch(PDO::FETCH_ASSOC);

  if (!$userPreferences) {
   http_response_code(404);
   echo json_encode(["status" => "error", "message" => "User preferences not found"]);
   return;
  }

  $query = "SELECT * FROM recipes WHERE 1=1";
  $params = [];

  // Apply filters based on user preferences
  if (!empty($userPreferences['preferred_cuisine']) && $userPreferences['preferred_cuisine'] !== 'Any') {
   $query .= " AND cuisine = ?";
   $params[] = $userPreferences['preferred_cuisine'];
  }

  if (!empty($userPreferences['preferred_difficulty']) && $userPreferences['preferred_difficulty'] !== 'Any') {
   $query .= " AND difficulty = ?";
   $params[] = $userPreferences['preferred_difficulty'];
  }

  if (!empty($userPreferences['dietary_restriction']) && $userPreferences['dietary_restriction'] !== 'Any') {
   $query .= " AND dietary_restriction = ?";
   $params[] = $userPreferences['dietary_restriction'];
  }

  $query .= " ORDER BY RAND() LIMIT 1"; // Randomly pick one recipe

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($recipe) {
   http_response_code(200);
   echo json_encode(["status" => "success", "data" => $recipe]);
  } else {
   http_response_code(404);
   echo json_encode(["status" => "error", "message" => "No matching surprise recipe found"]);
  }
 } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Error fetching surprise recipe: " . $e->getMessage()]);
 }
}
