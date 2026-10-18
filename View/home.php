<?php require __DIR__ . '/partials/header.php'; ?>
<?php $stats = $repository->getDemoStats(); ?>
<section class="hero hero--home">
    <div class="container hero__grid">
        <div class="hero__content">
            <span class="eyebrow">Career network for AIUB</span>
            <h1>Build your future with AIUB Career Connect.</h1>
            <p>Discover internships, explore alumni networks, and manage a stronger path from campus to career through one trusted AIUB platform.</p>
            <div class="action-row">
                <a href="index.php?page=opportunities" class="btn btn--primary">Browse opportunities</a>
                <a href="index.php?page=register" class="btn btn--secondary">Create profile</a>
            </div>
            <ul class="hero-stats">
                <li><strong><?php echo htmlspecialchars($stats['student_profiles']); ?></strong><span>Student profiles</span></li>
                <li><strong><?php echo htmlspecialchars($stats['verified_alumni']); ?>+</strong><span>Alumni mentors</span></li>
                <li><strong><?php echo htmlspecialchars($stats['published_opportunities']); ?>+</strong><span>Active listings</span></li>
            </ul>
        </div>
    </div>
</section>

<section class="section container">
    <div class="section-heading">
        <span class="eyebrow">For every AIUB user</span>
        <h2>Designed around student, alumni, and admin workflows</h2>
    </div>
    <div class="feature-grid three-col">
        <article class="feature-card">
            <div class="icon-badge">01</div>
            <h3>Students</h3>
            <p>Create a profile, save opportunities, and discover alumni and internship leads relevant to your field.</p>
        </article>
        <article class="feature-card">
            <div class="icon-badge">02</div>
            <h3>Alumni</h3>
            <p>Share career experiences, support students, and make contact preferences clear through a verified alumni directory.</p>
        </article>
        <article class="feature-card">
            <div class="icon-badge">03</div>
            <h3>Admins</h3>
            <p>Publish opportunities, review profile information, and manage a healthy career network for the campus community.</p>
        </article>
    </div>
</section>

<section class="section alt-bg">
    <div class="container">
        <div class="split-layout">
            <div>
                <span class="eyebrow">Opportunity pipeline</span>
                <h2>Find the right fit faster.</h2>
                <p>Students can browse relevant internships and jobs, review company details, and apply through included external links.</p>
            </div>
            <div class="mini-panel">
                <h3>Featured roles</h3>
                <?php foreach (array_slice(array_values(array_filter($repository->getOpportunities(), static fn (array $opportunity): bool => $opportunity['status'] === 'published' && $opportunity['deadline'] >= date('Y-m-d'))), 0, 3) as $opportunity): ?>
                    <div class="listing-mini">
                        <span><?php echo htmlspecialchars($opportunity['type']); ?></span>
                        <strong><?php echo htmlspecialchars($opportunity['title']); ?></strong>
                        <small><?php echo htmlspecialchars($opportunity['company']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
