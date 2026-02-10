/**
 * Live Sport 250 - Enhanced Frontend Logic
 * Premium LiveScore-style functionality
 */

// Global State
const state = {
    currentTab: 'live',
    allMatches: {
        live: [],
        finished: [],
        scheduled: []
    },
    filteredMatches: [],
    favorites: JSON.parse(localStorage.getItem('favorites') || '[]'),
    selectedLeagues: [],
    searchQuery: '',
    viewMode: 'grid',
    updateInterval: 30,
    countdown: 30
};

// DOM Elements
const elements = {
    // Tabs
    tabButtons: document.querySelectorAll('.tab-btn'),
    tabContents: document.querySelectorAll('.tab-content'),

    // Containers
    liveContainer: document.getElementById('live-matches'),
    finishedContainer: document.getElementById('finished-matches'),
    scheduledContainer: document.getElementById('scheduled-matches'),

    // Search & Filters
    searchInput: document.getElementById('search-input'),
    clearSearch: document.getElementById('clear-search'),
    favoritesFilter: document.getElementById('favorites-filter'),
    leagueFilterBtn: document.getElementById('league-filter-btn'),
    leagueDropdown: document.getElementById('league-dropdown'),
    leagueList: document.getElementById('league-list'),
    clearLeagueFilter: document.getElementById('clear-league-filter'),

    // View Options
    viewButtons: document.querySelectorAll('.view-btn'),

    // Stats
    liveCount: document.getElementById('live-count'),
    liveBadge: document.getElementById('live-badge'),
    finishedBadge: document.getElementById('finished-badge'),
    scheduledBadge: document.getElementById('scheduled-badge'),
    updateTimer: document.getElementById('update-timer'),
    totalMatches: document.getElementById('total-matches'),
    totalLeagues: document.getElementById('total-leagues'),

    // Modal
    modal: document.getElementById('match-modal'),
    modalOverlay: document.getElementById('modal-overlay'),
    modalClose: document.getElementById('modal-close'),

    // FAB
    refreshFab: document.getElementById('refresh-fab'),

    // Templates
    matchTemplate: document.getElementById('match-template'),
    emptyTemplate: document.getElementById('empty-state-template')
};

// Initialize App
document.addEventListener('DOMContentLoaded', () => {
    initializeEventListeners();
    fetchAllMatches();
    startCountdown();
});

// Event Listeners
function initializeEventListeners() {
    // Tab switching
    elements.tabButtons.forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    // Search
    elements.searchInput.addEventListener('input', handleSearch);
    elements.clearSearch.addEventListener('click', clearSearch);

    // Filters
    elements.favoritesFilter.addEventListener('click', toggleFavoritesFilter);
    elements.leagueFilterBtn.addEventListener('click', toggleLeagueDropdown);
    elements.clearLeagueFilter.addEventListener('click', clearLeagueFilter);

    // View mode
    elements.viewButtons.forEach(btn => {
        btn.addEventListener('click', () => switchView(btn.dataset.view));
    });

    // Modal
    elements.modalClose.addEventListener('click', closeModal);
    elements.modalOverlay.addEventListener('click', closeModal);

    // FAB
    elements.refreshFab.addEventListener('click', () => {
        elements.refreshFab.querySelector('i').style.animation = 'spin 1s linear';
        setTimeout(() => {
            elements.refreshFab.querySelector('i').style.animation = '';
        }, 1000);
        fetchAllMatches();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.league-filter-wrapper')) {
            elements.leagueDropdown.classList.remove('active');
        }
    });
}

// Fetch Functions
async function fetchAllMatches() {
    try {
        // Fetch all match types
        const [liveData, finishedData, scheduledData] = await Promise.all([
            fetchMatchesByType('live'),
            fetchMatchesByType('finished'),
            fetchMatchesByType('scheduled')
        ]);

        state.allMatches.live = liveData;
        state.allMatches.finished = finishedData;
        state.allMatches.scheduled = scheduledData;

        updateStats();
        updateLeagueFilter();
        renderCurrentTab();

    } catch (error) {
        console.error('Error fetching matches:', error);
        showError('Failed to load matches. Please try again.');
    }
}

async function fetchMatchesByType(type) {
    try {
        const response = await fetch(`get_live_scores.php?type=${type}`);
        const result = await response.json();

        if (result.success) {
            return result.data || [];
        } else {
            console.error(`Error fetching ${type} matches:`, result.message);
            return [];
        }
    } catch (error) {
        console.error(`Network error fetching ${type} matches:`, error);
        return [];
    }
}

