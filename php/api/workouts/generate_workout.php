<?php
declare(strict_types=1);
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

final class WorkoutGenerator {
    private mysqli $db;
    private const REST_SECONDS = ['beginner' => 30, 'intermediate' => 20, 'advanced' => 15];
    private const FALLBACK_REPS = ['beginner' => 12, 'intermediate' => 15, 'advanced' => 20];
    private const FALLBACK_DURATION = ['beginner' => 30, 'intermediate' => 45, 'advanced' => 60];
    private const BASE_SELECT = "SELECT id, exercise_name, type, description, image_url, met_value, muscle_group, equipment, difficulty, default_reps, default_duration FROM exercises";

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    public function generatePreset(string $difficulty, array $userProfile): array {
        $config = $this->getPresetConfig($difficulty);
        $target = $config['target'];
        $activity = strtolower($userProfile['activity_level'] ?? 'moderate');
        
        if (in_array($activity, ['active', 'very active'], true)) {
            $target += 2;
        }
        
        if (($userProfile['fitness_goal'] ?? '') === 'build_muscle' && $difficulty === 'advanced') {
            $target = 30;
        }

        $mainTarget = max(1, $target - $config['warmup'] - $config['cooldown']);
        $warmup = $this->fetchRandom(self::BASE_SELECT, ['type' => 'Warm-up'], $config['warmup']);
        $cooldown = $this->fetchRandom(self::BASE_SELECT, ['type' => 'Cool-down'], $config['cooldown']);
        
        $main = $this->fetchRandom(self::BASE_SELECT, [
            'type' => ['Strength', 'Cardio'],
            'difficulty' => $config['difficulties'],
            'equipment' => ['None']
        ], $mainTarget);

        $main = $this->ensureCount($main, $mainTarget);
        return $this->assignExecutionDetails(array_merge($warmup, $main, $cooldown), $difficulty);
    }

    public function generateCustom(array $equipment, ?string $muscleGroup, int $durationMinutes): array {
        if (!in_array('None', $equipment, true)) {
            $equipment[] = 'None';
        }
        
        // Custom workouts use 3 sets for strength, taking ~4-5 mins per exercise.
        // Warmup/Cooldown take ~3-5 mins total.
        $availableMins = max(2, $durationMinutes - 4);
        $mainTarget = max(1, (int)floor($availableMins / 4.5));
        
        $warmupCount = $durationMinutes >= 30 ? 3 : 2;
        $cooldownCount = ($durationMinutes >= 45) ? 2 : 1;
        
        $warmup = $this->fetchRandom(self::BASE_SELECT, ['type' => 'Warm-up'], $warmupCount);
        $cooldown = $this->fetchRandom(self::BASE_SELECT, ['type' => 'Cool-down'], $cooldownCount);
        
        $filters = ['type' => ['Strength', 'Cardio'], 'equipment' => $equipment];
        if ($muscleGroup) {
            $filters['muscle_group'] = $muscleGroup;
        }

        $main = $this->fetchRandom(self::BASE_SELECT, $filters, $mainTarget);
        $main = $this->ensureCount($main, $mainTarget);
        
        return $this->assignExecutionDetails(array_merge($warmup, $main, $cooldown), 'intermediate', true);
    }

    private function ensureCount(array $exercises, int $target): array {
        $count = count($exercises);
        if ($count === 0 || $count >= $target) return $exercises;
        for ($i = $count; $i < $target; $i++) {
            $exercises[] = $exercises[$i % $count];
        }
        shuffle($exercises);
        return $exercises;
    }

    private function fetchRandom(string $baseSql, array $criteria, int $limit): array {
        $params = []; $types = ''; $clauses = [];
        $equipmentFilter = null;
        
        foreach ($criteria as $field => $value) {
            if ($field === 'equipment') {
                $equipmentFilter = $value;
                continue;
            }
            if (is_array($value)) {
                $placeholders = implode(',', array_fill(0, count($value), '?'));
                $clauses[] = "$field IN ($placeholders)";
                foreach ($value as $v) { $params[] = $v; $types .= $this->paramType($v); }
            } else {
                $clauses[] = "$field = ?";
                $params[] = $value; $types .= $this->paramType($value);
            }
        }
        
        // Step 1: Fetch only IDs (and equipment if filtering is needed)
        $idCols = $equipmentFilter !== null ? "id, equipment" : "id";
        $idSql = "SELECT $idCols FROM exercises" . ($clauses ? ' WHERE ' . implode(' AND ', $clauses) : '');
        $rows = $this->executeFetch($idSql, $params, $types);
        
        if (empty($rows)) return [];
        
        // Step 2: Apply equipment filtering in PHP if required
        if ($equipmentFilter !== null) {
            $available = array_map(function($item) {
                $item = trim($item);
                if (strcasecmp($item, 'Dumbbell') === 0) return 'Dumbbells';
                return $item;
            }, $equipmentFilter);
            if (!in_array('None', $available, true)) {
                $available[] = 'None';
            }
            
            $filtered = array_filter($rows, function($row) use ($available) {
                $req = array_map('trim', explode(',', $row['equipment'] ?? 'None'));
                foreach ($req as $r) {
                    if (!in_array($r, $available, true)) {
                        return false;
                    }
                }
                return true;
            });
            $rows = array_values($filtered);
        }
        
        if (empty($rows)) return [];
        
        // Step 3: Pick random IDs
        $validIds = array_column($rows, 'id');
        shuffle($validIds);
        $selectedIds = array_slice($validIds, 0, $limit);
        
        // Step 4: Fetch full exercise data for selected IDs
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $finalSql = self::BASE_SELECT . " WHERE id IN ($placeholders)";
        $finalParams = $selectedIds;
        $finalTypes = str_repeat('i', count($selectedIds));
        
        return $this->executeFetch($finalSql, $finalParams, $finalTypes);
    }

