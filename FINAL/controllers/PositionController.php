<?php
/**
 * PositionController - Position management (names and hourly rates).
 *
 * Request handling, data access and business logic for views/admin/position.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';

function getPositions($pdo) {
    $stmt = $pdo->query("SELECT * FROM positions ORDER BY position_name ASC");
    return $stmt->fetchAll();
}

function getPosition($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM positions WHERE position_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createPosition($pdo, $name, $rate) {
    $stmt = $pdo->prepare("INSERT INTO positions (position_name, hourly_rate) VALUES (?, ?)");
    return $stmt->execute([$name, $rate]);
}

function updatePosition($pdo, $id, $name, $rate) {
    $stmt = $pdo->prepare("UPDATE positions SET position_name = ?, hourly_rate = ? WHERE position_id = ?");
    return $stmt->execute([$name, $rate, $id]);
}

function deletePosition($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM positions WHERE position_id = ?");
    return $stmt->execute([$id]);
}

function searchPositions($pdo, $search) {
    $stmt = $pdo->prepare("SELECT * FROM positions WHERE position_name LIKE ? ORDER BY position_name ASC");
    $stmt->execute(['%' . $search . '%']);
    return $stmt->fetchAll();
}

function getPositionCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM positions");
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function tableExists($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'positions'");
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

$positions = [];
$search = '';
$message = '';
$messageType = '';
$tableExists = false;
$positionCount = 0;
$deleteId = null;

try {
    $pdo = getDBConnection();
    $tableExists = tableExists($pdo);

    if ($tableExists) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $submissionToken = $_POST['submission_token'] ?? '';
            $sessionToken = $_SESSION['last_submission_token'] ?? '';

            if ($submissionToken && $submissionToken !== $sessionToken) {
                $_SESSION['last_submission_token'] = $submissionToken;

                if ($action === 'create') {
                    $name = trim($_POST['position_name'] ?? '');
                    $rate = floatval($_POST['hourly_rate'] ?? 0);
                    if (!empty($name) && $rate > 0) {
                        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM positions WHERE position_name = ?");
                        $checkStmt->execute([$name]);
                        $exists = $checkStmt->fetchColumn();

                        if ($exists > 0) {
                            $message = 'Position "' . htmlspecialchars($name) . '" already exists!';
                            $messageType = 'error';
                        } else {
                            if (createPosition($pdo, $name, $rate)) {
                                $message = 'Position created successfully!';
                                $messageType = 'success';
                            } else {
                                $message = 'Failed to create position.';
                                $messageType = 'error';
                            }
                        }
                    } else {
                        $message = 'Please fill in all fields correctly.';
                        $messageType = 'error';
                    }
                } elseif ($action === 'update') {
                    $id = intval($_POST['position_id'] ?? 0);
                    $name = trim($_POST['position_name'] ?? '');
                    $rate = floatval($_POST['hourly_rate'] ?? 0);
                    if ($id > 0 && !empty($name) && $rate > 0) {
                        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM positions WHERE position_name = ? AND position_id != ?");
                        $checkStmt->execute([$name, $id]);
                        $exists = $checkStmt->fetchColumn();

                        if ($exists > 0) {
                            $message = 'Position "' . htmlspecialchars($name) . '" already exists!';
                            $messageType = 'error';
                        } else {
                            if (updatePosition($pdo, $id, $name, $rate)) {
                                $message = 'Position updated successfully!';
                                $messageType = 'success';
                            } else {
                                $message = 'Failed to update position.';
                                $messageType = 'error';
                            }
                        }
                    } else {
                        $message = 'Please fill in all fields correctly.';
                        $messageType = 'error';
                    }
                } elseif ($action === 'delete') {
                    $id = intval($_POST['position_id'] ?? 0);
                    if ($id > 0) {
                        if (deletePosition($pdo, $id)) {
                            $message = 'Position deleted successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to delete position.';
                            $messageType = 'error';
                        }
                    }
                }
            }
        }

        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = trim($_GET['search']);
            $positions = searchPositions($pdo, $search);
        } else {
            $positions = getPositions($pdo);
        }

        $positionCount = getPositionCount($pdo);

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $deleteId = intval($_GET['delete']);
        }
    }

} catch(PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
    $messageType = 'error';
}

$submissionToken = bin2hex(random_bytes(16));
$_SESSION['page_token'] = $submissionToken;
