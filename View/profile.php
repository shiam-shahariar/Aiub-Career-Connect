<?php require __DIR__ . '/partials/header.php'; ?>
<?php if (!$currentUser): ?>
<section class="section container">
    <div class="message-panel">
        <h1>Please log in</h1>
        <p>You must sign in before editing your profile.</p>
        <a class="btn btn--primary" href="index.php?page=login">Login</a>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; return; ?>
<?php endif; ?>

<section class="section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Profile</span>
            <h1><?php echo htmlspecialchars($currentUser['name']); ?> profile</h1>
        </div>
        <a href="index.php?page=dashboard" class="btn btn--secondary">Back to dashboard</a>
    </div>

    <div class="panel form-panel">
        <p class="demo-note">This profile form is a prototype demonstration. Saving updates keeps the data in the current session only.</p>

        <form method="post" action="index.php" class="form-grid two-col">
            <input type="hidden" name="action" value="save_profile">

            <label>
                Full name
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($currentUser['name']); ?>">
            </label>
            <label>
                Role
                <input type="text" name="role" value="<?php echo htmlspecialchars($currentUser['role']); ?>" readonly>
            </label>
            <label>
                Email
                <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>">
            </label>
            <label>
                Department
                <input type="text" name="department" value="<?php echo htmlspecialchars($currentUser['department']); ?>">
            </label>
            <label>
                Graduation year
                <input type="text" name="graduation_year" value="2026">
            </label>
            <label>
                Current company
                <input type="text" name="company" value="N/A">
            </label>
            <label class="full-width">
                Skills
                <input type="text" name="skills" value="PHP, JavaScript, SQL, UI/UX">
            </label>
            <label class="full-width">
                Bio
                <textarea rows="5" name="bio">I am an AIUB student actively seeking opportunities in web development, digital products, and collaborative engineering work.</textarea>
            </label>
            <div class="form-actions form-actions--wide">
                <button type="submit" class="btn btn--primary">Save profile</button>
            </div>
        </form>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