    private function assignExecutionDetails(array $exercises, string $difficulty, bool $isCustom = false): array {
        return array_map(function (array $ex) use ($difficulty, $isCustom) {
            $isStrength = $ex['type'] === 'Strength';
            $ex['sets'] = ($isCustom && $isStrength) ? 3 : 1;
            $ex['rest_seconds'] = self::REST_SECONDS[$difficulty] ?? 20;
            
            if (!empty($ex['default_duration'])) {
                $ex['duration_seconds'] = (int)$ex['default_duration'];
                $ex['reps'] = null;
                return $ex;
            }
            if (!empty($ex['default_reps'])) {
                $scale = match ($difficulty) { 'advanced' => 1.5, 'intermediate' => 1.2, default => 1.0 };
                $ex['reps'] = (int)ceil((int)$ex['default_reps'] * $scale);
                $ex['duration_seconds'] = null;
                return $ex;
            }
            if ($isStrength) {
                $ex['reps'] = self::FALLBACK_REPS[$difficulty] ?? 12;
                $ex['duration_seconds'] = null;
            } else {
                $ex['duration_seconds'] = self::FALLBACK_DURATION[$difficulty] ?? 45;
                $ex['reps'] = null;
            }
            return $ex;
        }, $exercises);
    }

    private function getPresetConfig(string $type): array {
        return match ($type) {
            'advanced' => ['target' => 28, 'warmup' => 4, 'cooldown' => 4, 'difficulties' => ['Beginner', 'Intermediate', 'Advanced']],
            'intermediate' => ['target' => 20, 'warmup' => 3, 'cooldown' => 3, 'difficulties' => ['Beginner', 'Intermediate']],
            default => ['target' => 14, 'warmup' => 2, 'cooldown' => 2, 'difficulties' => ['Beginner']]
        };
    }

    private function executeFetch(string $sql, array $params, string $types): array {
        try {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new RuntimeException("DB Prepare Failed: " . $this->db->error);
            if ($params) $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) throw new RuntimeException("DB Execute Failed: " . $stmt->error);
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Throwable $e) {
            error_log('WorkoutGenerator Error: ' . $e->getMessage());
            return [];
        }
    }

    private function paramType(mixed $value): string { return is_int($value) ? 'i' : 's'; }
}

function getQueryInput(string $key, mixed $default = null): mixed {
    return isset($_GET[$key]) ? htmlspecialchars(strip_tags($_GET[$key])) : $default;
}

try {
    $conn = getVerifiedConnection();
    $userId = getRequiredUserId();
    $userProfile = [];
    
    $stmt = $conn->prepare("SELECT activity_level, fitness_goal FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userProfile = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();
    }

    $generator = new WorkoutGenerator($conn);
    $type = getQueryInput('type', 'beginner');
    
    if ($type === 'custom') {
        $rawEquip = getQueryInput('equipment', '');
        $equipment = $rawEquip ? array_filter(explode(',', $rawEquip)) : ['None'];
        $muscle = getQueryInput('muscle');
        if ($muscle === 'Full Body') $muscle = null;
        $duration = (int)getQueryInput('duration', 30);
        if ($duration < 5 || $duration > 180) throw new Exception("Duration must be between 5 and 180 minutes.");
        $workout = $generator->generateCustom($equipment, $muscle, $duration);
    } else {
        $workout = $generator->generatePreset($type, $userProfile);
    }

    if (empty($workout)) {
        sendJsonResponse('error', null, 'No exercises found matching criteria.');
    } else {
        sendJsonResponse('success', ['workout' => $workout, 'count' => count($workout)]);
    }
} catch (Throwable $e) {
    error_log("API Error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Internal Server Error');
} finally {
    if (isset($conn)) $conn->close();
}
?>
