<?php

/**
 * Corrected legacy MySQLi code.
 *
 * Issues fixed in the original:
 * 1. SQL injection via unsanitized $_GET['id'] — replaced with prepared statement.
 * 2. No connection error handling — mysqli_connect_error() checked before use.
 * 3. No input validation — ID must be a positive integer.
 * 4. No query error details — mysqli_error() used for debugging.
 * 5. Connection closed only after successful connection.
 */

#----------- 1. SQL injection via unsanitized $_GET['id'] — replaced with prepared statement ---------------------

declare(strict_types=1);        #PHP will enforce strict type checking.  PHP may silently convert "5abc" to 5

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'test';

#----------- 2. No connection error handling — mysqli_connect_error() checked before use ---------------------

#stop execution immediately if DB is down, return HTTP 500 (server error), show meaningful message

$conn = mysqli_connect($host, $username, $password, $database);

if ($conn === false) 
{
    http_response_code(500);
    echo 'Database connection failed: ' . mysqli_connect_error();
    exit;
}

#----------- 3. No input validation — ID must be a positive integer ---------------------
#ensures it is a valid integer, original was $id = $_GET['id'] which could get ?id=1 or 1=1 that is SQL Injection risk


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    echo 'Invalid user ID.';
    mysqli_close($conn);
    exit;
}

#----------- 4. No query error details — mysqli_error() used for debugging ---------------------

$stmt = mysqli_prepare($conn, 'SELECT name FROM users WHERE id = ? LIMIT 1');

if ($stmt === false) {
    http_response_code(500);
    echo 'Query preparation failed: ' . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);

if (! mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo 'Query execution failed: ' . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

$result = mysqli_stmt_get_result($stmt);

if ($result === false) {
    http_response_code(500);
    echo 'Failed to fetch result set.';
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo 'User not found.';
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
    }
}

#----------- 5. Connection closed only after successful connection. ---------------------

mysqli_free_result($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);


// 1. Security
    // SQL Injection fixed
    // XSS fixed
// 2. Stability
    // connection error handling
    // query failure handling
// 3. Data safety
    // input validation
    // strict typing
// 4. API correctness
    // proper HTTP codes (400, 404, 500)
// 5. Performance
    // SELECT only needed column
    // LIMIT 1