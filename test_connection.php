<?php

// Database configuration
const DB_HOST = 'localhost';
const DB_NAME = 'recipe_recommender';
const DB_USER = 'root';
const DB_PASSWORD = 'pierrebm123321';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test the connection
    // echo "Database connection successful!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
