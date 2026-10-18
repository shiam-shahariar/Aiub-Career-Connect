<?php
require __DIR__ . '/partials/header.php';
$opportunities = $repository->getOpportunities();
?>
<section class="section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Job board</span>
            <h1>Explore internships and jobs</h1>
        </div>
    </div>

    <div class="filter-panel panel">
        <form id="opportunity-filter-form" class="filter-form">
            <label>
                Keyword
                <input type="text" name="keyword" placeholder="Search title or company">
            </label>
            <label>
                Type
                <select name="type">
                    <option value="">All</option>
                    <option value="Internship">Internship</option>
                    <option value="Job">Job</option>
                </select>
            </label>
            <label>
                Department
                <select name="department">
                    <option value="">All</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Business Administration">Business Administration</option>
                    <option value="EEE">EEE</option>
                </select>
            </label>
            <button type="submit" class="btn btn--primary">Apply filters</button>
        </form>
    </div>

    <div id="opportunity-results" class="listing-grid">
        <?php foreach ($opportunities as $opportunity): ?>
            <?php if ($opportunity['status'] !== 'published') continue; ?>
            <article class="listing-card">
                <div class="listing-card__meta">
                    <span class="badge badge--soft"><?php echo htmlspecialchars($opportunity['type']); ?></span>
                    <span><?php echo htmlspecialchars($opportunity['location']); ?></span>
                </div>
                <h3><?php echo htmlspecialchars($opportunity['title']); ?></h3>
                <p class="company-name"><?php echo htmlspecialchars($opportunity['company']); ?></p>
                <p><?php echo htmlspecialchars($opportunity['description']); ?></p>
                <div class="listing-card__foot">
                    <small>Deadline: <?php echo htmlspecialchars($opportunity['deadline']); ?></small>
                    <a href="index.php?page=opportunity-detail&id=<?php echo $opportunity['id']; ?>" class="btn btn--secondary">View details</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
