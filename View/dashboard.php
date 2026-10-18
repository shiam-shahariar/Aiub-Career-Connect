<?php require __DIR__ . '/partials/header.php'; ?>
<?php $stats = $repository->getDemoStats(); ?>
<?php if (!$currentUser): ?>
<section class="section container">
    <div class="message-panel">
        <h1>Access required</h1>
        <p>Please log in to view your dashboard.</p>
        <a class="btn btn--primary" href="index.php?page=login">Login</a>
    </div>
</section>
<?php return; ?>
<?php endif; ?>

<section class="section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Dashboard</span>
            <h1>Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?></h1>
        </div>
        <a href="index.php?page=profile" class="btn btn--primary">Edit profile</a>
    </div>

    <p class="demo-note">Demo dashboard values are shown for this prototype and are not live analytics.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <span>Profile completion</span>
            <strong>82%</strong>
            <small>Demo status</small>
        </div>
        <div class="stat-card">
            <span>Saved roles</span>
            <strong><?php echo htmlspecialchars($stats['saved_roles']); ?></strong>
            <small>Internships and jobs</small>
        </div>
        <div class="stat-card">
            <span>Mentor contacts</span>
            <strong><?php echo htmlspecialchars($stats['mentor_contacts']); ?></strong>
            <small>Verified alumni</small>
        </div>
        <div class="stat-card">
            <span>Recent activity</span>
            <strong><?php echo htmlspecialchars($stats['recent_activity']); ?></strong>
            <small>Applications viewed</small>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <h3>Recommended opportunities</h3>
            <?php foreach (array_slice(array_values(array_filter($repository->getOpportunities(), static fn (array $opportunity): bool => $opportunity['status'] === 'published' && $opportunity['deadline'] >= date('Y-m-d'))), 0, 3) as $opportunity): ?>
                <div class="profile-item">
                    <div>
                        <strong><?php echo htmlspecialchars($opportunity['title']); ?></strong>
                        <small><?php echo htmlspecialchars($opportunity['company']) . ' • ' . htmlspecialchars($opportunity['type']); ?></small>
                    </div>
                    <a href="index.php?page=opportunity-detail&id=<?php echo $opportunity['id']; ?>">View</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
