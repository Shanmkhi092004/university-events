<?php

/**
 * Use this code snippet in your app.
 *
 * If you need more information about configurations or implementing the sample code, visit the AWS docs:
 * https://aws.amazon.com/developer/language/php/
 */

require __DIR__ . '/../vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

/**
 * This code expects that you have AWS credentials set up per:
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
 */

// Create a Secrets Manager Client
$client = new SecretsManagerClient([
    'profile' => 'default',
    'version' => '2017-10-17',
    'region' => 'us-east-2',
]);

$secret_name = 'rds!db-898434a4-fd76-4d5c-8641-c6a7d6075436';

try {
    $result = $client->getSecretValue([
        'SecretId' => $secret_name,
    ]);
} catch (AwsException $e) {
    // For a list of exceptions thrown, see
    // https://docs.aws.amazon.com/secretsmanager/latest/apireference/API_GetSecretValue.html
    throw $e;
}

// Decrypts secret using the associated KMS key.

// Decrypts secret using the associated KMS key.
$secret = $result['SecretString'];
$secretArr = json_decode($secret, true);

$host = $secretArr['host'] ?? $secretArr['hostname'] ?? 'localhost';
$user = $secretArr['username'] ?? '';
$pass = $secretArr['password'] ?? '';
$db   = $secretArr['dbname'] ?? '';
$port = isset($secretArr['port']) ? (int)$secretArr['port'] : 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

?>