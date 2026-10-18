<?php require __DIR__ . '/partials/header.php'; ?>
<section class="section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Alumni network</span>
            <h1>AIUB Alumni Directory</h1>
        </div>
    </div>

    <div class="directory-grid">
        <?php foreach ($repository->getAlumni() as $alumnus): ?>
            <article class="directory-card">
                <div class="directory-card__top">
                    <div>
                        <h3><?php echo htmlspecialchars($alumnus['name']); ?></h3>
                        <p><?php echo htmlspecialchars($alumnus['job_title']); ?> at <?php echo htmlspecialchars($alumnus['company']); ?></p>
                    </div>
                    <?php if ($alumnus['verified']): ?>
                        <span class="badge badge--verified">Verified</span>
                    <?php else: ?>
                        <span class="badge badge--pending">Pending</span>
                    <?php endif; ?>
                </div>
                <ul>
                    <li><?php echo htmlspecialchars($alumnus['department']); ?> • <?php echo htmlspecialchars($alumnus['graduation_year']); ?></li>
                    <li>Industry: <?php echo htmlspecialchars($alumnus['industry']); ?></li>
                    <li>Skills: <?php echo htmlspecialchars(implode(', ', $alumnus['skills'])); ?></li>
                </ul>
                <div class="card-actions">
                    <?php if ($alumnus['email_contact']): ?>
                        <a href="mailto:<?php echo rawurlencode($alumnus['email']); ?>?subject=<?php echo rawurlencode('AIUB Career Connect inquiry'); ?>" class="btn btn--secondary">Email</a>
                    <?php else: ?>
                        <button class="btn btn--ghost" type="button" disabled>Email unavailable</button>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
