<?php
require_once 'functions.php';

header('Content-Type: application/json');

// Get match type from query parameter (default: live)
$type = isset($_GET['type']) ? $_GET['type'] : 'live';

// Fetch matches based on type
switch ($type) {
    case 'finished':
        $matches = fetchFinishedMatches();
        break;
    case 'scheduled':
        $matches = fetchScheduledMatches();
        break;
    case 'live':
    default:
        $matches = fetchLiveMatches();
        break;
}

if ($matches !== false) {
    echo json_encode([
        'success' => true,
        'data' => $matches,
        'count' => count($matches),
        'type' => $type,
        'timestamp' => time()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Failed to fetch {$type} matches.",
        'type' => $type
    ]);
}
?>
