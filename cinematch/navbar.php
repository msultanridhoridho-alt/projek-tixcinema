<?php include_once 'database.php'; ?>
<nav class="navbar navbar-expand-lg sticky-top navbar-dark px-4" style="background-color: #000000;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-danger fs-3" href="index.php">CINEMATCH</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#rekomendasi-section">Rekomendasi Film</a>
                </li>

                <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="admin.php">
                            <i class="fas fa-user-shield me-1"></i> Panel Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="me-3 text-white-50">
                        <i class="fas fa-user-circle text-danger me-1"></i> 
                        <?= ucfirst($_SESSION['user']); ?> (<?= ucfirst($_SESSION['role']); ?>)
                    </span>
                    <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-danger px-3 fw-bold">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>