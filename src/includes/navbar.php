<nav class="navbar navbar-expand-md navbar-dark bg-primary d-md-none">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Main Menu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenuMobile"
            aria-controls="sidebarMenuMobile">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>


<aside class="bg-primary text-white p-4 d-none d-md-flex flex-column" style="width: 280px; min-height: 100vh;">
    <h1 class="mb-4 text-center fw-bold">Main Menu</h1>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="homepage.php" class="nav-link text-white px-3 py-2 fw-bold">Transaction</a>
        </li>
        <li class="nav-item mb-2">
            <a href="history.php" class="nav-link text-white px-3 py-2 fw-bold">History</a>
        </li>
    </ul>
    <a href="logout.php" class="btn btn-danger w-100 mt-auto">Logout</a>
</aside>


<div class="offcanvas offcanvas-start text-bg-primary d-md-none" tabindex="-1" id="sidebarMenuMobile">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold">Main Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <ul class="nav flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="homepage.php" class="nav-link text-white px-3 py-2">Transaction</a>
            </li>
            <li class="nav-item mb-2">
                <a href="history.php" class="nav-link text-white px-3 py-2">History</a>
            </li>
        </ul>
        <a href="logout.php" class="btn btn-danger w-100 mt-auto">Logout</a>
    </div>
</div>