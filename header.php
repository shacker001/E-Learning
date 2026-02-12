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

        /* Sidebar (hidden by default) */
        #sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: -240px;
            /* hidden */
            top: 0;
            background: #212529;
            color: #fff;
            padding-top: 20px;
            overflow-y: auto;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        #sidebar.open {
            left: 0;
            /* slide in */
        }

        /* Overlay */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }

        #overlay.show {
            display: block;
        }

        /* Content */
        #content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
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

        #sidebar a i {
            margin-right: 8px;
        }

        /* Dark mode */
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

        /* Profile box */
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

    <!-- Toggle button always visible -->
    <button id="toggleSidebar" class="btn btn-secondary m-2">
        <i class="bi bi-list"></i>
    </button>
    <div id="overlay"></div>

    <?php if (isset($_SESSION['user'])): ?>
        <div id="sidebar">
            <!-- Profile -->
            <div class="profile-box">
                <img src="/online_training/uploads/profile_pics/<?php echo $_SESSION['user']['profile_pic'] ?? 'default_profile.jpg'; ?>" alt="Profile">
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
            <a href="/online_training/pages/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/online_training/pages/sessions.php"><i class="bi bi-camera-video"></i> Sessions</a>
            <a href="/online_training/pages/materials.php"><i class="bi bi-cloud-arrow-up"></i> Materials</a>
            <a href="/online_training/pages/attendance.php"><i class="bi bi-clipboard-check"></i> Attendance</a>
            <a href="/online_training/pages/performance.php"><i class="bi bi-bar-chart-line"></i> Performance</a>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/online_training/pages/live_preview.php"><i class="bi bi-camera-video"></i> Live Preview</a>
                <a href="/online_training/pages/share_screen.php"><i class="bi bi-display"></i> Share Screen</a>
            <?php endif; ?>

            <hr class="text-secondary">

            <!-- Dark Mode -->
            <a href="#" id="toggleTheme"><i class="bi bi-moon-stars"></i> Dark Mode</a>

            <!-- Logout -->
            <a href="/online_training/auth/logout.php" class="mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    <?php endif; ?>

    <div id="content">

        <script>
            /* Dark mode toggle */
            const themeBtn = document.getElementById("toggleTheme");
            const themeIcon = themeBtn.querySelector("i");

            function updateIcon() {
                if (document.body.classList.contains("dark")) {
                    themeIcon.classList.replace("bi-moon-stars", "bi-brightness-high");
                    themeBtn.innerHTML = '<i class="bi bi-brightness-high"></i> Light Mode';
                } else {
                    themeIcon.classList.replace("bi-brightness-high", "bi-moon-stars");
                    themeBtn.innerHTML = '<i class="bi bi-moon-stars"></i> Dark Mode';
                }
            }
            if (localStorage.getItem("theme") === "dark") {
                document.body.classList.add("dark");
            }
            updateIcon();
            themeBtn.addEventListener("click", function(e) {
                e.preventDefault();
                document.body.classList.toggle("dark");
                localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
                updateIcon();
            });

            /* Sidebar toggle */
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const toggleBtn = document.getElementById('toggleSidebar');

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        </script>