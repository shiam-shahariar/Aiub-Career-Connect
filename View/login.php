<?php require __DIR__ . '/partials/header.php'; ?>
<section class="section container auth-page">
    <div class="auth-card">
        <div class="auth-card__intro">
            <span class="eyebrow">Welcome back</span>
            <h1>Login to your AIUB account</h1>
            <p>Access your profile, dashboard, and opportunities using your registered AIUB email.</p>
        </div>

        <form method="post" action="index.php" class="form-grid">
            <input type="hidden" name="action" value="login">

            <label>
                Email address
                <input type="email" name="email" placeholder="you@aiub.edu" required>
            </label>

            <label>
                Password
                <input type="password" name="password" placeholder="Enter your password" required>
            </label>

            <button type="submit" class="btn btn--primary btn--full">Login</button>
        </form>

        <div class="auth-switch">
            <span>Need an account?</span>
            <a href="index.php?page=register">Register here</a>
        </div>

        <div class="demo-box">
            <strong>Demo credentials</strong>
            <p>These sample accounts are for this prototype only and use the bundled example data.</p>
            <ul>
                <li>Student: student@aiub.edu / student123</li>
                <li>Alumni: alumni@aiub.edu / alumni123</li>
                <li>Admin: admin@aiub.edu / admin123</li>
            </ul>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
