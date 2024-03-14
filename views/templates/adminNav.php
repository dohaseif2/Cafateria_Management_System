<?php
require_once "head.php";
session_start();

// Check if the user is logged in and his role is admin or not
if (!isset($_SESSION['logged_in'])) {
  header('Location: login.php');
} else if ($_SESSION['user']['role'] !== 'admin') {
  header('Location: userHome.php');
}

?>

<!-- Navbar start -->
<div class="container-fluid p-0 nav-bar" style="background-color: #362517; ">
  <nav class="navbar navbar-expand-lg navbar-dark py-1 fs-5 d-flex justify-content-evenly">
    <a class="navbar-brand px-lg-4 me-5">
      <h1 class="m-0 fs-1 display-4 text-white ">ITI Cafeteria</h1>
    </a>
    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse text-capitalize justify-content-between" id="navbarCollapse">
      <div class="navbar-nav px-5">
        <a href="/admin-home.php" class="nav-item nav-link mx-3 small">home</a>
        <a href="./adminProducts.php" class="nav-item nav-link mx-3 small">products</a>
        <a href="./adminUsers.php" class="nav-item nav-link mx-3 small">users</a>
        <a href="/admin-manual.php" class="nav-item nav-link mx-3 small">manual orders</a>
        <a href="/admin-checks.php" class="nav-item nav-link mx-3 small">checks</a>
      </div>
      <ul class="navbar-nav mx-4">
        <li class="nav-item d-flex align-items-center">
          <a class="nav-link small" aria-expanded="false">
            <img class="nav-img rounded-circle ms-2" src="<?= $_SESSION['user']['image'] ?>" width="60px" />
            <span class="nav-user"><?= $_SESSION['user']['name'] ?></span>
          </a>
          <span class="text-white fw-bold fs-3">|</span>
          <a href="../controllers/authenticateController.php" class="nav-link small">logout</a>
        </li>
      </ul>
    </div>
  </nav>
</div>
<!-- Navbar End -->