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
$email = '';

if ($username) {
    $sql = "
        SELECT TOP 1 u.EMAIL
        FROM Users u
        WHERE u.USERNAME = ?
    ";
    $stmt = sqlsrv_query($conn, $sql, [$username]);
    if ($stmt && sqlsrv_fetch($stmt)) {
        $email = sqlsrv_get_field($stmt, 0);
    }
}

$packages = [
  "A" => ["includes"=>"Surfing, Sightseeing","price"=>1500],
  "B" => ["includes"=>"Island Hopping, Snorkeling","price"=>2000],
  "C" => ["includes"=>"City Tour, Food Trip","price"=>1200]
];

// Destinations 
$destinations = [
    ["name"=>"Palawan","description"=>"Known for lagoons, limestone cliffs, and crystal-clear waters.","coords"=>[9.8349,118.7384],"image"=>"images/Palawan.jpg","videoIds"=>["5myfOaVzKw0"],"type"=>"Beach","location"=>"Palawan","price"=>5000,"availableDates"=>["2025-12-20","2025-12-25"],"packages"=>["A","B"]],
    ["name"=>"Boracay","description"=>"Famous for white-sand beaches, island hopping, and nightlife.","coords"=>[11.9674,121.9247],"image"=>"images/Boracay.png","videoIds"=>["b7LSsKsJGHY"],"type"=>"Beach","location"=>"Boracay","price"=>4500,"availableDates"=>["2025-12-21","2025-12-26"],"packages"=>["A","C"]],
    ["name"=>"Baguio","description"=>"The Summer Capital of the Philippines, known for cool weather.","coords"=>[16.4023,120.5960],"image"=>"images/Baguio.jpg","videoIds"=>["ir-Bt8HK4Eo"],"type"=>"Mountain","location"=>"Baguio","price"=>3000,"availableDates"=>["2025-12-22","2025-12-27"],"packages"=>["C"]],
    ["name"=>"Cebu","description"=>"Beautiful beaches, historical landmarks, and diving spots.","coords"=>[10.3157,123.8854],"image"=>"images/Cebu.jpg","videoIds"=>["ANhQ9soZ6OU"],"type"=>"Beach","location"=>"Cebu","price"=>4000,"availableDates"=>["2025-12-23","2025-12-28"],"packages"=>["A","B","C"]],
    ["name"=>"Siargao","description"=>"Surfing paradise and natural lagoons.","coords"=>[9.8486,126.0469],"image"=>"images/Siargao.jpg","videoIds"=>["n5f7pi0JDCo"],"type"=>"Surfing","location"=>"Siargao","price"=>5500,"availableDates"=>["2025-12-24","2025-12-29"],"packages"=>["A"]],
    ["name"=>"Davao","description"=>"Famous for Mount Apo and durian fruit.","coords"=>[7.1907,125.4553],"image"=>"images/Davao.jpg","videoIds"=>["ztTnBsuTFuE"],"type"=>"City","location"=>"Davao","price"=>3500,"availableDates"=>["2025-12-20","2025-12-25"],"packages"=>["B","C"]],
    ["name"=>"Bohol","description"=>"Chocolate Hills, beaches, and tarsiers.","coords"=>[9.9496,124.1586],"image"=>"images/Bohol.jpg","videoIds"=>["3Vp8tc9EZPg"],"type"=>"Beach","location"=>"Bohol","price"=>4000,"availableDates"=>["2025-12-21","2025-12-26"],"packages"=>["A","B"]],
    ["name"=>"Vigan","description"=>"Historical Spanish colonial town with cobblestone streets.","coords"=>[17.5745,120.3860],"image"=>"images/Vigan.jpg","videoIds"=>["z9t47d82Qgo"],"type"=>"City","location"=>"Vigan","price"=>3000,"availableDates"=>["2025-12-22","2025-12-27"],"packages"=>["C"]],
    ["name"=>"Tagaytay","description"=>"Cool climate with views of Taal Volcano.","coords"=>[14.0942,120.9887],"image"=>"images/Tagaytay.jpg","videoIds"=>["abc123"],"type"=>"Mountain","location"=>"Tagaytay","price"=>2500,"availableDates"=>["2025-12-23","2025-12-28"],"packages"=>["B"]],
    ["name"=>"El Nido","description"=>"Stunning islands and lagoons, popular for snorkeling.","coords"=>[11.2027,119.4059],"image"=>"images/El Nido.jpg","videoIds"=>["def456"],"type"=>"Beach","location"=>"Palawan","price"=>5500,"availableDates"=>["2025-12-24","2025-12-29"],"packages"=>["A"]],
    ["name"=>"Coron","description"=>"Diving paradise with shipwrecks and clear lakes.","coords"=>[12.0024,120.2020],"image"=>"images/Coron.jpg","videoIds"=>["ghi789"],"type"=>"Beach","location"=>"Palawan","price"=>5000,"availableDates"=>["2025-12-25","2025-12-30"],"packages"=>["A","B"]],
    ["name"=>"Camiguin","description"=>"Small volcanic island with hot springs and waterfalls.","coords"=>[9.2561,124.7289],"image"=>"images/Camiguin.jpg","videoIds"=>["jkl012"],"type"=>"Island","location"=>"Camiguin","price"=>3000,"availableDates"=>["2025-12-20","2025-12-25"],"packages"=>["C"]],
    ["name"=>"Leyte","description"=>"Historical sites and beach resorts.","coords"=>[11.2210,124.9954],"image"=>"images/Leyte.jpg","videoIds"=>["mno345"],"type"=>"Beach","location"=>"Leyte","price"=>3500,"availableDates"=>["2025-12-21","2025-12-26"],"packages"=>["B"]],
    ["name"=>"Samar","description"=>"Lush forests and hidden caves for adventure seekers.","coords"=>[11.8050,125.0094],"image"=>"images/Samar.jpg","videoIds"=>["pqr678"],"type"=>"Adventure","location"=>"Samar","price"=>3000,"availableDates"=>["2025-12-22","2025-12-27"],"packages"=>["C"]],
    ["name"=>"Palaui","description"=>"Remote island with pristine beaches and hiking trails.","coords"=>[18.4050,122.0815],"image"=>"images/Palaui.jpg","videoIds"=>["stu901"],"type"=>"Island","location"=>"Cagayan","price"=>4000,"availableDates"=>["2025-12-23","2025-12-28"],"packages"=>["A"]],
    ["name"=>"Pagudpud","description"=>"Northern beach destination with scenic cliffs.","coords"=>[18.5653,120.7754],"image"=>"images/Pagudpud.jpg","videoIds"=>["vwx234"],"type"=>"Beach","location"=>"Ilocos Norte","price"=>3500,"availableDates"=>["2025-12-24","2025-12-29"],"packages"=>["A","B"]],
    ["name"=>"Siquijor","description"=>"Island with mystical traditions and beaches.","coords"=>[9.1624,123.4678],"image"=>"images/Siquijor.jpg","videoIds"=>["yz1234"],"type"=>"Island","location"=>"Siquijor","price"=>3000,"availableDates"=>["2025-12-20","2025-12-25"],"packages"=>["C"]],
    ["name"=>"Banaue","description"=>"Famous for the Rice Terraces and indigenous culture.","coords"=>[16.9270,121.1452],"image"=>"images/Banaue.jpg","videoIds"=>["abc567"],"type"=>"Mountain","location"=>"Banaue","price"=>2500,"availableDates"=>["2025-12-21","2025-12-26"],"packages"=>["B"]],
    ["name"=>"Sagada","description"=>"Known for hanging coffins and caves.","coords"=>[17.0992,120.8410],"image"=>"images/Sagada.jpg","videoIds"=>["def890"],"type"=>"Mountain","location"=>"Sagada","price"=>2500,"availableDates"=>["2025-12-22","2025-12-27"],"packages"=>["C"]],
    ["name"=>"Puerto Princesa","description"=>"Underground river and eco-tourism destination.","coords"=>[9.7420,118.7310],"image"=>"images/Puerto Princesa.jpg","videoIds"=>["ghi123"],"type"=>"Beach","location"=>"Palawan","price"=>4500,"availableDates"=>["2025-12-23","2025-12-28"],"packages"=>["A","B"]],
    ["name"=>"Dumaguete","description"=>"University town with nearby diving spots.","coords"=>[9.3077,123.3057],"image"=>"images/Dumaguete.jpg","videoIds"=>["jkl456"],"type"=>"City","location"=>"Negros Oriental","price"=>3000,"availableDates"=>["2025-12-24","2025-12-29"],"packages"=>["C"]],
    ["name"=>"Oslob","description"=>"Famous for whale shark watching.","coords"=>[9.4603,123.3303],"image"=>"images/Oslob.jpg","videoIds"=>["mno789"],"type"=>"Beach","location"=>"Cebu","price"=>3500,"availableDates"=>["2025-12-20","2025-12-25"],"packages"=>["B"]],
    ["name"=>"Cagayan de Oro","description"=>"Adventure capital with white water rafting.","coords"=>[8.4542,124.6319],"image"=>"images/Cagayan de Oro.jpg","videoIds"=>["pqr012"],"type"=>"Adventure","location"=>"Misamis Oriental","price"=>4000,"availableDates"=>["2025-12-21","2025-12-26"],"packages"=>["A","C"]],
    ["name"=>"Subic","description"=>"Former US naval base, now beaches and adventure parks.","coords"=>[14.7944,120.2716],"image"=>"images/Subic.jpg","videoIds"=>["stu345"],"type"=>"City","location"=>"Zambales","price"=>3500,"availableDates"=>["2025-12-22","2025-12-27"],"packages"=>["B"]],
    ["name"=>"Iloilo","description"=>"Heritage city with festivals and seafood.","coords"=>[10.7202,122.5621],"image"=>"images/Iloilo.jpg","videoIds"=>["vwx567"],"type"=>"City","location"=>"Iloilo","price"=>3500,"availableDates"=>["2025-12-23","2025-12-28"],"packages"=>["C"]]
];


