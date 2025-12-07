<?php if ($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $this->session->flashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title mb-4">Dashboard - Posts by Platform</h2>
        
        <div class="mb-4">
            <label for="platform-filter" class="form-label">Filter by Platform</label>
            <select class="form-select" id="platform-filter" style="max-width: 300px;">
                <option value="all" <?= $platform_filter === 'all' ? 'selected' : '' ?>>All Platforms</option>
                <?php foreach ($platforms as $platform): ?>
                    <option value="<?= $platform ?>" <?= $platform_filter === $platform ? 'selected' : '' ?>>
                        <?= $platform ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if (empty($posts)): ?>
            <p class="text-center text-muted py-5">
                <?= $platform_filter === 'all' ? 'No posts available.' : 'No posts assigned to ' . $platform_filter . '.' ?>
            </p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($posts as $post): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <?php if (!empty($post['image_url'])): ?>
                                <div class="me-3">
                                    <img src="<?= htmlspecialchars($post['image_url']) ?>" 
                                         alt="Post image" 
                                         style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;"
                                         onerror="this.style.display='none'">
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        <span class="badge bg-primary me-2">Priority <?= $post['priority'] ?></span>
                                        <span class="badge bg-secondary"><?= date('M d, Y', strtotime($post['pub_date'])) ?></span>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                    <p class="card-text text-muted"><?= htmlspecialchars($post['content']) ?></p>
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <?php foreach ($post['platforms'] as $platform): ?>
                                            <span class="badge bg-info"><?= $platform ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=' . ($current_page - 1)) ?>">Previous</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Page <?= $current_page ?> of <?= $total_pages ?></span>
                    </li>
                    <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=' . ($current_page + 1)) ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('platform-filter').addEventListener('change', function() {
    window.location.href = '<?= base_url('rss/dashboard') ?>?platform=' + this.value;
});
</script>