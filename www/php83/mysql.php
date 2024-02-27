<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Docker: MySQL Test</title>
  <style>
    body{
      font-family: sans-serif;
    }
  </style>
</head>
<body>
  <h1>MySQL Test Page</h1>
  <p>
    <em>Note: This will fail to connect unless MariaDB has been uncommented in docker-compose.yml.</em>
  </p>
  <h4>Attempting to connect to MySQL from PHP. Please wait...</h4>

  <?php
    $host = 'mysql';
    $username = 'root';
    $password = 'insecurepassword';
    $conn = new mysqli($host, $username, $password);

    if ($conn->connect_error) {
      die('<p>Connection failed: ' . $conn->connect_error . '</p>');
    } 
    echo '<p>Connected to MySQL successfully!</p>';
  ?>
</body>
</html>