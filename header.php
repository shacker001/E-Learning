<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Online Training</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            overflow-x: hidden;
        }

        /* Sidebar */
        #sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #212529;
            color: #fff;
            padding-top: 20px;
        }

        #sidebar a {
            color: #ddd;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
        }

        #sidebar a:hover {
            background: #343a40;
            color: #fff;
        }

        /* Content area */
        #content {
            margin-left: 240px;
            padding: 20px;
        }

        /* Dark mode styles */
        body.dark {
            background-color: #121212;
            color: #e4e4e4;
        }

        body.dark #sidebar {
            background: #111;
        }

        body.dark #sidebar a {
            color: #ccc;
        }

        body.dark #sidebar a:hover {
            background: #222;
            color: #fff;
        }

        body.dark #content {
            background-color: #121212;
        }

        body.dark .card {
            background-color: #1e1e1e;
            color: #ddd;
        }

        body.dark .list-group-item {
            background-color: #1e1e1e;
            color: #ddd;
            border-color: #333;
        }

        /* Profile picture placeholder (we will activate this next) */
        .profile-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-box img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .profile-box small {
            color: #bbb;
        }
    </style>
</head>

<body>

    <?php if (isset($_SESSION['user'])): ?>
        <div id="sidebar">

            <!-- Profile Section (we will activate this next) -->
            <div class="profile-box">
                <img src="/online_training/uploads/profile_pics/<?php echo $_SESSION['user']['profile_pic'] ?? 'default_profile.jpg'; ?>"
                    alt="Profile">

                <div class="mt-2">
                    <strong><?php echo htmlspecialchars($_SESSION['user']['name']); ?></strong><br>
                    <small><?php echo ucfirst($_SESSION['user']['role']); ?></small>
                </div>
                <a href="/online_training/pages/profile.php">
                    <i class="bi bi-person-circle"></i> Profile
                </a>

            </div>

            <hr class="text-secondary">

            <!-- Navigation -->
            <a href="/online_training/pages/dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <a href="/online_training/pages/sessions.php">
                <i class="bi bi-camera-video"></i> Sessions
            </a>

            <a href="/online_training/pages/materials.php">
                <i class="bi bi-cloud-arrow-up"></i> Materials
            </a>

            <a href="/online_training/pages/attendance.php">
                <i class="bi bi-clipboard-check"></i> Attendance
            </a>

            <a href="/online_training/pages/performance.php">
                <i class="bi bi-bar-chart-line"></i> Performance
            </a>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/online_training/pages/live_preview.php">
                    <i class="bi bi-camera-video"></i> Live Preview
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/online_training/pages/share_screen.php">
                    <i class="bi bi-display"></i> Share Screen
                </a>
            <?php endif; ?>


            <hr class="text-secondary">

            <!-- Dark Mode Toggle -->
            <a href="#" id="toggleTheme">
                <i class="bi bi-moon-stars"></i> Dark Mode
            </a>

            <!-- Logout -->
            <a href="/online_training/auth/logout.php" class="mt-3">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    <?php endif; ?>

    <div id="content">

        <script>
            const toggleBtn = document.getElementById("toggleTheme");
            const icon = toggleBtn.querySelector("i");

            function updateIcon() {
                if (document.body.classList.contains("dark")) {
                    icon.classList.replace("bi-moon-stars", "bi-brightness-high");
                    toggleBtn.innerHTML = '<i class="bi bi-brightness-high"></i> Light Mode';
                } else {
                    icon.classList.replace("bi-brightness-high", "bi-moon-stars");
                    toggleBtn.innerHTML = '<i class="bi bi-moon-stars"></i> Dark Mode';
                }
            }

            // Load saved theme
            if (localStorage.getItem("theme") === "dark") {
                document.body.classList.add("dark");
            }
            updateIcon();

            toggleBtn.addEventListener("click", function(e) {
                e.preventDefault();
                document.body.classList.toggle("dark");

                // Save preference
                localStorage.setItem("theme",
                    document.body.classList.contains("dark") ? "dark" : "light"
                );

                updateIcon();
            });
        </script>