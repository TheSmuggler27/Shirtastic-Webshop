<?php
class DB {
  private static $pdo = null;

  public static function getConnection() {
    if (self::$pdo === null) {
      $host = 'localhost';
      $dbname = 'shirtastic_db';
      $username = 'root';
      $password = '';

      try {
        self::$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
      }
    }

    return self::$pdo;
  }
}
?>
