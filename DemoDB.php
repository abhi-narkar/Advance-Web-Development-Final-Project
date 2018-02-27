<?php
class DemoDB {

  /** Give a connection to the quizz DB, in UTF-8 */
  public static function getConnection() {
    // DB configuration
    $db = "agile_tool";
    $dsn = "mysql:dbname=$db;host=localhost;charset=utf8";
    $user = "root";
    $password = "";
    // Get a DB connection with PDO library
    $bdd = new PDO($dsn, $user, $password);
    // Set communication in utf-8
    $bdd->exec("SET character_set_client = 'utf8'");
    return $bdd;
  }
}
