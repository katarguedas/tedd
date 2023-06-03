<?php

class TeDD_DB
{

  private mysqli $mysqli;

  public function __construct(string $hostname, string $username, string $password, string $database, string $port)
  {
    $this->mysqli = mysqli_connect($hostname, $username, $password, $database, $port);
  }

  public function __destruct()
  {
    mysqli_close($this->mysqli);
  }

  



}