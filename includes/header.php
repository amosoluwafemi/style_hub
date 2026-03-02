<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Syncing session variables
$loggedInId = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? $_SESSION['id'] ?? null;
$loggedInName = $_SESSION['user_name'] ?? $_SESSION['customer_name'] ?? $_SESSION['full_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style Hub | Luxury Fashion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Fixed Typewriter Cursor Syntax */
        #logo-typewriter::after, 
        #typewriter-h1::after, 
        #typewriter-p::after {
            content: "|";
            margin-left: 2px;
            color: #c5a059;
            animation: blink 0.8s infinite;
            font-weight: 200;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        /* --- Luxury Header Styling --- */
        .navbar {
            backdrop-filter: blur(15px) saturate(150%);
            -webkit-backdrop-filter: blur(15px) saturate(150%);
            background: rgba(10, 25, 47, 0.85) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            letter-spacing: 2px;
            background: linear-gradient(to right, #fff, #c5a059);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Dropdown Fix: Remove the forced 'display: block' to let Bootstrap JS work */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease-in-out;
            background: rgba(15, 23, 42, 0.98) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            display: block; /* Necessary for the transition, but visibility handles the hide */
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            color: rgba(255,255,255,0.8) !important;
            transition: 0.2s;
        }

        .dropdown-item:hover {
            background: rgba(13, 110, 253, 0.2) !important;
            color: #fff !important;
            padding-left: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-800 fs-4" href="shop.php" id="logo-typewriter"></a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item">
                    <form action="shop.php" method="GET" class="me-lg-3 my-2 my-lg-0">
                        <div class="input-group input-group-sm search-container rounded-pill overflow-hidden" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                            <input type="text" name="search" class="form-control border-0 bg-transparent text-white px-3" placeholder="Search style..." style="width: 180px; box-shadow: none;">
                            <button class="btn text-white border-0" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </li>

                <li class="nav-item">
                    <a href="cart.php" class="nav-link position-relative px-2 mx-lg-2">
                        <i class="bi bi-bag h5"></i>
                        <?php if(!empty($_SESSION['cart'])): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.6rem;">
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if($loggedInId): ?>
                    <li class="nav-item dropdown ms-lg-2">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 dropdown-toggle d-flex align-items-center shadow-sm" 
                                type="button" 
                                id="userDropdown"
                                data-bs-toggle="dropdown" 
                                aria-expanded="false"
                                style="background: linear-gradient(45deg, #0d6efd, #0048af); border:none;">
                             <i class="bi bi-person-circle me-2"></i>
                             Hi, <?php echo htmlspecialchars(explode(' ', $loggedInName)[0]); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-3 p-2 rounded-4" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item rounded-3 py-2" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item rounded-3 py-2" href="orders.php"><i class="bi bi-box-seam me-2"></i>Orders</a></li>
                            <li><hr class="dropdown-divider bg-secondary opacity-25 mx-2"></li>
                            <li><a class="dropdown-item rounded-3 py-2 text-danger fw-600" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold mt-2 mt-lg-0 shadow-sm">Sign In</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Reveal Engine
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Typewriter Engine
    function typeWriter(text, element, speed, callback) {
        if (!element) return;
        let i = 0;
        element.innerHTML = "";
        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            } else if (callback) {
                callback();
            }
        }
        type();
    }

    // Typewriter Sequence
    typeWriter("STYLE HUB", document.getElementById("logo-typewriter"), 120, function() {
        const h1 = document.getElementById("typewriter-h1");
        if (h1) {
            setTimeout(() => {
                typeWriter("Our Collections", h1, 80, function() {
                    const p = document.getElementById("typewriter-p");
                    if (p) setTimeout(() => typeWriter("Curated premium fashion and skincare essentials.", p, 40), 300);
                });
            }, 500);
        }
    });
});
</script>