$categories = [
  "Beach"=>"<i class='bi bi-sunset'></i>",
  "City"=>"<i class='bi bi-building'></i>",
  "Mountain"=>"<i class='bi bi-geo-alt'></i>",
  "Surfing"=>"<i class='bi bi-wind'></i>",
  "Island"=>"<i class='bi bi-droplet'></i>",
  "Adventure"=>"<i class='bi bi-flag'></i>"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ExplorePH Featured Destinations</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
body { font-family: 'Poppins', sans-serif; padding-top: 70px; }
.navbar-brand { font-weight:600; }
#searchInput { width:300px; }
.map-categories-row { display:flex; gap:20px; flex-wrap:wrap; }
.categories-col { flex:0 0 300px; }
.categories-list { max-height:500px; overflow-y:auto; margin-top:10px; }
.category-icon { cursor:pointer; padding:15px; border:1px solid #ddd; border-radius:8px; transition:0.3s; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8f9fa; margin-bottom:10px; }
.category-icon:hover { background:#e2e6ea; }
#mainMap { flex:1; height:500px; }
.carousel-item img { width:100%; height:400px; object-fit:cover; }
.travel-tip-card img { max-height:150px; object-fit:cover; }
.hero-banner { height:100vh; background:url('images/hero.jpg') center/cover no-repeat; position:relative; display:flex; align-items:center; justify-content:center; color:#fff; text-shadow:1px 1px 5px #000; }
.hero-overlay { position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)); z-index:1; }
.hero-content { position:relative; z-index:2; text-align:center; }
.hero-content h1 { font-size:3rem; font-weight:600; margin-bottom:20px; }
.hero-content .btn-hero { font-size:1.2rem; padding:10px 25px; background:#ff5722; border:none; color:#fff; border-radius:5px; transition:0.3s; }
.hero-content .btn-hero:hover { background:#e64a19; }
footer { background:#343a40; color:#fff; padding:30px 0; margin-top:50px; }
footer a { color:#fff; text-decoration:none; }
footer a:hover { text-decoration:underline; }
.divider {
  height: 3px;
  background: linear-gradient(to right, #000000ff, #000000ff);
  width: 80%;
  margin: 50px auto;
  border-radius: 2px;
}



</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="#">ExplorePH</a>

    <div class="d-flex align-items-center">
      <div class="input-group me-2">
        <input type="text" id="searchInput" class="form-control" placeholder="Search destinations or packages...">
        <button class="btn btn-outline-light" id="searchBtn"><i class="bi bi-search"></i></button>
        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">Filter</button>
        <ul class="dropdown-menu p-3" style="min-width:250px;">
          <label>Price (Max)</label>
          <input type="range" class="form-range" min="0" max="6000" id="filterPrice" oninput="updatePriceLabel(this.value)">
          <small id="priceLabel">₱3000</small>
          <label class="mt-2">Type</label>
          <select class="form-select" id="filterType" onchange="applyFilters()">
            <option value="">All Types</option>
            <?php foreach(array_keys($categories) as $c){ echo "<option value='$c'>$c</option>"; } ?>
          </select>
          <button class="btn btn-primary mt-2 w-100" onclick="applyFilters()">Apply</button>
        </ul>
      </div>

      <a href="book.php" class="btn btn-outline-light me-2" style="white-space:nowrap;">Book Now</a>

      <div class="dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">Account</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="bookings.php">Past Booking</a></li>
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Search Results</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="searchResults">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Hero Banner -->
<div class="hero-banner">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1>Explore the Philippines</h1>
    <a href="book.php" class="btn btn-hero">Book Now</a>
  </div>
</div>

<!-- Featured Places -->
<div class="container mt-5">
  <h3 class="mb-3">Featured Places</h3>
  <div class="row g-3">
    <?php
      $featuredNames = ["Palawan", "Boracay", "Baguio"];
      foreach($destinations as $dest){
        if(in_array($dest['name'], $featuredNames)):
    ?>
      <div class="col-md-4">
        <div class="card h-100">
          <img src="<?php echo $dest['image']; ?>" class="card-img-top featured-img" alt="<?php echo $dest['name']; ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo $dest['name']; ?></h5>
            <p class="card-text"><?php echo $dest['description']; ?></p>
            <p class="card-text"><small class="text-muted">Price: ₱<?php echo $dest['price']; ?></small></p>
            <a href="book.php?dest=<?php echo urlencode($dest['name']); ?>" class="btn btn-primary btn-sm">Book this trip</a>
          </div>
        </div>
      </div>
    <?php
        endif;
      }
    ?>
  </div>
</div>

<div class="divider"></div>


<style>
.featured-img {
  height: 250px; 
  object-fit: cover;
}
</style>


<div class="container-fluid mt-5">
  <div class="row map-categories-row" style="max-width:1140px; margin:0 auto;">

    <!-- Categories Column -->
    <div class="categories-col col-md-4">
      <h3>Explore by Category</h3>
      <div class="categories-list" style="max-height:400px; overflow-y:auto;">
        <?php foreach($categories as $name=>$icon): ?>
          <div class="category-icon text-center mb-3" onclick="filterByCategory('<?php echo $name; ?>')">
            <?php echo $icon; ?><br>
            <small><?php echo $name; ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    
    


    <div class="col-md-8" id="map-container">
      <h3 class="text-center">Explore Destinations on Map</h3>
      <div id="mainMap" style="height:400px;"></div>
    </div>
  </div>
</div>

<div class="divider"></div>


<div class="container mt-4">
  <h3>Know more about your Destinations</h3>
  <div class="mb-3">
    <select class="form-select" id="destinationsDropdown" onchange="goToDestination(this.value)">
      <option value="">-- Select a Destination --</option>
      <option value="palawan.html">Palawan</option>
      <option value="boracay.html">Boracay</option>
      <option value="baguio.html">Baguio</option>
      <option value="cebu.html">Cebu</option>
      <option value="siargao.html">Siargao</option>
      <option value="davao.html">Davao</option>
      <option value="bohol.html">Bohol</option>
      <option value="vigan.html">Vigan</option>
      <option value="tagaytay.html">Tagaytay</option>
      <option value="el-nido.html">El Nido</option>
      <option value="coron.html">Coron</option>
      <option value="camiguin.html">Camiguin</option>
      <option value="leyte.html">Leyte</option>
      <option value="samar.html">Samar</option>
      <option value="palaui.html">Palaui</option>
      <option value="pagudpud.html">Pagudpud</option>
      <option value="siquijor.html">Siquijor</option>
      <option value="banaue.html">Banaue</option>
      <option value="sagada.html">Sagada</option>
      <option value="puerto-princesa.html">Puerto Princesa</option>
      <option value="dumaguete.html">Dumaguete</option>
      <option value="oslob.html">Oslob</option>
      <option value="cagayan-de-oro.html">Cagayan de Oro</option>
      <option value="subic.html">Subic</option>
      <option value="iloilo.html">Iloilo</option>
    </select>
  </div>
</div>


<!-- Carousel -->
<div class="container mt-5">
  <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php foreach($destinations as $index => $dest): ?>
        <div class="carousel-item <?php echo $index===0?'active':''; ?>">
          <img src="<?php echo $dest['image']; ?>" class="d-block" alt="<?php echo $dest['name']; ?>">
          <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
            <h5><?php echo $dest['name']; ?></h5>
            <p><?php echo $dest['description']; ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</div>

<div class="divider"></div>

<!-- Travel Tips Section -->
<div class="container mt-5">
  <h3 class="text-center mb-3">Travel Tips & Guides</h3>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card travel-tip-card h-100">
        <img src="images/tip1.jpg" class="card-img-top" alt="Tip 1">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Packing Essentials</h5>
          <p class="card-text">Learn what to bring for a perfect Philippines trip.</p>
          <a href="Essentials.html" class="btn btn-black mt-auto">Read More</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card travel-tip-card h-100">
        <img src="images/tip2.jpg" class="card-img-top" alt="Tip 2">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Local Cuisine</h5>
          <p class="card-text">Must-try Filipino foods during your travels.</p>
          <a href="#" class="btn btn-black mt-auto">Read More</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card travel-tip-card h-100">
        <img src="images/tip3.jpg" class="card-img-top" alt="Tip 3">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Island Hopping</h5>
          <p class="card-text">Best routes and tips for island tours.</p>
          <a href="#" class="btn btn-black mt-auto">Read More</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="searchResultsLabel">Search Results</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="searchResultsBody">
        <!-- Results will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="mt-5">
  <div class="container text-center">
    <p>&copy; <?php echo date('Y'); ?> ExplorePH. All rights reserved.</p>
    <p>
      <a href="About.html">About</a> | 
      <a href="Contact.html">Contact</a> | 
      <a href="Privacy.html">Privacy Policy</a>
    </p>
  </div>
</footer>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const destinations = <?php echo json_encode($destinations); ?>;

// Initialize map
let map = L.map('mainMap').setView([12.8797,121.7740],5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
  attribution:'&copy; OpenStreetMap contributors'
}).addTo(map);

// Markers
let markers = destinations.map(d=>{
  const m = L.marker(d.coords).addTo(map)
    .bindPopup(`<b>${d.name}</b><br>${d.description}<br>
    <a href="book.php?dest=${encodeURIComponent(d.name)}">Book this trip</a>`);
  return {marker:m, dest:d};
});

// Update price label dynamically
function updatePriceLabel(val){
  document.getElementById('priceLabel').textContent = `₱${val}`;
  // No automatic applyFilters to prevent modal while typing
}

// Filters
function applyFilters(showModal = false){
  const query = document.getElementById('searchInput').value.toLowerCase();
  const price = parseInt(document.getElementById('filterPrice').value) || 0;
  const type = document.getElementById('filterType').value;

  let resultsHTML = '';
  let foundAny = false;

  markers.forEach(item=>{
    const d = item.dest;
    let match = true;

    if(query && !d.name.toLowerCase().includes(query) && !d.description.toLowerCase().includes(query)) match=false;
    if(price && d.price > price) match=false;
    if(type && d.type !== type) match=false;

    if(match){
      item.marker.addTo(map);
      foundAny = true;

      resultsHTML += `
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-4">
              <img src="${d.image}" class="img-fluid rounded-start" alt="${d.name}">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">${d.name}</h5>
                <p class="card-text">${d.description}</p>
                <p class="card-text"><small class="text-muted">Price: ₱${d.price}</small></p>
                <a href="book.php?dest=${encodeURIComponent(d.name)}" class="btn btn-primary btn-sm">Book this trip</a>
              </div>
            </div>
          </div>
        </div>
      `;
    } else {
      map.removeLayer(item.marker);
    }
  });

  const visible = markers.filter(m => map.hasLayer(m.marker));
  if(visible.length === 1){ 
    map.setView(visible[0].dest.coords,9); 
    visible[0].marker.openPopup(); 
  } else {
    map.setView([12.8797,121.7740],5);
  }

  // Show modal only if explicitly requested
  if(showModal){
    document.getElementById('searchResultsBody').innerHTML = resultsHTML || '<p>No results found.</p>';
    const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
    modal.show();
  }
}

function filterByCategory(cat){
  document.getElementById('filterType').value = cat;
  applyFilters();
}

// Event listeners for search
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');

// Click search button
searchBtn.addEventListener('click', () => applyFilters(true));

// Press Enter
searchInput.addEventListener('keydown', (e) => {
  if(e.key === 'Enter'){
    e.preventDefault();
    applyFilters(true);
  }
});

// Initialize price label
updatePriceLabel(document.getElementById('filterPrice').value);
function goToDestination(url) {
  if(url) {
    window.location.href = url;
  }
}

</script>

</body>
</html>
