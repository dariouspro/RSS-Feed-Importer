<?php if ($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4">
    <?= $this->session->flashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="mb-5">
        <h1 class="display-6 fw-bold mb-2">Manage Posts</h1>
        <p class="text-muted">Organize and manage your imported RSS feed posts (<?= $total_posts ?> total)</p>
    </div>

    <?php if (empty($posts)): ?>
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-newspaper text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="card-title mb-3">No posts yet</h4>
                <p class="card-text text-muted mb-4">Import an RSS feed to get started</p>
                <a href="<?= base_url('rss/import') ?>" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i> Import RSS Feed
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Posts List -->
        <div id="posts-container" class="mb-5">
            <?php foreach ($posts as $index => $post): ?>
                <?php 
                $has_twitter = in_array('X (Twitter)', $post['platforms']);
                $exceeds_limit = $has_twitter && $post['char_count'] > 280;
                ?>
                <div class="card mb-3 border <?= $exceeds_limit ? 'border-danger border-2' : 'border-light' ?> post-item" 
                     draggable="true" 
                     data-id="<?= $post['id'] ?>"
                     data-priority="<?= $post['priority'] ?>"
                     data-original-index="<?= $index ?>">
                    
                    <!-- Drag Handle -->
                    <div class="drag-handle position-absolute top-50 start-0 translate-middle-y ms-3">
                        <i class="bi bi-grip-vertical text-muted"></i>
                    </div>
                    
                    <div class="card-body p-4">
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
                                <!-- Post Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 priority-badge">
                                            <i class="bi bi-flag me-1"></i> Priority <?= $post['priority'] ?>
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            <?= date('M d, Y', strtotime($post['pub_date'])) ?>
                                        </span>
                                        <span class="badge <?= $exceeds_limit ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : 'bg-info bg-opacity-10 text-info' ?>">
                                            <?= $post['char_count'] ?> chars
                                        </span>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu"> 
                                            <li><a class="dropdown-item text-danger" href="<?= base_url('rss/delete/' . $post['id']) ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this post?')">
                                                   <i class="bi bi-trash me-2"></i> Delete
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- Post Title -->
                                <h5 class="card-title mb-3"><?= htmlspecialchars($post['title']) ?></h5>
                                
                                <!-- Post Excerpt -->
                                <p class="card-text text-muted mb-4">
                                    <?= htmlspecialchars(substr($post['content'], 0, 200)) ?><?= strlen($post['content']) > 200 ? '...' : '' ?>
                                </p>
                                
                                <!-- Twitter Warning -->
                                <?php if ($exceeds_limit): ?>
                                <div class="alert alert-danger d-flex align-items-center mb-3 py-2">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>Post exceeds X (Twitter) 280 character limit!</div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Platform Selection with Dropdown -->
                                <div class="platform-selection" data-post-id="<?= $post['id'] ?>">
                                    <div class="row align-items-end">
                                        <div class="col-md-8">
                                            <label class="form-label text-muted small mb-1">Select platforms for this post:</label>
                                            <select class="form-select platform-select" multiple size="3" data-post-id="<?= $post['id'] ?>">
                                                <?php foreach ($platforms as $platform): ?>
                                                    <?php 
																  $platform_icons = [
														'X (Twitter)' => 'bi bi-twitter-x text-dark',
														'Facebook' => 'bi bi-facebook text-primary',
														'LinkedIn' => 'bi bi-linkedin text-info',
														'Instagram' => 'bi bi-instagram text-danger',
														'TikTok' => 'bi bi-tiktok text-dark',
														'Threads' => 'bi bi-threads text-purple'
													];
                                                    $icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
                                                    ?>
                                                    <option value="<?= $platform ?>" 
                                                            <?= in_array($platform, $post['platforms']) ? 'selected' : '' ?>
                                                            data-icon="<?= $icon ?>">
                                                        <?= $platform ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" 
                                                    class="btn btn-primary w-100 save-platforms-btn"
                                                    data-post-id="<?= $post['id'] ?>">
                                                <i class="bi bi-save me-1"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Current Selection Display -->
                                    <div class="mt-2">
                                        <p class="text-muted mb-1 small">Currently assigned:</p>
                                        <div class="d-flex flex-wrap gap-1 current-platforms" id="current-platforms-<?= $post['id'] ?>">
                                            <?php if (!empty($post['platforms'])): ?>
                                                <?php foreach ($post['platforms'] as $platform): ?>
                                                    <?php 
																 $platform_icons = [
													'X (Twitter)' => 'bi bi-twitter-x text-dark',
													'Facebook' => 'bi bi-facebook text-primary',
													'LinkedIn' => 'bi bi-linkedin text-info',
													'Instagram' => 'bi bi-instagram text-danger',
													'TikTok' => 'bi bi-tiktok text-dark',
													'Threads' => 'bi bi-threads text-purple'
												];
                                                    $icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
                                                    ?>
                                                    <span class="badge bg-primary d-flex align-items-center">
                                                        <i class="<?= $icon ?> me-1"></i> <?= $platform ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">No platforms selected</span>
                                            <?php endif; ?>
                                        </div>
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
                    <a class="page-link" href="<?= base_url('rss/manage?page=' . ($current_page - 1)) ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                
                <!-- Page Numbers -->
                <?php 
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('rss/manage?page=' . $i) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('rss/manage?page=' . ($current_page + 1)) ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav> 
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .card {
        border-radius: 12px;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .drag-handle {
        cursor: move;
        opacity: 0;
        transition: opacity 0.2s ease;
        background: white;
        padding: 4px 6px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }
    
    .card:hover .drag-handle {
        opacity: 1;
    }
    
    .post-item.dragging {
        opacity: 0.5;
        transform: rotate(2deg) scale(0.98);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
        z-index: 1000;
        position: relative;
    }
    
    .post-item.drag-over {
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05);
        transition: transform 0.2s ease;
    }
    
    .post-item.drag-over-above {
        border-top: 3px solid #0d6efd !important;
        transform: translateY(-10px);
    }
    
    .post-item.drag-over-below {
        border-bottom: 3px solid #0d6efd !important;
        transform: translateY(10px);
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    
    .platform-badge {
        border-radius: 20px;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .platform-badge:hover {
        transform: translateY(-1px);
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .drag-feedback {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1001;
        animation: fadeInOut 1.5s ease;
    }
    
    /* Platform Selection Styles */
    .platform-select {
        height: auto;
        min-height: 100px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .platform-select option {
        padding: 8px 12px;
        border-radius: 4px;
        margin-bottom: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .platform-select option:hover {
        background-color: #f8f9fa;
    }
    
    .platform-select option:checked {
        background-color: #0d6efd;
        color: white;
    }
    
    .platform-select option:checked::before {
        font-family: "bootstrap-icons";
        content: "\F26A";
        margin-right: 8px;
    }
    
    .save-platforms-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .save-platforms-btn .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
    
    .save-feedback {
        font-size: 0.75rem;
        animation: fadeInOut 2s ease;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: scale(0.8); }
        20% { opacity: 1; transform: scale(1); }
        80% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.8); }
    }
    
    /* Multi-select hint */
    .select-hint {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 4px;
    }
</style>

<script>
// Drag and Drop Implementation
let draggedItem = null;
let draggedItemIndex = null;
let originalPosition = null;

// Initialize drag and drop for all posts
function initializeDragAndDrop() {
    const postItems = document.querySelectorAll('.post-item');
    
    postItems.forEach((item, index) => {
        setupDragEvents(item, index);
    });
}

// Setup drag events for a single post item
function setupDragEvents(item, index) {
    item.addEventListener('dragstart', handleDragStart);
    item.addEventListener('dragend', handleDragEnd);
    item.addEventListener('dragover', handleDragOver);
    item.addEventListener('dragenter', handleDragEnter);
    item.addEventListener('dragleave', handleDragLeave);
    item.addEventListener('drop', handleDrop);
    
    // Prevent buttons from triggering drag
    const buttons = item.querySelectorAll('button, a, .dropdown-toggle');
    buttons.forEach(button => {
        button.addEventListener('mousedown', (e) => e.stopPropagation());
        button.addEventListener('dragstart', (e) => e.stopPropagation());
    });
}

// Event Handlers
function handleDragStart(e) {
    draggedItem = this;
    draggedItemIndex = Array.from(this.parentNode.children).indexOf(this);
    
    // Store original position for potential revert
    originalPosition = {
        parent: this.parentNode,
        nextSibling: this.nextSibling
    };
    
    this.classList.add('dragging');
    
    // Set drag image and data
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.id);
    
    // Make it semi-transparent
    setTimeout(() => {
        this.style.opacity = '0.4';
    }, 0);
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    this.style.opacity = '1';
    
    // Remove all drag-over classes
    document.querySelectorAll('.post-item').forEach(item => {
        item.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
        item.style.transform = '';
    });
    
    draggedItem = null;
    draggedItemIndex = null;
    originalPosition = null;
}

function handleDragOver(e) {
    e.preventDefault();
    
    if (draggedItem !== this) {
        const rect = this.getBoundingClientRect();
        const y = e.clientY - rect.top;
        const height = rect.height;
        const threshold = height / 2;
        
        // Remove all drag-over classes first
        this.classList.remove('drag-over-above', 'drag-over-below');
        
        if (y < threshold) {
            this.classList.add('drag-over-above');
        } else {
            this.classList.add('drag-over-below');
        }
        
        this.classList.add('drag-over');
    }
}

function handleDragEnter(e) {
    e.preventDefault();
}

function handleDragLeave(e) {
    this.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
    this.style.transform = '';
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    if (draggedItem !== this) {
        // Get drop position
        const rect = this.getBoundingClientRect();
        const y = e.clientY - rect.top;
        const height = rect.height;
        const threshold = height / 2;
        
        // Remove visual feedback
        this.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
        this.style.transform = '';
        
        // Determine where to insert
        if (y < threshold) {
            // Insert above
            this.parentNode.insertBefore(draggedItem, this);
        } else {
            // Insert below
            if (this.nextSibling) {
                this.parentNode.insertBefore(draggedItem, this.nextSibling);
            } else {
                this.parentNode.appendChild(draggedItem);
            }
        }
        
        // Update priorities visually
        updateVisualPriorities();
        
        // Send update to server
        updatePriorityOnServer(draggedItem.dataset.id, this.dataset.priority);
    }
}

// Update priority badges visually
function updateVisualPriorities() {
    const posts = document.querySelectorAll('.post-item');
    posts.forEach((post, index) => {
        const priorityBadge = post.querySelector('.priority-badge');
        if (priorityBadge) {
            const newPriority = index + 1;
            priorityBadge.innerHTML = `\<i class="bi bi-flag me-1"></i> Priority ${newPriority}`;
            post.dataset.priority = newPriority;
        }
    });
}

// Update priority on server via AJAX
function updatePriorityOnServer(postId, targetPriority) {
    // Show loading state on dragged item
    const dragHandle = draggedItem.querySelector('.drag-handle');
    const originalHandleContent = dragHandle.innerHTML;
    dragHandle.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
    
    // Send AJAX request
    fetch('<?= base_url('rss/update_priority') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `post_id=${postId}&new_priority=${targetPriority}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Show feedback
        showFeedback(draggedItem, data.success ? 'success' : 'error');
        
        if (!data.success) {
            // Revert position on failure
            revertPosition();
        }
    })
    .catch(error => {
        console.error('Error updating priority:', error);
        showFeedback(draggedItem, 'error');
        revertPosition();
    })
    .finally(() => {
        // Restore drag handle
        setTimeout(() => {
            dragHandle.innerHTML = originalHandleContent;
        }, 500);
    });
}

// Show visual feedback
function showFeedback(element, type) {
    // Remove existing feedback
    const existingFeedback = element.querySelector('.drag-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Create new feedback
    const feedback = document.createElement('div');
    feedback.className = `drag-feedback badge bg-${type === 'success' ? 'success' : 'danger'}`;
    feedback.innerHTML = type === 'success' ? 
        '<i class="bi bi-check-lg me-1"></i> Updated' : 
        '<i class="bi bi-x-lg me-1"></i> Failed';
    
    element.appendChild(feedback);
    
    // Remove after animation
    setTimeout(() => {
        if (feedback.parentNode) {
            feedback.remove();
        }
    }, 1500);
}

// Revert to original position
function revertPosition() {
    if (originalPosition) {
        if (originalPosition.nextSibling) {
            originalPosition.parent.insertBefore(draggedItem, originalPosition.nextSibling);
        } else {
            originalPosition.parent.appendChild(draggedItem);
        }
        
        // Update visual priorities after revert
        updateVisualPriorities();
    }
}

// Platform Selection with Dropdown and Save Button
function initializePlatformSelection() {
    // Update current platforms display when dropdown changes
    document.querySelectorAll('.platform-select').forEach(select => {
        select.addEventListener('change', function() {
            updateCurrentPlatformsDisplay(this);
        });
    });
    
    // Handle save button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.save-platforms-btn')) {
            const button = e.target.closest('.save-platforms-btn');
            savePlatforms(button);
        }
    });
    
    // Add multi-select hint
    document.querySelectorAll('.platform-select').forEach(select => {
        const hint = document.createElement('small');
        hint.className = 'select-hint';
        hint.innerHTML = 'Hold Ctrl (Cmd on Mac) to select multiple';
        select.parentNode.appendChild(hint);
    });
}

