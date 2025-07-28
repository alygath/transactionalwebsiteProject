<?php require ("./connection.php");
?>
<html>
<head>
<meta charset="utf-8">
<title>Transactional website</title>
	

	<link rel="stylesheet" href="css/bootstrap-5.3.3-dist/css/bootstrap.css">
	<script src="css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js" defer></script>
	<link href="./css/fontawesome-free-6.6.0-web/css/fontawesome.css" rel="stylesheet" />
	 <link href="./css/fontawesome-free-6.6.0-web/css/all.css" rel="stylesheet" />
	<link href="./css/style.css" rel="stylesheet">
	
	
	
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><img src="images/logo.png" alt="Company Logo" style="width:auto; height:auto; max-height:100px;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="products.php">Products</a>
        </li>
      </ul>
  <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex flex-row align-items-center me-3">
  <li class="nav-item">
    <a href="cart.php" class="nav-link me-2"> <!-- Add margin-end to the first icon -->
      <i class="fa-solid fa-basket-shopping fs-3"></i>
    </a>
  </li>
  <li class="nav-item">
    <a href="account.php" class="nav-link">
      <i class="fa-solid fa-user fs-3"></i>
    </a>
  </li>
</ul>

    <form class="d-flex" role="search" action="search.php" method="GET">
    <input class="form-control me-2" type="search" name="query" placeholder="Search" aria-label="Search" required>
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>

    </div>
  </div>
</nav>