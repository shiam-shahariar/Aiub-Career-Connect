<?php require __DIR__ . '/partials/header.php'; ?>
<?php $stats = $repository->getDemoStats(); ?>
<?php if (!$currentUser || $currentUser['role'] !== 'admin'): ?>
<section class="section container">
    <div class="message-panel">
        <h1>Admin access only</h1>
        <p>Only administrators can manage AIUB career posts.</p>
        <a href="index.php?page=login" class="btn btn--primary">Login as admin</a>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; return; ?>
<?php endif; ?>

<section class="section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Admin control</span>
            <h1>Opportunity management</h1>
        </div>
    </div>

    <p class="demo-note">Sample admin metrics are shown for this demonstration and should be replaced with live database values in production.</p>

    <div class="stats-grid">
        <div class="stat-card"><span>Drafts</span><strong><?php echo htmlspecialchars($stats['draft_opportunities']); ?></strong><small>Waiting review</small></div>
        <div class="stat-card"><span>Published</span><strong><?php echo htmlspecialchars($stats['published_opportunities']); ?></strong><small>Live opportunities</small></div>
        <div class="stat-card"><span>Pending alumni</span><strong><?php echo htmlspecialchars(count(array_filter($repository->getAlumni(), static fn (array $alumni): bool => !$alumni['verified']))); ?></strong><small>Profiles to review</small></div>
        <div class="stat-card"><span>Students</span><strong><?php echo htmlspecialchars($stats['active_students']); ?></strong><small>Active accounts</small></div>
    </div>

    <div class="panel">
        <h3>Recent opportunities</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repository->getOpportunities() as $opportunity): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($opportunity['title']); ?></td>
                            <td><?php echo htmlspecialchars($opportunity['type']); ?></td>
                            <td><?php echo htmlspecialchars($opportunity['company']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($opportunity['status'])); ?></td>
                            <td><a href="index.php?page=opportunity-detail&id=<?php echo $opportunity['id']; ?>">Review</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
