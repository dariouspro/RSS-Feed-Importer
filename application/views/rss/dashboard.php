<?php if ($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4">
    <?= $this->session->flashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="mb-5">
        <h1 class="display-6 fw-bold mb-2">Dashboard</h1>
        <p class="text-muted">Overview of all posts across platforms (<?= $total_posts ?> total posts)</p>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded">
                <div class="rounded-circle bg-primary p-2 me-3">
                    <i class="bi bi-newspaper text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small">Total Posts</p>
                    <h4 class="mb-0 fw-bold"><?= $total_posts ?></h4>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded">
                <div class="rounded-circle bg-success p-2 me-3">
                    <i class="bi bi-share text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small">Platforms</p>
                    <h4 class="mb-0 fw-bold"><?= count($platforms) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="card-title fw-bold mb-1">All Posts</h4>
                    <p class="text-muted">View all posts across all platforms</p>
                </div>
                
                <!-- Import RSS Feed Button -->
                <a href="<?= base_url('rss/import') ?>" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i> Import RSS Feed
                </a>
            </div>

            <?php if (empty($posts)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-bar-chart text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="card-title mb-3">No posts available</h4>
                    <p class="card-text text-muted mb-4">
                        Import an RSS feed to see posts across all platforms
                    </p>
                    <a href="<?= base_url('rss/import') ?>" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i> Import RSS Feed
                    </a>
                </div>
            <?php else: ?>
                <!-- Posts List -->
                <div id="posts-list" class="mb-4">
                    <?php foreach ($posts as $post): ?>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row">
                                <!-- Post Image -->
                                <?php if (!empty($post['image_url'])): ?>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <img src="<?= htmlspecialchars($post['image_url']) ?>" 
                                         alt="Post image" 
                                         class="img-fluid rounded"
                                         style="height: 160px; width: 100%; object-fit: cover;"
                                         onerror="this.style.display='none'">
                                </div>
                                <?php endif; ?>
                                
                                <!-- Post Content -->
                                <div class="<?= !empty($post['image_url']) ? 'col-md-9' : 'col-12' ?>">
                                    <!-- Post Meta -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                            <i class="bi bi-flag me-1"></i> Priority <?= $post['priority'] ?>
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            <i class="bi bi-calendar me-1"></i> <?= date('M d, Y', strtotime($post['pub_date'])) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-hash me-1"></i> ID: <?= $post['id'] ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Post Title -->
                                    <h5 class="card-title mb-3"><?= htmlspecialchars($post['title']) ?></h5>
                                    
                                    <!-- Post Excerpt -->
                                    <p class="card-text text-muted mb-4">
                                        <?= htmlspecialchars(substr($post['content'], 0, 200)) ?><?= strlen($post['content']) > 200 ? '...' : '' ?>
                                    </p>
                                    
                                    <!-- Platform Distribution -->
                                    <div class="mb-3">
                                        <p class="text-muted mb-2 small">Platform Distribution:</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($post['platforms'] as $platform): ?>
                                                <?php 
                                                $platform_styles = [
                                                    'X (Twitter)' => 'bg-dark text-white',
                                                    'Facebook' => 'bg-primary text-white',
                                                    'LinkedIn' => 'bg-info text-white',
                                                    'Instagram' => 'bg-danger text-white',
                                                    'TikTok' => 'bg-dark text-white',
                                                    'Threads' => 'bg-purple text-white'
                                                ];
                                                $platform_style = isset($platform_styles[$platform]) ? $platform_styles[$platform] : 'bg-secondary text-white';
                                                
                                                $platform_icons = [
                                                    'X (Twitter)' => 'bi bi-twitter-x',
                                                    'Facebook' => 'bi bi-facebook',
                                                    'LinkedIn' => 'bi bi-linkedin',
                                                    'Instagram' => 'bi bi-instagram',
                                                    'TikTok' => 'bi bi-tiktok',
                                                    'Threads' => 'bi bi-threads'
                                                ];
                                                $icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
                                                ?>
                                                <span class="badge <?= $platform_style ?> d-flex align-items-center">
                                                    <i class="<?= $icon ?> me-1"></i> <?= $platform ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Additional Info -->
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <div class="text-muted small">
                                            <i class="bi bi-info-circle me-1"></i>
                                            <?php 
                                            $platform_count = count($post['platforms']);
                                            $platform_text = $platform_count === 1 ? 'platform' : 'platforms';
                                            ?>
                                            Assigned to <?= $platform_count ?> <?= $platform_text ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('g:i A', strtotime($post['pub_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('rss/dashboard?page=' . ($current_page - 1)) ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php 
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        // First page
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('rss/dashboard?page=1') ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('rss/dashboard?page=' . $i) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('rss/dashboard?page=' . $total_pages) ?>">
                                    <?= $total_pages ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('rss/dashboard?page=' . ($current_page + 1)) ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
        border-radius: 6px;
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .rounded-circle {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    .border-opacity-25 {
        --bs-border-opacity: 0.25;
    }
    
    .posts-list .card {
        border: 1px solid #dee2e6;
    }
    
    .posts-list .card:hover {
        border-color: #0d6efd;
    }
</style>