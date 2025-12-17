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

$username = $_SESSION['username'] ?? '';

// Fetch destinations
$destinations = [];
$sqlDest = "SELECT DESTINATION_ID, NAME, PRICE FROM Destinations";
$stmtDest = sqlsrv_query($conn, $sqlDest);
while ($row = sqlsrv_fetch_array($stmtDest, SQLSRV_FETCH_ASSOC)) {
    $destinations[] = $row;
}

// Fetch hotels
$hotels = [];
$sqlHotels = "SELECT HOTEL_ID, NAME, DESTINATION_ID, PRICE FROM Hotels";
$stmtHotels = sqlsrv_query($conn, $sqlHotels);
while ($row = sqlsrv_fetch_array($stmtHotels, SQLSRV_FETCH_ASSOC)) {
    $hotels[] = $row;
}

$successMsg = '';
$receiptData = [];

$packageDetails = [
    'None' => ['label' => 'No Package', 'price' => 0],
    'A' => ['label' => 'Package A – Surfing & Sightseeing', 'price' => 1000],
    'B' => ['label' => 'Package B – City Tour & Food Trip', 'price' => 800],
    'C' => ['label' => 'Package C – Adventure Activities', 'price' => 1200],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $destination_id = $_POST['destination'];
    $hotel_id = $_POST['hotel'] ?: null;
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $people = (int)$_POST['people'];
    $package = $_POST['package'];

    $today = date('Y-m-d');

    if ($checkin < $today || $checkout < $checkin) {
        $successMsg = "Invalid date selection.";
    } else {

        // Destination price
        $basePrice = 0;
        $destinationName = '';
        foreach ($destinations as $d) {
            if ($d['DESTINATION_ID'] == $destination_id) {
                $basePrice = $d['PRICE'];
                $destinationName = $d['NAME'];
                break;
            }
        }

        // Package price
        $packagePrice = $packageDetails[$package]['price'];

        // FINAL PRICE
        $finalPrice = ($basePrice + $packagePrice) * $people;

        $refNumber = 'REF-' . strtoupper(uniqid());

        $sqlInsert = "INSERT INTO Bookings
        (USERNAME, DESTINATION_ID, HOTEL_ID, CHECKIN_DATE, CHECKOUT_DATE, TRIP_PRICE, REF_NUMBER, PEOPLE, PACKAGE)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $username,
            $destination_id,
            $hotel_id,
            $checkin,
            $checkout,
            $finalPrice,
            $refNumber,
            $people,
            $package
        ];

        $stmt = sqlsrv_query($conn, $sqlInsert, $params);

        if ($stmt) {
            $successMsg = "Booking successful!";
            $receiptData = [
                'ref' => $refNumber,
                'destination' => $destinationName,
                'people' => $people,
                'package' => $packageDetails[$package]['label'],
                'price' => $finalPrice
            ];
        } else {
            $successMsg = print_r(sqlsrv_errors(), true);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Book Your Trip | ExplorePH</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { padding-top:70px; font-family:Poppins,sans-serif; }
.form-section { max-width:600px; margin:50px auto; background:#f8f9fa; padding:30px; border-radius:10px; }
.hero-banner { height:300px; background:url('images/hero.jpg') center/cover no-repeat; color:#fff; display:flex; align-items:center; justify-content:center; }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="homepage.php">ExplorePH</a>
  </div>
</nav>

<div class="hero-banner">
  <h1>Book Your Trip</h1>
</div>

<div class="form-section">
<?php if ($successMsg): ?>
<div class="alert alert-info"><?= $successMsg ?></div>
<?php endif; ?>

<form method="POST">

<label class="form-label">Destination</label>
<select class="form-select mb-3" name="destination" onchange="loadHotels()" required>
<?php foreach ($destinations as $d): ?>
<option value="<?= $d['DESTINATION_ID'] ?>">
<?= $d['NAME'] ?> (₱<?= number_format($d['PRICE'],2) ?>)
</option>
<?php endforeach; ?>
</select>

<label class="form-label">Hotel</label>
<select class="form-select mb-3" name="hotel" id="hotel">
<option value="">No Hotel</option>
</select>

<label class="form-label">People</label>
<input type="number" name="people" class="form-control mb-3" min="1" value="1" required>

<label class="form-label">Package</label>
<select class="form-select mb-3" name="package">
<?php foreach ($packageDetails as $key => $p): ?>
<option value="<?= $key ?>">
<?= $p['label'] ?> (+₱<?= number_format($p['price']) ?> / person)
</option>
<?php endforeach; ?>
</select>

<label class="form-label">Check-in</label>
<input type="date" name="checkin" class="form-control mb-3" min="<?= date('Y-m-d') ?>" required>

<label class="form-label">Check-out</label>
<input type="date" name="checkout" class="form-control mb-3" min="<?= date('Y-m-d') ?>" required>

<button class="btn btn-primary w-100">Confirm Booking</button>
<a href="homepage.php" class="btn btn-secondary w-100 mt-2">Back</a>

</form>
</div>

<?php if ($receiptData): ?>
<div class="modal show d-block">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header"><h5>Receipt</h5></div>
<div class="modal-body">
<p><strong>Reference:</strong> <?= $receiptData['ref'] ?></p>
<p><strong>Destination:</strong> <?= $receiptData['destination'] ?></p>
<p><strong>People:</strong> <?= $receiptData['people'] ?></p>
<p><strong>Package:</strong> <?= $receiptData['package'] ?></p>
<p><strong>Total:</strong> ₱<?= number_format($receiptData['price'],2) ?></p>
</div>
<div class="modal-footer">
<a href="homepage.php" class="btn btn-success">Done</a>
</div>
</div>
</div>
</div>
<?php endif; ?>

<script>
const hotels = <?= json_encode($hotels) ?>;
function loadHotels() {
    const dest = document.querySelector('[name="destination"]').value;
    const h = document.getElementById('hotel');
    h.innerHTML = '<option value="">No Hotel</option>';
    hotels.forEach(x=>{
        if(x.DESTINATION_ID == dest){
            h.innerHTML += `<option value="${x.HOTEL_ID}">${x.NAME} - ₱${x.PRICE}</option>`;
        }
    });
}
</script>

</body>
</html>