// Tab Management
function switchTab(tabName) {
    state.currentTab = tabName;

    // Update tab buttons
    elements.tabButtons.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tabName);
    });

    // Update tab contents
    elements.tabContents.forEach(content => {
        content.classList.toggle('active', content.id === `${tabName}-content`);
    });

    renderCurrentTab();
}

// Search & Filter Functions
function handleSearch(e) {
    state.searchQuery = e.target.value.toLowerCase().trim();

    // Show/hide clear button
    elements.clearSearch.style.display = state.searchQuery ? 'block' : 'none';

    renderCurrentTab();
}

function clearSearch() {
    state.searchQuery = '';
    elements.searchInput.value = '';
    elements.clearSearch.style.display = 'none';
    renderCurrentTab();
}

function toggleFavoritesFilter() {
    elements.favoritesFilter.classList.toggle('active');
    renderCurrentTab();
}

function toggleLeagueDropdown(e) {
    e.stopPropagation();
    elements.leagueDropdown.classList.toggle('active');
}

function clearLeagueFilter() {
    state.selectedLeagues = [];
    updateLeagueFilter();
    renderCurrentTab();
}

function updateLeagueFilter() {
    // Get all unique leagues from current matches
    const allLeagues = new Set();
    Object.values(state.allMatches).flat().forEach(match => {
        if (match.league && match.league.name) {
            allLeagues.add(JSON.stringify({
                id: match.league.id,
                name: match.league.name,
                logo: match.league.logo
            }));
        }
    });

    // Render league list
    elements.leagueList.innerHTML = '';
    Array.from(allLeagues).map(JSON.parse).sort((a, b) => a.name.localeCompare(b.name)).forEach(league => {
        const item = document.createElement('div');
        item.className = 'league-item';
        item.dataset.leagueId = league.id;

        if (state.selectedLeagues.includes(league.id)) {
            item.classList.add('active');
        }

        item.innerHTML = `
            <img src="${league.logo}" alt="${league.name}" onerror="this.style.display='none'">
            <span>${league.name}</span>
        `;

        item.addEventListener('click', () => {
            const leagueId = parseInt(item.dataset.leagueId);
            const index = state.selectedLeagues.indexOf(leagueId);

            if (index > -1) {
                state.selectedLeagues.splice(index, 1);
                item.classList.remove('active');
            } else {
                state.selectedLeagues.push(leagueId);
                item.classList.add('active');
            }

            updateLeagueFilterButton();
            renderCurrentTab();
        });

        elements.leagueList.appendChild(item);
    });
}

function updateLeagueFilterButton() {
    const count = state.selectedLeagues.length;
    const btnText = elements.leagueFilterBtn.querySelector('span');

    if (count === 0) {
        btnText.textContent = 'All Leagues';
        elements.leagueFilterBtn.classList.remove('active');
    } else {
        btnText.textContent = `${count} League${count > 1 ? 's' : ''}`;
        elements.leagueFilterBtn.classList.add('active');
    }
}

// View Mode
function switchView(viewMode) {
    state.viewMode = viewMode;

    elements.viewButtons.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === viewMode);
    });

    // Update containers
    [elements.liveContainer, elements.finishedContainer, elements.scheduledContainer].forEach(container => {
        if (viewMode === 'list') {
            container.classList.add('list-view');
        } else {
            container.classList.remove('list-view');
        }
    });
}

// Render Functions
function renderCurrentTab() {
    const matches = state.allMatches[state.currentTab];
    const filtered = filterMatches(matches);

    const container = state.currentTab === 'live' ? elements.liveContainer :
        state.currentTab === 'finished' ? elements.finishedContainer :
            elements.scheduledContainer;

    if (filtered.length === 0) {
        renderEmptyState(container);
    } else {
        renderMatches(container, filtered);
    }
}

function filterMatches(matches) {
    let filtered = [...matches];

    // Filter by search query
    if (state.searchQuery) {
        filtered = filtered.filter(match => {
            const homeTeam = match.teams.home.name.toLowerCase();
            const awayTeam = match.teams.away.name.toLowerCase();
            const league = match.league ? match.league.name.toLowerCase() : '';

            return homeTeam.includes(state.searchQuery) ||
                awayTeam.includes(state.searchQuery) ||
                league.includes(state.searchQuery);
        });
    }

    // Filter by favorites
    if (elements.favoritesFilter.classList.contains('active')) {
        filtered = filtered.filter(match =>
            state.favorites.includes(match.fixture.id)
        );
    }

    // Filter by selected leagues
    if (state.selectedLeagues.length > 0) {
        filtered = filtered.filter(match =>
            match.league && state.selectedLeagues.includes(match.league.id)
        );
    }

    return filtered;
}