// Update the current platforms display
function updateCurrentPlatformsDisplay(selectElement) {
    const postId = selectElement.dataset.postId;
    const selectedOptions = Array.from(selectElement.selectedOptions);
    const currentPlatformsDiv = document.getElementById(`current-platforms-${postId}`);
    
    // Clear current display
    currentPlatformsDiv.innerHTML = '';
    
    if (selectedOptions.length === 0) {
        currentPlatformsDiv.innerHTML = '<span class="text-muted small">No platforms selected</span>';
        return;
    }
    
    // Add badges for each selected platform
    selectedOptions.forEach(option => {
        const platform = option.value;
        const icon = option.dataset.icon || 'bi bi-share';
        
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary d-flex align-items-center me-1 mb-1';
        badge.innerHTML = `<i class="${icon} me-1"></i> ${platform}`;
        
        currentPlatformsDiv.appendChild(badge);
    });
}

// Save platforms to server
function savePlatforms(button) {
    const postId = button.dataset.postId;
    const selectElement = document.querySelector(`.platform-select[data-post-id="${postId}"]`);
    const selectedOptions = Array.from(selectElement.selectedOptions);
    const selectedPlatforms = selectedOptions.map(option => option.value);
    
    // Disable button and show loading
    button.disabled = true;
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    
    // Send AJAX request to update all platforms at once
    fetch('<?= base_url('rss/update_platforms') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            post_id: postId,
            platforms: selectedPlatforms
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success feedback
            showPlatformSaveFeedback(button, 'success', 'Saved!');
            console.log('Platforms updated:', data);
        } else {
            // Show error feedback
            showPlatformSaveFeedback(button, 'error', 'Failed!');
            console.error('Save failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Error saving platforms:', error);
        showPlatformSaveFeedback(button, 'error', 'Error!');
    })
    .finally(() => {
        // Re-enable button after delay
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 2000);
    });
}

// Show feedback for platform save
function showPlatformSaveFeedback(button, type, message) {
    // Remove existing feedback
    const existingFeedback = button.parentNode.querySelector('.save-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Create feedback element
    const feedback = document.createElement('div');
    feedback.className = `save-feedback position-absolute mt-1 small text-${type === 'success' ? 'success' : 'danger'}`;
    feedback.innerHTML = `<i class="bi bi-${type === 'success' ? 'check' : 'x'}-circle me-1"></i> ${message}`;
    feedback.style.fontSize = '0.75rem';
    
    button.parentNode.style.position = 'relative';
    button.parentNode.appendChild(feedback);
    
    // Remove feedback after 3 seconds
    setTimeout(() => {
        if (feedback.parentNode) {
            feedback.remove();
        }
    }, 3000);
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
    initializePlatformSelection();
});
</script>