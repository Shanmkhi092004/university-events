<!-- <?php
// Database connection configuration

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'university_events';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf-8
$conn->set_charset("utf8");

?> -->

<?php
require __DIR__ . '/../vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient; 
use Aws\Exception\AwsException;

// AWS Secrets Manager configuration
$client = new SecretsManagerClient([
    'region' => 'your-aws-region', // e.g., us-east-1
    'version' => 'latest'
]);

$secretName = 'your-secret-name';

try {
    $result = $client->getSecretValue([
        'SecretId' => $secretName,
    ]);
    $secret = json_decode($result['SecretString'], true);
    $host = $secret['host'];
    $db   = $secret['dbname'];
    $user = $secret['username'];
    $pass = $secret['password'];
    $port = isset($secret['port']) ? $secret['port'] : 3306;
} catch (AwsException $e) {
    die('Unable to retrieve secret: ' . $e->getMessage());
}

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
