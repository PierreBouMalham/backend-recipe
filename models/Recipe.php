<?php
 
class Recipe
{
 private $id;
 private $title;
 private $cuisine;
 private $ingredients;
 private $instructions;
 private $imageUrl;
 private $difficulty;
 private $prepTime;
 private $cookTime;
 
 public function __construct($id, $title, $cuisine, $ingredients, $instructions, $imageUrl, $difficulty, $prepTime, $cookTime)
 {
  $this->id = $id;
  $this->title = $title;
  $this->cuisine = $cuisine;
  $this->ingredients = $ingredients;
  $this->instructions = $instructions;
  $this->imageUrl = $imageUrl;
  $this->difficulty = $difficulty;
  $this->prepTime = $prepTime;
  $this->cookTime = $cookTime;
 }
 
 public static function findAll($pdo)
 {
  $stmt = $pdo->query("SELECT * FROM recipes");
  $recipes = [];
 
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $recipes[] = new Recipe(
    $row['id'],
    $row['title'],
    $row['cuisine'],
    $row['ingredients'],
    $row['instructions'],
    $row['image_url'],
    $row['difficulty'],
    $row['prep_time'],
    $row['cook_time']
   );
  }
 
  return $recipes;
 }
 
 public static function findRandom($pdo)
 {
  $stmt = $pdo->query("SELECT * FROM recipes ORDER BY RAND() LIMIT 1");
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
  if ($row) {
   return new Recipe(
    $row['id'],
    $row['title'],
    $row['cuisine'],
    $row['ingredients'],
    $row['instructions'],
    $row['image_url'],
    $row['difficulty'],
    $row['prep_time'],
    $row['cook_time']
   );
  }
 
  return null;
 }
 
 public function toArray()
 {
  return [
   'id' => $this->id,
   'title' => $this->title,
   'cuisine' => $this->cuisine,
   'ingredients' => $this->ingredients,
   'instructions' => $this->instructions,
   'image_url' => $this->imageUrl,
   'difficulty' => $this->difficulty,
   'prep_time' => $this->prepTime,
   'cook_time' => $this->cookTime,
  ];
 }
}
 
 