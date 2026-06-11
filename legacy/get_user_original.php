<?php

/**
 * Original buggy legacy code from the requirements document (for reference).
 */

$conn = mysqli_connect('localhost', 'root', '', 'test');
$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['name'];
    }
} else {
    echo 'Query failed';
}
mysqli_close($conn);
