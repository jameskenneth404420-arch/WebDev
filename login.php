<?php
session_start();

$servername = "MSI\\MSSQL2022";
$connectionOptions = [
    "Database" => "Final Project",
    "TrustServerCertificate" => true,
    "Authentication" => "ActiveDirectoryIntegrated"
];

$conn = sqlsrv_connect($servername, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"]; // plain text as requested

    $sql = "SELECT USERNAME FROM Users WHERE USERNAME = ? AND PASSWORD = ?";
    $params = [$username, $password];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($stmt)) {
        // ✅ SAVE SESSION
        $_SESSION['username'] = $username;

        // ✅ REDIRECT PROPERLY
        header("Location: homepage.php");
        exit;
    } else {
        echo "<script>
            alert('Incorrect username or password');
            window.location='login.html';
        </script>";
    }
}
?>
