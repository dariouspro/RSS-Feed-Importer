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
    <p class="text-muted">
        Overview of posts distribution across platforms 
        (<?= $total_posts ?> posts with assigned platforms)
        <?php if ($platform_filter === 'all'): ?>
            <br><small class="text-muted"><i class="bi bi-info-circle me-1"></i> Posts without assigned platforms are hidden</small>
        <?php endif; ?>
    </p>
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
                <small class="text-muted">
                    <?php if ($platform_filter !== 'all'): ?>
                        <?= $platform_filter ?> only
                    <?php else: ?>
                        All platforms
                    <?php endif; ?>
                </small>
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
                <small class="text-muted">
                    <?php if ($platform_filter !== 'all'): ?>
                        Showing <?= $platform_filter ?> posts
                    <?php else: ?>
                        All available
                    <?php endif; ?>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Current Filter Status -->
    <?php if ($platform_filter !== 'all'): ?>
    <div class="col-md-6 col-sm-12">
        <div class="d-flex align-items-center p-3 bg-info bg-opacity-10 border border-info border-opacity-25 rounded">
            <div class="rounded-circle bg-info p-2 me-3">
                <i class="bi bi-filter text-white"></i>
            </div>
            <div class="flex-grow-1">
                <p class="text-muted mb-1 small">Currently Viewing</p>
                <h4 class="mb-0 fw-bold"><?= $platform_filter ?> Posts</h4>
                <small class="text-muted">Filtered by platform (<?= $total_posts ?> posts)</small>
            </div>
            <a href="<?= base_url('rss/dashboard?platform=all') ?>" class="btn btn-sm btn-outline-info">
                Clear Filter
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

    <!-- Dashboard Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="card-title fw-bold mb-1">Posts by Platform</h4>
                    <p class="text-muted">Filter and view posts across different platforms</p>
                </div>
                
                <div class="d-flex gap-2">
                    <!-- Platform Filter -->
                    <div>
                        <label for="platform-filter" class="form-label fw-semibold">Filter by Platform</label>
                        <select class="form-select" id="platform-filter" style="width: 200px;">
                            <option value="all" <?= $platform_filter === 'all' ? 'selected' : '' ?>>All Platforms</option>
                            <?php foreach ($platforms as $platform): ?>
                                <option value="<?= $platform ?>" <?= $platform_filter === $platform ? 'selected' : '' ?>>
                                    <?= $platform ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Import RSS Feed Button -->
                    <div class="pt-4">
                        <a href="<?= base_url('rss/import') ?>" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i> Import RSS
                        </a>
                    </div>
                </div>
            </div>

            <?php if (empty($posts)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-bar-chart text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="card-title mb-3">
                        <?= $platform_filter === 'all' ? 'No posts available' : 'No posts for ' . $platform_filter ?>
                    </h4>
                    <p class="card-text text-muted mb-4">
                        <?= $platform_filter === 'all' ? 'Import an RSS feed to see posts' : 'Try selecting "All Platforms" or import more content' ?>
                    </p>
                    <?php if ($platform_filter === 'all'): ?>
                    <a href="<?= base_url('rss/import') ?>" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i> Import RSS Feed
                    </a>
                    <?php else: ?>
                    <a href="<?= base_url('rss/manage') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-newspaper me-2"></i> Manage Posts
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Platform Summary (Only shown when a specific platform is selected) -->
                <?php if ($platform_filter !== 'all'): ?>
                <div class="alert alert-info mb-4 d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        Showing posts assigned to <strong><?= $platform_filter ?></strong>
                        <span class="text-muted ms-2">(<?= count($posts) ?> posts)</span>
                    </div>
                    <a href="<?= base_url('rss/dashboard?platform=all') ?>" class="btn btn-sm btn-outline-info ms-auto">
                        View All Platforms
                    </a>
                </div>
                <?php endif; ?>
																		  
						<!-- Quick Platform Stats -->
						<div class="row mb-4">
							<?php 
							// Count posts per platform from CURRENTLY FILTERED posts
							$platform_counts = [];
							foreach ($posts as $post) {
								foreach ($post['platforms'] as $platform) {
									if (!isset($platform_counts[$platform])) {
										$platform_counts[$platform] = 0;
									}
									$platform_counts[$platform]++;
								}
							}
							
							// Sort by count descending, then by name
							arsort($platform_counts);
							
							// Platform icons mapping
							$platform_icons = [
								'X (Twitter)' => 'bi bi-twitter-x text-dark',
								'Facebook' => 'bi bi-facebook text-primary',
								'LinkedIn' => 'bi bi-linkedin text-info',
								'Instagram' => 'bi bi-instagram text-danger',
								'TikTok' => 'bi bi-tiktok text-dark',
								'Threads' => 'bi bi-threads text-purple'
							];
							?>
							
							<!-- All Platforms Card (when not selected) -->
							<?php if ($platform_filter !== 'all'): ?>
							<div class="col-md-2 col-sm-4 col-6 mb-2">
								<a href="<?= base_url('rss/dashboard?platform=all') ?>" 
								   class="text-decoration-none">
									<div class="card border-0 shadow-sm text-center p-3 h-100">
										<div class="mb-2">
											<i class="bi bi-grid-3x3-gap text-secondary" style="font-size: 1.5rem;"></i>
										</div>
										<h6 class="mb-1 fw-bold">All Platforms</h6>
										<p class="mb-0 text-muted small">View all posts</p>
									</div>
								</a>
							</div>
							<?php endif; ?>
							
							<!-- Platform Cards - Only show platforms with posts -->
							<?php foreach ($platform_counts as $platform => $count): ?>
								<?php if ($count > 0): ?>
									<?php 
									$is_selected = ($platform_filter === $platform);
									?>
									<div class="col-md-2 col-sm-4 col-6 mb-2">
										<a href="<?= base_url('rss/dashboard?platform=' . urlencode($platform)) ?>" 
										   class="text-decoration-none">
											<div class="card border-0 shadow-sm text-center p-3 h-100 
												<?= $is_selected ? 'border-primary border-2 bg-primary bg-opacity-10' : '' ?>"
												style="cursor: pointer; transition: all 0.2s ease;">
												<?php 
												$icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share text-secondary';
												?>
												<div class="mb-2">
													<i class="<?= $icon ?>" style="font-size: 1.5rem;"></i>
												</div>
												<h6 class="mb-1 fw-bold <?= $is_selected ? 'text-primary' : '' ?>">
													<?= $platform ?>
												</h6>
												
												<p class="mb-0">
													<span class="<?= $is_selected ? 'text-primary' : 'text-dark' ?> fw-bold">
														<?= $count ?>
													</span> 
													<span class="text-muted small">
														<?= $count === 1 ? 'post' : 'posts' ?>
													</span>
												</p>
												
												<?php if ($is_selected): ?>
												<div class="mt-1">
													<small class="badge bg-primary">Selected</small>
												</div>
												<?php endif; ?>
											</div>
										</a>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
							
							<!-- Show message if no platforms have posts -->
							<?php if (empty($platform_counts) || array_sum($platform_counts) === 0): ?>
							<div class="col-12">
								<div class="alert alert-warning text-center py-3">
									<i class="bi bi-exclamation-triangle me-2"></i>
									No posts found for the selected filter
								</div>
							</div>
							<?php endif; ?>
						</div>      
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
                                        
                                        <!-- Platform Count Badge -->
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            <i class="bi bi-share me-1"></i> <?= count($post['platforms']) ?> Platforms
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
									<p class="text-muted mb-2 small">Assigned Platforms:</p>
									<div class="d-flex flex-wrap gap-2">
										<?php 
										// Platform icons mapping
										$platform_icons = [
											'X (Twitter)' => 'bi bi-twitter-x',
											'Facebook' => 'bi bi-facebook',
											'LinkedIn' => 'bi bi-linkedin',
											'Instagram' => 'bi bi-instagram',
											'TikTok' => 'bi bi-tiktok',
											'Threads' => 'bi bi-threads'
										];
										
										// Platform styles mapping
										$platform_styles = [
											'X (Twitter)' => 'bg-dark text-white',
											'Facebook' => 'bg-primary text-white',
											'LinkedIn' => 'bg-info text-white',
											'Instagram' => 'bg-danger text-white',
											'TikTok' => 'bg-dark text-white',
											'Threads' => 'bg-purple text-white'
										];
										
										// Show assigned platforms
										foreach ($post['platforms'] as $platform): 
											$is_selected = ($platform_filter !== 'all' && $platform === $platform_filter);
											
											$style_class = isset($platform_styles[$platform]) ? $platform_styles[$platform] : 'bg-secondary text-white';
											
											if ($is_selected) {
												$style_class .= ' border border-3 border-warning';
											}
											
											$icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
										?>
											<span class="badge <?= $style_class ?> d-flex align-items-center">
												<i class="<?= $icon ?> me-1"></i> <?= $platform ?>
												
												<?php if ($is_selected): ?>
													<i class="bi bi-check-circle ms-1"></i>
												<?php endif; ?>
											</span>
										<?php endforeach; ?>
									</div>
								</div>													  
							 <!-- Post Status -->
									<div class="d-flex justify-content-between align-items-center pt-3 border-top">
										<div class="text-muted small">
											<?php 
											$platform_count = count($post['platforms']);
											$platform_text = $platform_count === 1 ? 'platform' : 'platforms';
											
											if ($platform_filter === 'all') {
												echo '<i class="bi bi-grid-3x3-gap me-1"></i>';
												echo "Assigned to <strong>{$platform_count}</strong> {$platform_text}";
											} else {
												echo '<i class="bi bi-check-circle-fill text-success me-1"></i>';
												echo "Has <strong>{$platform_filter}</strong> platform";
											}
											?>
										</div>
										
										<div class="d-flex align-items-center gap-2">
											<div class="text-muted small">
												<i class="bi bi-clock me-1"></i>
												<?= date('g:i A', strtotime($post['pub_date'])) ?>
											</div>
											
											<?php if ($platform_filter !== 'all'): ?>
												<span class="badge bg-success bg-opacity-10 text-success border border-success">
													<i class="bi bi-check-lg me-1"></i> Included
												</span>
											<?php endif; ?>
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
                            <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=' . ($current_page - 1)) ?>">
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
                                <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=1') ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=' . $i) ?>">
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
                                <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=' . $total_pages) ?>">
                                    <?= $total_pages ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('rss/dashboard?platform=' . $platform_filter . '&page=' . ($current_page + 1)) ?>">
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
    
    /* Platform cards */
    .platform-card {
        transition: all 0.3s ease;
    }
    
    .platform-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* Highlight selected platform */
    .border-primary {
        border-color: #0d6efd !important;
    }
    
    /* Filter styling */
    #platform-filter:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<script>
// Platform filter functionality
document.getElementById('platform-filter').addEventListener('change', function() {
    const selectedPlatform = this.value;
    const currentPage = <?= $current_page ?>;
    window.location.href = '<?= base_url('rss/dashboard') ?>?platform=' + selectedPlatform + '&page=1';
});

// Add hover effects to platform cards
document.addEventListener('DOMContentLoaded', function() {
    const platformCards = document.querySelectorAll('.platform-card');
    platformCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Highlight current platform in filter dropdown
    const currentPlatform = '<?= $platform_filter ?>';
    if (currentPlatform !== 'all') {
        const platformFilter = document.getElementById('platform-filter');
        const selectedOption = platformFilter.querySelector(`option[value="${currentPlatform}"]`);
        
        if (selectedOption) {
            // Remove and re-add to put it at top (after "All Platforms")
            selectedOption.remove();
            const allOption = platformFilter.querySelector('option[value="all"]');
            allOption.after(selectedOption);
        }
    }
});
</script>