<?php require __DIR__ . '/partials/header.php'; ?>
<?php
$id = (int) ($_GET['id'] ?? 1);
$opportunity = null;
foreach ($repository->getOpportunities() as $item) {
    if ((int) $item['id'] === $id
        && $item['status'] === 'published'
        && $item['deadline'] >= date('Y-m-d')) {
        $opportunity = $item;
        break;
    }
}
?>
<section class="section container">
    <?php if (!$opportunity): ?>
        <div class="message-panel">
            <h1>Opportunity unavailable</h1>
            <p>This listing is closed, expired, or no longer available.</p>
            <a class="btn btn--primary" href="index.php?page=opportunities">Browse active opportunities</a>
        </div>
    <?php else: ?>
    <div class="single-opportunity">
        <div class="panel opportunity-panel">
            <span class="eyebrow"><?php echo htmlspecialchars($opportunity['type']); ?> • <?php echo htmlspecialchars($opportunity['location']); ?></span>
            <h1><?php echo htmlspecialchars($opportunity['title']); ?></h1>
            <div class="meta-row">
                <span><?php if (!empty($opportunity['company_id'])): ?><a href="index.php?page=company-detail&amp;id=<?php echo (int) $opportunity['company_id']; ?>"><?php echo htmlspecialchars($opportunity['company_name'] ?? $opportunity['company']); ?></a><?php else: ?><?php echo htmlspecialchars($opportunity['company']); ?><?php endif; ?></span>
                <span><?php echo htmlspecialchars($opportunity['department']); ?></span>
                <span>Deadline: <?php echo htmlspecialchars($opportunity['deadline']); ?></span>
            </div>

            <p><?php echo htmlspecialchars($opportunity['description']); ?></p>

            <div class="detail-list">
                <div><strong>Mode:</strong> <?php echo htmlspecialchars($opportunity['remote']); ?></div>
                <div><strong>Published:</strong> <?php echo htmlspecialchars($opportunity['published_at']); ?></div>
            </div>

            <div class="card-actions">
                <a href="<?php echo htmlspecialchars($opportunity['application_url']); ?>" class="btn btn--primary" target="_blank" rel="noreferrer">Apply now</a>
                <a href="index.php?page=opportunities" class="btn btn--secondary">Back to listings</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
