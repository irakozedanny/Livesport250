<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Sport 250 - Live Football Scores & Results</title>
    <meta name="description" content="Get live football scores, results, and fixtures from leagues around the world. Real-time updates every 30 seconds.">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Animated Background -->
    <div class="background-gradient"></div>
    <div class="background-mesh"></div>
    
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="brand">
                    <div class="logo-container">
                        <img src="logo.png?v=<?php echo time(); ?>" alt="Live Sport 250 Logo" class="brand-logo">
                    </div>
                </div>
                
                <div class="header-actions">
                    <div class="stats-badge">
                        <i class="fas fa-chart-line"></i>
                        <div class="stats-info">
                            <span class="stats-label">Live Matches</span>
                            <span class="stats-value" id="live-count">0</span>
                        </div>
                    </div>
                    
                    <div class="refresh-status">
                        <div class="pulse-dot"></div>
                        <span id="update-timer">Next update: 30s</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Tabs -->
    <nav class="nav-tabs">
        <div class="container">
            <div class="tabs-wrapper">
                <div class="tabs-container">
                    <button class="tab-btn active" data-tab="live">
                        <i class="fas fa-circle-dot"></i>
                        <span>Live</span>
                        <span class="tab-badge" id="live-badge">0</span>
                    </button>
                    <button class="tab-btn" data-tab="finished">
                        <i class="fas fa-check-circle"></i>
                        <span>Finished</span>
                        <span class="tab-badge" id="finished-badge">0</span>
                    </button>
                    <button class="tab-btn" data-tab="scheduled">
                        <i class="fas fa-clock"></i>
                        <span>Scheduled</span>
                        <span class="tab-badge" id="scheduled-badge">0</span>
                    </button>
                </div>
                
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search-input" class="search-input" placeholder="Search teams or leagues...">
                    <button class="clear-search" id="clear-search" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Filters Bar -->
    <div class="filters-bar">
        <div class="container">
            <div class="filters-content">
                <div class="filter-group">
                    <button class="filter-btn" id="favorites-filter">
                        <i class="far fa-star"></i>
                        <span>Favorites</span>
                    </button>
                    
                    <div class="league-filter-wrapper">
                        <button class="filter-btn" id="league-filter-btn">
                            <i class="fas fa-trophy"></i>
                            <span>All Leagues</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="league-dropdown" id="league-dropdown">
                            <div class="league-dropdown-header">
                                <span>Filter by League</span>
                                <button class="clear-filter" id="clear-league-filter">Clear</button>
                            </div>
                            <div class="league-list" id="league-list">
                                <!-- Populated dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="view-options">
                    <button class="view-btn active" data-view="grid" title="Grid View">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn" data-view="list" title="List View">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Tab Content: Live -->
            <div class="tab-content active" id="live-content">
                <div class="matches-container" id="live-matches">
                    <div class="loading-state">
                        <div class="loader-spinner"></div>
                        <p>Loading live matches...</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Finished -->
            <div class="tab-content" id="finished-content">
                <div class="matches-container" id="finished-matches">
                    <div class="loading-state">
                        <div class="loader-spinner"></div>
                        <p>Loading finished matches...</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Scheduled -->
            <div class="tab-content" id="scheduled-content">
                <div class="matches-container" id="scheduled-matches">
                    <div class="loading-state">
                        <div class="loader-spinner"></div>
                        <p>Loading scheduled matches...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <i class="fas fa-futbol"></i>
                    <span>Live Sport 250</span>
                </div>
                <div class="footer-info">
                    <p>&copy; 2026 Live Sport 250. All rights reserved.</p>
                    <p class="footer-credit">Designed by Danny Pro 👨‍💻</p>
                </div>
                <div class="footer-stats" id="footer-stats">
                    <div class="stat-item">
                        <span class="stat-value" id="total-matches">0</span>
                        <span class="stat-label">Total Matches</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="total-leagues">0</span>
                        <span class="stat-label">Leagues</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Match Details Modal -->
    <div class="modal" id="match-modal">
        <div class="modal-overlay" id="modal-overlay"></div>
        <div class="modal-content">
            <button class="modal-close" id="modal-close">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-header">
                <div class="modal-league">
                    <img src="" alt="" class="modal-league-logo" id="modal-league-logo">
                    <span id="modal-league-name">Premier League</span>
                </div>
                <span class="modal-status" id="modal-status">Live</span>
            </div>
            
            <div class="modal-match">
                <div class="modal-team">
                    <img src="" alt="" class="modal-team-logo" id="modal-home-logo">
                    <h3 id="modal-home-name">Team Home</h3>
                </div>
                
                <div class="modal-score">
                    <div class="modal-score-display">
                        <span class="modal-score-value" id="modal-home-score">0</span>
                        <span class="modal-score-separator">:</span>
                        <span class="modal-score-value" id="modal-away-score">0</span>
                    </div>
                    <div class="modal-time" id="modal-time">45'</div>
                </div>
                
                <div class="modal-team">
                    <img src="" alt="" class="modal-team-logo" id="modal-away-logo">
                    <h3 id="modal-away-name">Team Away</h3>
                </div>
            </div>
            
            <div class="modal-info">
                <div class="info-row">
                    <i class="fas fa-calendar"></i>
                    <span id="modal-date">January 23, 2026</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="modal-venue">Stadium Name</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" id="refresh-fab" title="Refresh Now">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Match Card Template -->
    <template id="match-template">
        <div class="match-card" data-match-id="">
            <button class="favorite-btn" data-match-id="">
                <i class="far fa-star"></i>
            </button>
            
            <div class="match-league">
                <img src="" alt="" class="league-logo">
                <span class="league-name"></span>
            </div>
            
            <div class="match-body">
                <div class="team home-team">
                    <img src="" alt="" class="team-logo">
                    <h3 class="team-name"></h3>
                </div>
                
                <div class="match-center">
                    <div class="score-display">
                        <span class="score home-score">0</span>
                        <span class="score-sep">:</span>
                        <span class="score away-score">0</span>
                    </div>
                    <div class="match-time"></div>
                    <span class="match-status-badge"></span>
                </div>
                
                <div class="team away-team">
                    <img src="" alt="" class="team-logo">
                    <h3 class="team-name"></h3>
                </div>
            </div>
            
            <button class="match-details-btn">
                <span>Details</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </template>

    <!-- Empty State Template -->
    <template id="empty-state-template">
        <div class="empty-state">
            <i class="fas fa-inbox empty-icon"></i>
            <h3 class="empty-title"></h3>
            <p class="empty-message"></p>
        </div>
    </template>

    <!-- Custom JS -->
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
