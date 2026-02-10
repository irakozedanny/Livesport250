<?php
require_once 'config.php';

/**
 * Fetches live football matches from API-Football
 * 
 * @return array|false Returns the array of matches or false on failure
 */
function fetchLiveMatches() {
    // If API Key is still placeholder, return sample data for demonstration
    if (API_KEY === 'YOUR_API_KEY_HERE') {
        return getSampleData('live');
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://" . API_HOST . "/fixtures?live=all",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-apisports-key: " . API_KEY,
            "x-rapidapi-host: " . API_HOST
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        $data = json_decode($response, true);
        if (isset($data['response'])) {
            return $data['response'];
        }
        return false;
    }
}

/**
 * Fetches finished football matches from API-Football (today)
 * 
 * @return array|false Returns the array of matches or false on failure
 */
function fetchFinishedMatches() {
    if (API_KEY === 'YOUR_API_KEY_HERE') {
        return getSampleData('finished');
    }

    $today = date('Y-m-d');
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://" . API_HOST . "/fixtures?date={$today}&status=FT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-apisports-key: " . API_KEY,
            "x-rapidapi-host: " . API_HOST
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return false;
    } else {
        $data = json_decode($response, true);
        if (isset($data['response'])) {
            return $data['response'];
        }
        return false;
    }
}

/**
 * Fetches scheduled football matches from API-Football (today and tomorrow)
 * 
 * @return array|false Returns the array of matches or false on failure
 */
function fetchScheduledMatches() {
    if (API_KEY === 'YOUR_API_KEY_HERE') {
        return getSampleData('scheduled');
    }

    $today = date('Y-m-d');
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://" . API_HOST . "/fixtures?date={$today}&status=NS",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-apisports-key: " . API_KEY,
            "x-rapidapi-host: " . API_HOST
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return false;
    } else {
        $data = json_decode($response, true);
        if (isset($data['response'])) {
            return $data['response'];
        }
        return false;
    }
}

/**
 * Returns sample football data for demonstration when API key is missing
 */
function getSampleData($type = 'live') {
    $liveMatches = [
        [
            'fixture' => [
                'id' => 1,
                'date' => date('c'),
                'status' => ['long' => '2nd Half', 'elapsed' => 74],
                'venue' => ['name' => 'Old Trafford']
            ],
            'teams' => [
                'home' => ['name' => 'Manchester United', 'logo' => 'https://media.api-sports.io/football/teams/33.png'],
                'away' => ['name' => 'Liverpool', 'logo' => 'https://media.api-sports.io/football/teams/40.png']
            ],
            'goals' => ['home' => 2, 'away' => 1],
            'league' => ['id' => 39, 'name' => 'Premier League', 'logo' => 'https://media.api-sports.io/football/leagues/39.png']
        ],
        [
            'fixture' => [
                'id' => 2,
                'date' => date('c'),
                'status' => ['long' => 'Half Time', 'elapsed' => 45],
                'venue' => ['name' => 'Santiago Bernabéu']
            ],
            'teams' => [
                'home' => ['name' => 'Real Madrid', 'logo' => 'https://media.api-sports.io/football/teams/541.png'],
                'away' => ['name' => 'Barcelona', 'logo' => 'https://media.api-sports.io/football/teams/529.png']
            ],
            'goals' => ['home' => 0, 'away' => 0],
            'league' => ['id' => 140, 'name' => 'La Liga', 'logo' => 'https://media.api-sports.io/football/leagues/140.png']
        ],
        [
            'fixture' => [
                'id' => 3,
                'date' => date('c'),
                'status' => ['long' => '1st Half', 'elapsed' => 32],
                'venue' => ['name' => 'San Siro']
            ],
            'teams' => [
                'home' => ['name' => 'AC Milan', 'logo' => 'https://media.api-sports.io/football/teams/489.png'],
                'away' => ['name' => 'Inter Milan', 'logo' => 'https://media.api-sports.io/football/teams/505.png']
            ],
            'goals' => ['home' => 1, 'away' => 1],
            'league' => ['id' => 135, 'name' => 'Serie A', 'logo' => 'https://media.api-sports.io/football/leagues/135.png']
        ]
    ];

    $finishedMatches = [
        [
            'fixture' => [
                'id' => 4,
                'date' => date('c', strtotime('-2 hours')),
                'status' => ['long' => 'Match Finished', 'elapsed' => 90],
                'venue' => ['name' => 'Allianz Arena']
            ],
            'teams' => [
                'home' => ['name' => 'Bayern Munich', 'logo' => 'https://media.api-sports.io/football/teams/157.png'],
                'away' => ['name' => 'Dortmund', 'logo' => 'https://media.api-sports.io/football/teams/165.png']
            ],
            'goals' => ['home' => 3, 'away' => 2],
            'league' => ['id' => 78, 'name' => 'Bundesliga', 'logo' => 'https://media.api-sports.io/football/leagues/78.png']
        ],
        [
            'fixture' => [
                'id' => 5,
                'date' => date('c', strtotime('-3 hours')),
                'status' => ['long' => 'Match Finished', 'elapsed' => 90],
                'venue' => ['name' => 'Parc des Princes']
            ],
            'teams' => [
                'home' => ['name' => 'Paris Saint Germain', 'logo' => 'https://media.api-sports.io/football/teams/85.png'],
                'away' => ['name' => 'Marseille', 'logo' => 'https://media.api-sports.io/football/teams/81.png']
            ],
            'goals' => ['home' => 2, 'away' => 0],
            'league' => ['id' => 61, 'name' => 'Ligue 1', 'logo' => 'https://media.api-sports.io/football/leagues/61.png']
        ]
    ];

    $scheduledMatches = [
        [
            'fixture' => [
                'id' => 6,
                'date' => date('c', strtotime('+2 hours')),
                'status' => ['long' => 'Not Started', 'elapsed' => null],
                'venue' => ['name' => 'Stamford Bridge']
            ],
            'teams' => [
                'home' => ['name' => 'Chelsea', 'logo' => 'https://media.api-sports.io/football/teams/49.png'],
                'away' => ['name' => 'Arsenal', 'logo' => 'https://media.api-sports.io/football/teams/42.png']
            ],
            'goals' => ['home' => null, 'away' => null],
            'league' => ['id' => 39, 'name' => 'Premier League', 'logo' => 'https://media.api-sports.io/football/leagues/39.png']
        ],
        [
            'fixture' => [
                'id' => 7,
                'date' => date('c', strtotime('+4 hours')),
                'status' => ['long' => 'Not Started', 'elapsed' => null],
                'venue' => ['name' => 'Camp Nou']
            ],
            'teams' => [
                'home' => ['name' => 'Atletico Madrid', 'logo' => 'https://media.api-sports.io/football/teams/530.png'],
                'away' => ['name' => 'Sevilla', 'logo' => 'https://media.api-sports.io/football/teams/536.png']
            ],
            'goals' => ['home' => null, 'away' => null],
            'league' => ['id' => 140, 'name' => 'La Liga', 'logo' => 'https://media.api-sports.io/football/leagues/140.png']
        ]
    ];

    switch ($type) {
        case 'live':
            return $liveMatches;
        case 'finished':
            return $finishedMatches;
        case 'scheduled':
            return $scheduledMatches;
        default:
            return $liveMatches;
    }
}
?>
