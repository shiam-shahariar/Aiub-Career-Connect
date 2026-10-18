<?php require __DIR__ . '/partials/header.php'; ?>
<section class="section container auth-page">
    <div class="auth-card auth-card--wide">
        <div class="auth-card__intro">
            <span class="eyebrow">Create account</span>
            <h1>Register as a Student or Alumni</h1>
            <p>Join the AIUB community and build a profile that helps you discover opportunities or support others.</p>
        </div>

        <form method="post" action="index.php" class="form-grid two-col">
            <input type="hidden" name="action" value="register">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label>
                Full name
                <input type="text" name="name" placeholder="Your full name" required>
            </label>

            <label>
                Account type
                <select name="role" required>
                    <option value="student">Student</option>
                    <option value="alumni">Alumni</option>
                </select>
            </label>

            <label>
                Email address
                <input type="email" name="email" placeholder="you@aiub.edu" required>
            </label>

            <label>
                Department
                <input type="text" name="department" placeholder="Computer Science" value="Computer Science">
            </label>

            <p class="demo-note demo-note--full">Demo-only registration flow for this prototype. Production data should be persisted to the MySQL database.</p>

            <label>
                Password
                <input type="password" name="password" placeholder="Create a password" required>
            </label>

            <label>
                Confirm password
                <input type="password" name="confirm_password" placeholder="Repeat password" required>
            </label>

            <div class="form-actions form-actions--wide">
                <button type="submit" class="btn btn--primary btn--full">Create account</button>
            </div>
        </form>

        <div class="auth-switch">
            <span>Already a member?</span>
            <a href="index.php?page=login">Go to login</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