function renderMatches(container, matches) {
    container.innerHTML = '';

    matches.forEach(match => {
        const card = createMatchCard(match);
        container.appendChild(card);
    });
}

function createMatchCard(match) {
    const template = elements.matchTemplate.content.cloneNode(true);
    const card = template.querySelector('.match-card');

    // Set match ID
    card.dataset.matchId = match.fixture.id;

    // Favorite button
    const favoriteBtn = card.querySelector('.favorite-btn');
    favoriteBtn.dataset.matchId = match.fixture.id;

    if (state.favorites.includes(match.fixture.id)) {
        favoriteBtn.classList.add('active');
        favoriteBtn.querySelector('i').classList.remove('far');
        favoriteBtn.querySelector('i').classList.add('fas');
    }

    favoriteBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleFavorite(match.fixture.id);
    });

    // League
    const leagueLogo = card.querySelector('.league-logo');
    const leagueName = card.querySelector('.league-name');

    if (match.league) {
        leagueLogo.src = match.league.logo;
        leagueLogo.onerror = () => leagueLogo.style.display = 'none';
        leagueName.textContent = match.league.name;
    } else {
        leagueLogo.style.display = 'none';
        leagueName.textContent = 'Unknown League';
    }

    // Teams
    const homeTeam = card.querySelector('.home-team');
    const awayTeam = card.querySelector('.away-team');

    homeTeam.querySelector('.team-logo').src = match.teams.home.logo;
    homeTeam.querySelector('.team-logo').onerror = function () {
        this.src = 'https://media.api-sports.io/football/teams/unknown.png';
    };
    homeTeam.querySelector('.team-name').textContent = match.teams.home.name;

    awayTeam.querySelector('.team-logo').src = match.teams.away.logo;
    awayTeam.querySelector('.team-logo').onerror = function () {
        this.src = 'https://media.api-sports.io/football/teams/unknown.png';
    };
    awayTeam.querySelector('.team-name').textContent = match.teams.away.name;

    // Score
    card.querySelector('.home-score').textContent = match.goals.home ?? 0;
    card.querySelector('.away-score').textContent = match.goals.away ?? 0;

    // Match time/status
    const matchTime = card.querySelector('.match-time');
    const statusBadge = card.querySelector('.match-status-badge');
    const status = match.fixture.status.long;

    if (match.fixture.status.elapsed) {
        matchTime.textContent = match.fixture.status.elapsed + "'";
        matchTime.style.display = 'block';
    } else if (state.currentTab === 'scheduled' && match.fixture.date) {
        const date = new Date(match.fixture.date);
        matchTime.textContent = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        matchTime.style.display = 'block';
        matchTime.style.background = 'rgba(245, 158, 11, 0.1)';
        matchTime.style.color = 'var(--status-scheduled)';
    } else {
        matchTime.style.display = 'none';
    }

    // Status badge
    statusBadge.textContent = status;
    if (status.toLowerCase().includes('live') || status.toLowerCase().includes('in play') || status.toLowerCase().includes('1st half') || status.toLowerCase().includes('2nd half')) {
        statusBadge.classList.add('live');
    } else if (status.toLowerCase().includes('finished') || status.toLowerCase().includes('full time')) {
        statusBadge.classList.add('finished');
    } else {
        statusBadge.classList.add('scheduled');
    }

    // Details button
    card.querySelector('.match-details-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        showMatchDetails(match);
    });

    // Card click
    card.addEventListener('click', () => {
        showMatchDetails(match);
    });

    return card;
}

function renderEmptyState(container) {
    const template = elements.emptyTemplate.content.cloneNode(true);
    const emptyState = template.querySelector('.empty-state');

    const title = emptyState.querySelector('.empty-title');
    const message = emptyState.querySelector('.empty-message');

    if (state.searchQuery || elements.favoritesFilter.classList.contains('active') || state.selectedLeagues.length > 0) {
        title.textContent = 'No matches found';
        message.textContent = 'Try adjusting your filters or search query.';
    } else {
        if (state.currentTab === 'live') {
            title.textContent = 'No live matches';
            message.textContent = 'There are no matches currently in progress. Check back soon!';
        } else if (state.currentTab === 'finished') {
            title.textContent = 'No finished matches';
            message.textContent = 'No matches have finished today yet.';
        } else {
            title.textContent = 'No scheduled matches';
            message.textContent = 'No upcoming matches scheduled at the moment.';
        }
    }

    container.innerHTML = '';
    container.appendChild(emptyState);
}

