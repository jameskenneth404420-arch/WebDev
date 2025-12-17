<?php
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
    $fullname = str_replace("'", "''", $_POST["fullname"]);
    $username = str_replace("'", "''", $_POST["username"]);
    $email = str_replace("'", "''", $_POST["email"]);
    $password = str_replace("'", "''", $_POST["password"]); // plain text
    $telephone = str_replace("'", "''", $_POST["telephone"]);

    // Validate telephone
    if (!preg_match('/^\d{11}$/', $telephone)) {
        echo "<script>alert('Telephone must be exactly 11 digits.'); window.history.back();</script>";
        exit();
    }

    // Check if username or email exists
    $checkSql = "SELECT * FROM Users WHERE EMAIL='$email' OR USERNAME='$username'";
    $checkResult = sqlsrv_query($conn, $checkSql);

    if ($checkResult === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($checkResult)) {
        echo "<script>alert('Email or Username is already taken.'); window.history.back();</script>";
        exit();
    }

    // Insert user
    $insertUserSql = "INSERT INTO Users (FULLNAME, USERNAME, PASSWORD, TELEPHONE, EMAIL)
                      VALUES ('$fullname', '$username', '$password', '$telephone', '$email')";
    $insertUserResult = sqlsrv_query($conn, $insertUserSql);

    if ($insertUserResult === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Get the USERID of the newly inserted user
    $getUserIdSql = "SELECT USERID FROM Users WHERE EMAIL='$email'";
    $userIdResult = sqlsrv_query($conn, $getUserIdSql);

    if ($userIdResult === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $userRow = sqlsrv_fetch_array($userIdResult, SQLSRV_FETCH_ASSOC);
    $userId = $userRow['USERID'];

    // Handle file upload
    if (isset($_FILES['validId']) && $_FILES['validId']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['validId']['tmp_name'];
        $fileName = $_FILES['validId']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg','jpeg','png'];

        if (!in_array($fileExt, $allowedExts)) {
            echo "<script>alert('Valid ID must be JPG or PNG.'); window.history.back();</script>";
            exit();
        }

        $newFileName = uniqid('id_', true) . '.' . $fileExt;
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Insert into IMAGE_ID table
            $dateUploaded = date('Y-m-d H:i:s');
            $insertImageSql = "INSERT INTO IMAGE_ID (IMAGE_NAME, FILEPATH, DATE_UPLOADED, USERID)
                               VALUES ('$fileName', '$destPath', '$dateUploaded', $userId)";
            $insertImageResult = sqlsrv_query($conn, $insertImageSql);

            if ($insertImageResult === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } else {
            echo "<script>alert('Failed to upload ID.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Valid ID is required.'); window.history.back();</script>";
        exit();
    }

    // Success
    echo "<script>alert('Registration successful!'); window.location='login.html';</script>";
    exit();
}
?>
