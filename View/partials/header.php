<?php
$currentUser = $currentUser ?? null;
$flashMessage = $_SESSION['flashMessage'] ?? null;
$flashSuccess = $_SESSION['flashSuccess'] ?? true;
$flashOverlay = $_SESSION['flashOverlay'] ?? false;
unset($_SESSION['flashMessage'], $_SESSION['flashSuccess'], $_SESSION['flashOverlay']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIUB Career Connect</title>
    <link rel="icon" type="image/png" href="Assets/American International University Bangladesh Seal.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Design/Style.css">
</head>
<body class="device-preview--normal">
    <div class="page-shell">
        <header class="site-header">
            <div class="container header-row">
                <a href="index.php?page=home" class="brand" aria-label="AIUB Career Connect home">
                    <img src="Assets/American International University Bangladesh Seal.png" alt="AIUB seal logo">
                    <div>
                        <span class="brand__title">AIUB Career Connect</span>
                        <small>Career Network &amp; Placement Portal</small>
                    </div>
                </a>

                <button class="nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>

                <nav class="main-nav" aria-label="Main navigation">
                    <a href="index.php?page=home">Home</a>
                    <a href="index.php?page=opportunities">Jobs &amp; Internships</a>
                    <a href="index.php?page=alumni">Alumni Directory</a>
                    <a href="index.php?page=companies">Companies</a>
                    <a href="index.php?page=dashboard">Dashboard</a>
                    <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                        <a href="index.php?page=admin">Admin</a>
                    <?php endif; ?>
                </nav>

                <div class="header-actions">
                    <?php if ($currentUser): ?>
                        <div class="user-badge">
                            <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <form method="post" action="index.php">
                                <input type="hidden" name="action" value="logout">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <button type="submit" class="btn btn--ghost">Logout</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="auth-actions">
                            <a href="index.php?page=login" class="btn btn--ghost">Login</a>
                            <a href="index.php?page=register" class="btn btn--primary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <?php if ($flashMessage): ?>
            <div class="flash-message <?php echo $flashSuccess ? 'flash-success' : 'flash-error'; ?><?php echo $flashOverlay ? ' flash-message--overlay' : ''; ?>" role="status">
                <?php echo htmlspecialchars($flashMessage); ?>
            </div>
        <?php endif; ?>

        <main class="page-content">