// Favorites Management
function toggleFavorite(matchId) {
    const index = state.favorites.indexOf(matchId);

    if (index > -1) {
        state.favorites.splice(index, 1);
    } else {
        state.favorites.push(matchId);
    }

    localStorage.setItem('favorites', JSON.stringify(state.favorites));

    // Update UI
    const favoriteBtn = document.querySelector(`.favorite-btn[data-match-id="${matchId}"]`);
    if (favoriteBtn) {
        favoriteBtn.classList.toggle('active');
        const icon = favoriteBtn.querySelector('i');
        icon.classList.toggle('far');
        icon.classList.toggle('fas');
    }

    // Re-render if favorites filter is active
    if (elements.favoritesFilter.classList.contains('active')) {
        renderCurrentTab();
    }
}

// Modal Functions
function showMatchDetails(match) {
    // Populate modal
    const modalLeagueLogo = document.getElementById('modal-league-logo');
    const modalLeagueName = document.getElementById('modal-league-name');
    const modalStatus = document.getElementById('modal-status');

    if (match.league) {
        modalLeagueLogo.src = match.league.logo;
        modalLeagueLogo.onerror = () => modalLeagueLogo.style.display = 'none';
        modalLeagueName.textContent = match.league.name;
    }

    modalStatus.textContent = match.fixture.status.long;

    // Teams
    document.getElementById('modal-home-logo').src = match.teams.home.logo;
    document.getElementById('modal-home-name').textContent = match.teams.home.name;
    document.getElementById('modal-away-logo').src = match.teams.away.logo;
    document.getElementById('modal-away-name').textContent = match.teams.away.name;

    // Score
    document.getElementById('modal-home-score').textContent = match.goals.home ?? 0;
    document.getElementById('modal-away-score').textContent = match.goals.away ?? 0;

    // Time
    const modalTime = document.getElementById('modal-time');
    if (match.fixture.status.elapsed) {
        modalTime.textContent = match.fixture.status.elapsed + "'";
    } else if (match.fixture.date) {
        const date = new Date(match.fixture.date);
        modalTime.textContent = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        modalTime.style.background = 'rgba(245, 158, 11, 0.1)';
        modalTime.style.color = 'var(--status-scheduled)';
    } else {
        modalTime.textContent = 'Not started';
    }

    // Date & Venue
    if (match.fixture.date) {
        const date = new Date(match.fixture.date);
        document.getElementById('modal-date').textContent = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    document.getElementById('modal-venue').textContent = match.fixture.venue?.name || 'Venue TBA';

    // Show modal
    elements.modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    elements.modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Stats Update
function updateStats() {
    const liveCount = state.allMatches.live.length;
    const finishedCount = state.allMatches.finished.length;
    const scheduledCount = state.allMatches.scheduled.length;
    const totalCount = liveCount + finishedCount + scheduledCount;

    elements.liveCount.textContent = liveCount;
    elements.liveBadge.textContent = liveCount;
    elements.finishedBadge.textContent = finishedCount;
    elements.scheduledBadge.textContent = scheduledCount;
    elements.totalMatches.textContent = totalCount;

    // Count unique leagues
    const leagues = new Set();
    Object.values(state.allMatches).flat().forEach(match => {
        if (match.league) leagues.add(match.league.id);
    });
    elements.totalLeagues.textContent = leagues.size;
}

// Countdown Timer
function startCountdown() {
    setInterval(() => {
        state.countdown--;

        if (state.countdown <= 0) {
            state.countdown = state.updateInterval;
            fetchAllMatches();
        }

        elements.updateTimer.textContent = `Next update: ${state.countdown}s`;
    }, 1000);
}

// Error Display
function showError(message) {
    const containers = [elements.liveContainer, elements.finishedContainer, elements.scheduledContainer];

    containers.forEach(container => {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle empty-icon" style="color: var(--status-live);"></i>
                <h3 class="empty-title">Error Loading Matches</h3>
                <p class="empty-message">${message}</p>
            </div>
        `;
    });
}
