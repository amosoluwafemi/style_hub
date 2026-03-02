<?php
session_start();
if (isset($_SESSION['customer_id'])) {
    header("Location: shop.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Style Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { height: 100%; margin: 0; font-family: 'Poppins', sans-serif; overflow: hidden; }

        /* Moving Gradient */
        .bg-gradient-animation {
            background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #1a1a1a);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
            height: 100vh; width: 100%; position: fixed; z-index: -2;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Image Overlay */
        .image-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover; background-position: center;
            transition: opacity 2s ease-in-out; z-index: -1; opacity: 0.2;
        }

        .welcome-container {
            height: 100vh; display: flex; justify-content: center;
            align-items: center; text-align: center; color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border-radius: 30px; padding: 60px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .brand-name {
            font-size: clamp(3rem, 10vw, 5rem);
            font-weight: 900; letter-spacing: 12px;
            margin-bottom: 15px; text-transform: uppercase;
            background: -webkit-linear-gradient(white, #383838);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-custom {
            padding: 15px 40px; border-radius: 50px;
            font-weight: 700; transition: 0.4s;
            text-transform: uppercase; letter-spacing: 2px;
        }

        .btn-login { background: white; color: black; border: none; }
        .btn-register { background: transparent; color: white; border: 2px solid white; }

        .btn-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(255,255,255,0.4);
        }
    </style>
</head>
<body>

    <div class="bg-gradient-animation"></div>
    <div id="image-slider" class="image-overlay"></div>

    <div class="welcome-container">
        <div class="glass-card">
            <h1 class="brand-name">STYLE HUB</h1>
            <p class="lead mb-5 opacity-75">PREMIUM FASHION & LUXURY SKINCARE</p>
            
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="login.php" class="btn btn-custom btn-login btn-lg">Login</a>
                <a href="register.php" class="btn btn-custom btn-register btn-lg">Register</a>
            </div>
            
            <div class="mt-5">
                <a href="shop.php" class="text-white-50 text-decoration-none small">
                    Explore the collection as Guest <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Update these with high-quality fashion/skincare images
        const images = [
            'assets/images/bg1.jpeg', 
            'assets/images/bg2.jpeg',
            'assets/images/bg3.jpeg'
        ];

        let currentIndex = 0;
        const slider = document.getElementById('image-slider');

        function changeImage() {
            slider.style.opacity = 0;
            setTimeout(() => {
                slider.style.backgroundImage = `url('${images[currentIndex]}')`;
                slider.style.opacity = 0.2;
                currentIndex = (currentIndex + 1) % images.length;
            }, 2000);
        }

        setInterval(changeImage, 7000);
        changeImage();
    </script>
</body>
</html>