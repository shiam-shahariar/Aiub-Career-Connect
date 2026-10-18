<?php require __DIR__ . '/partials/header.php'; ?>
<?php
$id = (int) ($_GET['id'] ?? 1);
$opportunity = null;
foreach ($repository->getOpportunities() as $item) {
    if ((int) $item['id'] === $id) {
        $opportunity = $item;
        break;
    }
}
if (!$opportunity) {
    $opportunity = $repository->getOpportunities()[0];
}
?>
<section class="section container">
    <div class="single-opportunity">
        <div class="panel opportunity-panel">
            <span class="eyebrow"><?php echo htmlspecialchars($opportunity['type']); ?> • <?php echo htmlspecialchars($opportunity['location']); ?></span>
            <h1><?php echo htmlspecialchars($opportunity['title']); ?></h1>
            <div class="meta-row">
                <span><?php echo htmlspecialchars($opportunity['company']); ?></span>
                <span><?php echo htmlspecialchars($opportunity['department']); ?></span>
                <span>Deadline: <?php echo htmlspecialchars($opportunity['deadline']); ?></span>
            </div>

            <p><?php echo htmlspecialchars($opportunity['description']); ?></p>

            <div class="detail-list">
                <div><strong>Mode:</strong> <?php echo htmlspecialchars($opportunity['remote']); ?></div>
                <div><strong>Published:</strong> <?php echo htmlspecialchars($opportunity['published_at']); ?></div>
                <div><strong>Application:</strong> <?php echo htmlspecialchars($opportunity['application_url']); ?></div>
            </div>

            <div class="card-actions">
                <a href="<?php echo htmlspecialchars($opportunity['application_url']); ?>" class="btn btn--primary" target="_blank" rel="noreferrer">Apply now</a>
                <a href="index.php?page=opportunities" class="btn btn--secondary">Back to listings</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
