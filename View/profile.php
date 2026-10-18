<?php
$hasAlumniProfile = $currentUser && $currentUser['role'] === 'alumni'
    && $repository->hasAlumniProfile((int) $currentUser['id']);
$profilePrivacy = $hasAlumniProfile
    ? $repository->getProfilePrivacy((int) $currentUser['id'])
    : null;
require __DIR__ . '/partials/header.php';
?>
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

<?php
$demoProfile = array_replace([
    'full_name' => $currentUser['name'],
    'email' => $currentUser['email'],
    'department' => $currentUser['department'],
    'graduation_year' => '2026',
    'company' => 'N/A',
    'skills' => 'PHP, JavaScript, SQL, UI/UX',
    'bio' => 'I am an AIUB student actively seeking opportunities in web development, digital products, and collaborative engineering work.',
], $_SESSION['demo_profiles'][(int) $currentUser['id']] ?? []);
?>
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
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label>
                Full name
                <input type="text" name="full_name" maxlength="150" value="<?php echo htmlspecialchars($demoProfile['full_name']); ?>">
            </label>
            <label>
                Role
                <input type="text" name="role" value="<?php echo htmlspecialchars($currentUser['role']); ?>" readonly>
            </label>
            <label>
                Email
                <input type="email" name="email" maxlength="180" value="<?php echo htmlspecialchars($demoProfile['email']); ?>">
            </label>
            <label>
                Department
                <input type="text" name="department" maxlength="100" value="<?php echo htmlspecialchars($demoProfile['department']); ?>">
            </label>
            <label>
                Graduation year
                <input type="text" name="graduation_year" inputmode="numeric" maxlength="4" value="<?php echo htmlspecialchars($demoProfile['graduation_year']); ?>">
            </label>
            <label>
                Current company
                <input type="text" name="company" maxlength="150" value="<?php echo htmlspecialchars($demoProfile['company']); ?>">
            </label>
            <label class="full-width">
                Skills
                <input type="text" name="skills" maxlength="2000" value="<?php echo htmlspecialchars($demoProfile['skills']); ?>">
            </label>
            <label class="full-width">
                Bio
                <textarea rows="5" name="bio" maxlength="5000"><?php echo htmlspecialchars($demoProfile['bio']); ?></textarea>
            </label>
            <div class="form-actions form-actions--wide">
                <button type="submit" class="btn btn--primary">Save profile</button>
            </div>
        </form>
    </div>

    <?php if ($currentUser['role'] === 'alumni' && $hasAlumniProfile): ?>
        <div class="panel privacy-panel">
            <h2>Directory and contact privacy</h2>
            <p>Only verified alumni profiles can appear in search results. Email contact is shown only when you explicitly allow it.</p>
            <form method="post" action="index.php" class="form-grid two-col">
                <input type="hidden" name="action" value="save_profile_privacy">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label>Profile visibility
                    <select name="visibility" required>
                        <option value="public"<?php echo $profilePrivacy['visibility'] === 'public' ? ' selected' : ''; ?>>Public directory</option>
                        <option value="logged_in"<?php echo $profilePrivacy['visibility'] === 'logged_in' ? ' selected' : ''; ?>>Logged-in AIUB users</option>
                        <option value="private"<?php echo $profilePrivacy['visibility'] === 'private' ? ' selected' : ''; ?>>Private (not listed)</option>
                    </select>
                </label>
                <label>Email contact visibility
                    <select name="email_visibility" required>
                        <option value="disabled"<?php echo $profilePrivacy['email_contact_visibility'] === 'disabled' ? ' selected' : ''; ?>>Disable email contact</option>
                        <option value="public"<?php echo $profilePrivacy['email_contact_visibility'] === 'public' ? ' selected' : ''; ?>>Show to everyone</option>
                        <option value="logged_in"<?php echo $profilePrivacy['email_contact_visibility'] === 'logged_in' ? ' selected' : ''; ?>>Show only to logged-in AIUB users</option>
                    </select>
                </label>
                <div class="form-actions form-actions--wide">
                    <button type="submit" class="btn btn--primary">Save privacy settings</button>
                </div>
            </form>
        </div>
    <?php elseif ($currentUser['role'] === 'alumni'): ?>
        <p class="demo-note">Privacy controls are available once an alumni profile is connected to a saved profile record.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
