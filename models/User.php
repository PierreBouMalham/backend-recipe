<?php
 
class User
{
 private $id;
 private $username;
 private $email;
 private $passwordHash;
 
 public function __construct($id, $username, $email, $passwordHash)
 {
  $this->id = $id;
  $this->username = $username;
  $this->email = $email;
  $this->passwordHash = $passwordHash;
 }
 
 public static function findByEmail($pdo, $email)
 {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
  if ($user) {
   return new User($user['id'], $user['username'], $user['email'], $user['password_hash']);
  }
 
  return null;
 }
 
 public static function create($pdo, $username, $email, $password)
 {
  $passwordHash = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
 
  try {
   $stmt->execute([$username, $email, $passwordHash]);
   return new User($pdo->lastInsertId(), $username, $email, $passwordHash);
  } catch (PDOException $e) {
   throw new Exception("Error creating user: " . $e->getMessage());
  }
 }
 
 public function verifyPassword($password)
 {
  return password_verify($password, $this->passwordHash);
 }
 
 public function getId()
 {
  return $this->id;
 }
 
 public function getUsername()
 {
  return $this->username;
 }
 
 public function getEmail()
 {
  return $this->email;
 }
}