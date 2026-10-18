<?php
require __DIR__ . '/partials/header.php';
if (!$currentUser || $currentUser['role'] !== 'admin'):
?>
<section class="section container">
    <div class="message-panel">
        <h1>Admin access only</h1>
        <p>Only administrators can review alumni profiles and manage companies and opportunities.</p>
        <a href="index.php?page=login" class="btn btn--primary">Login as admin</a>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; return; ?>
<?php
endif;
$stats = $repository->getDemoStats();
$reviewQueue = $repository->getAlumniReviewQueue();
$reports = $repository->getOpenProfileReports();
$companies = $repository->getCompanies([], $currentUser);
$auditLog = $repository->getAuditLog();
$opportunities = $repository->getOpportunities();
$csrfToken = htmlspecialchars($_SESSION['csrf_token']);
?>
<section class="section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Admin control</span>
            <h1>Network management</h1>
        </div>
    </div>

    <p class="demo-note">Review alumni claims, manage verified company records, and maintain opportunity status. <?php echo $repository->isPersistent() ? 'Changes and audit history are stored in MySQL.' : 'Demo mode is active: changes last for this session. Start MySQL and import the database scripts for persistent storage.'; ?></p>

    <div class="stats-grid">
        <div class="stat-card"><span>Alumni reviews to action</span><strong><?php echo count($reviewQueue); ?></strong><small>Pending or previously rejected</small></div>
        <div class="stat-card"><span>Verified alumni</span><strong><?php echo (int) $stats['verified_alumni']; ?></strong><small>Verified profiles</small></div>
        <div class="stat-card"><span>Active companies</span><strong><?php echo count($companies); ?></strong><small>Company directory records</small></div>
        <div class="stat-card"><span>Open profile reports</span><strong><?php echo count($reports); ?></strong><small>Reports requiring review</small></div>
    </div>

    <section class="panel admin-section">
        <h2>Alumni verification queue</h2>
        <?php if (!$reviewQueue): ?>
            <p>No alumni profiles are waiting for review.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Alumnus</th><th>Department / graduation</th><th>Company / title</th><th>Current status</th><th>Review</th></tr></thead>
                    <tbody>
                        <?php foreach ($reviewQueue as $person): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($person['name']); ?></td>
                                <td><?php echo htmlspecialchars($person['department']); ?> · <?php echo htmlspecialchars((string) $person['graduation_year']); ?></td>
                                <td><?php echo htmlspecialchars($person['company']); ?><?php if ($person['job_title']): ?> · <?php echo htmlspecialchars($person['job_title']); ?><?php endif; ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($person['verification_status'])); ?><?php if (!empty($person['review_note'])): ?><br><small><?php echo htmlspecialchars($person['review_note']); ?></small><?php endif; ?></td>
                                <td>
                                    <form method="post" action="index.php" class="admin-inline-form">
                                        <input type="hidden" name="action" value="review_alumni">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                        <input type="hidden" name="profile_id" value="<?php echo (int) $person['profile_id']; ?>">
                                        <select name="status" aria-label="Set alumni verification status">
                                            <option value="verified">Verify</option>
                                            <option value="rejected">Reject</option>
                                            <option value="pending">Keep pending</option>
                                        </select>
                                        <input type="text" name="review_note" maxlength="2000" placeholder="Review note" value="<?php echo htmlspecialchars($person['review_note'] ?? ''); ?>">
                                        <button class="btn btn--primary" type="submit">Save review</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel admin-section">
        <h2>Opportunity lifecycle</h2>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Opportunity</th><th>Company</th><th>Deadline</th><th>Status</th><th>Update status / company</th></tr></thead>
                <tbody>
                    <?php foreach ($opportunities as $opportunity): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($opportunity['title']); ?></td>
                            <td><?php echo htmlspecialchars($opportunity['company']); ?></td>
                            <td><?php echo htmlspecialchars($opportunity['deadline']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($opportunity['status'])); ?></td>
                            <td>
                                <form method="post" action="index.php" class="admin-inline-form">
                                    <input type="hidden" name="action" value="update_opportunity_status">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="opportunity_id" value="<?php echo (int) $opportunity['id']; ?>">
                                    <select name="status" aria-label="Set opportunity status">
                                        <?php foreach (['draft', 'published', 'closed', 'archived'] as $status): ?>
                                            <option value="<?php echo $status; ?>"<?php echo $opportunity['status'] === $status ? ' selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="company_id" aria-label="Link opportunity to a company">
                                        <option value="0">No company link</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?php echo (int) $company['id']; ?>"<?php echo (int) ($opportunity['company_id'] ?? 0) === (int) $company['id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($company['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn--primary" type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="demo-note">Published opportunities past their deadline are automatically closed when listings are read.</p>
    </section>

    <section class="panel admin-section">
        <h2>Company records</h2>
        <p class="demo-note">Potential duplicates use a case-insensitive, trimmed company name plus location. Merge records to move alumni and opportunities to the selected canonical company.</p>
        <form method="post" action="index.php" class="company-admin-form company-create-form">
            <input type="hidden" name="action" value="save_company">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="id" value="0">
            <h3>Add a company</h3>
            <label>Company name<input type="text" name="name" maxlength="200" required></label>
            <label>Industry<input type="text" name="industry" maxlength="100"></label>
            <label>Website<input type="url" name="website" maxlength="255" placeholder="https://"></label>
            <label>Location<input type="text" name="location" maxlength="150"></label>
            <label class="full-width">Description<textarea name="description" rows="3"></textarea></label>
            <button class="btn btn--primary" type="submit">Add company</button>
        </form>
        <?php if (!$companies): ?>
            <p>No active company records.</p>
        <?php else: ?>
            <div class="admin-company-list">
                <?php foreach ($companies as $company): ?>
                    <form method="post" action="index.php" class="company-admin-form">
                        <input type="hidden" name="action" value="save_company">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="id" value="<?php echo (int) $company['id']; ?>">
                        <h3><?php echo htmlspecialchars($company['name']); ?> <small><?php echo (int) $company['alumni_count']; ?> verified visible alumni</small></h3>
                        <label>Company name<input type="text" name="name" maxlength="200" required value="<?php echo htmlspecialchars($company['name']); ?>"></label>
                        <label>Industry<input type="text" name="industry" maxlength="100" value="<?php echo htmlspecialchars($company['industry']); ?>"></label>
                        <label>Website<input type="url" name="website" maxlength="255" value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>" placeholder="https://"></label>
                        <label>Location<input type="text" name="location" maxlength="150" value="<?php echo htmlspecialchars($company['location'] ?? ''); ?>"></label>
                        <label class="full-width">Description<textarea name="description" rows="3"><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea></label>
                        <button class="btn btn--secondary" type="submit">Save company</button>
                    </form>
                <?php endforeach; ?>
            </div>
            <?php if (count($companies) > 1): ?>
                <form method="post" action="index.php" class="merge-company-form">
                    <input type="hidden" name="action" value="merge_companies">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <label>Duplicate record
                        <select name="source_company_id" required>
                            <?php foreach ($companies as $company): ?><option value="<?php echo (int) $company['id']; ?>"><?php echo htmlspecialchars($company['name'] . ' — ' . ($company['location'] ?: 'location unspecified') . ' — ' . $company['industry']); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Keep this record
                        <select name="target_company_id" required>
                            <?php foreach ($companies as $company): ?><option value="<?php echo (int) $company['id']; ?>"><?php echo htmlspecialchars($company['name'] . ' — ' . ($company['location'] ?: 'location unspecified') . ' — ' . $company['industry']); ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <button class="btn btn--primary" type="submit">Merge companies</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <section class="panel admin-section">
        <h2>Profile reports</h2>
        <?php if (!$reports): ?>
            <p>No open profile reports.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Alumni profile</th><th>Reason / details</th><th>Reported</th><th>Review</th></tr></thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['full_name'] ?? ('Profile #' . $report['profile_id'])); ?></td>
                                <td><?php echo htmlspecialchars($report['reason']); ?><?php if ($report['details']): ?><br><?php echo htmlspecialchars($report['details']); ?><?php endif; ?></td>
                                <td><?php echo htmlspecialchars($report['created_at']); ?></td>
                                <td>
                                    <form method="post" action="index.php" class="admin-inline-form">
                                        <input type="hidden" name="action" value="review_profile_report">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                        <input type="hidden" name="report_id" value="<?php echo (int) $report['id']; ?>">
                                        <button class="btn btn--secondary" name="status" value="reviewed" type="submit">Mark reviewed</button>
                                        <button class="btn btn--ghost" name="status" value="dismissed" type="submit">Dismiss</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel admin-section">
        <h2>Recent administrative actions</h2>
        <?php if (!$auditLog): ?>
            <p>No administrative actions have been recorded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Action</th><th>Target</th><th>Actor</th><th>Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($auditLog as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(str_replace('_', ' ', $entry['action'])); ?></td>
                                <td><?php echo htmlspecialchars($entry['target_type'] . ' #' . $entry['target_id']); ?></td>
                                <td><?php echo htmlspecialchars($entry['actor_name'] ?? ($entry['actor_id'] ? 'User #' . $entry['actor_id'] : 'System')); ?></td>
                                <td><?php echo htmlspecialchars($entry['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
