<?php
/**
 * AttendanceController - Attendance management (list, filters, CRUD, bulk add, CSV import).
 *
 * Request handling, data access and business logic for views/admin/attendance.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

// Build WHERE clause shared by list + count queries
function buildAttendanceWhere($date = null, $employee_id = null, $date_from = null, $date_to = null, $employee_name = null) {
    $where = " WHERE 1=1";
    $params = [];

    if ($date) {
        $where .= " AND a.attendance_date = ?";
        $params[] = $date;
    }

    if ($date_from && $date_to) {
        $where .= " AND a.attendance_date BETWEEN ? AND ?";
        $params[] = $date_from;
        $params[] = $date_to;
    } elseif ($date_from) {
        $where .= " AND a.attendance_date >= ?";
        $params[] = $date_from;
    } elseif ($date_to) {
        $where .= " AND a.attendance_date <= ?";
        $params[] = $date_to;
    }

    if ($employee_id) {
        $where .= " AND a.employee_id = ?";
        $params[] = $employee_id;
    }

    if ($employee_name) {
        $where .= " AND (e.first_name LIKE ? OR e.last_name LIKE ? OR CONCAT(e.first_name, ' ', e.last_name) LIKE ?)";
        $searchTerm = "%{$employee_name}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    return [$where, $params];
}

function getAttendanceCount($pdo, $date = null, $employee_id = null, $date_from = null, $date_to = null, $employee_name = null) {
    list($where, $params) = buildAttendanceWhere($date, $employee_id, $date_from, $date_to, $employee_name);
    $sql = "SELECT COUNT(*) FROM attendance a INNER JOIN employees e ON a.employee_id = e.employee_id" . $where;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getAttendance($pdo, $date = null, $employee_id = null, $date_from = null, $date_to = null, $employee_name = null, $limit = null, $offset = 0) {
    list($where, $params) = buildAttendanceWhere($date, $employee_id, $date_from, $date_to, $employee_name);
    $sql = "SELECT a.*, e.first_name, e.last_name, e.employee_code FROM attendance a INNER JOIN employees e ON a.employee_id = e.employee_id" . $where;
    $sql .= " ORDER BY a.attendance_date DESC, e.last_name ASC";

    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAttendanceById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT a.*, e.first_name, e.last_name, e.employee_code FROM attendance a INNER JOIN employees e ON a.employee_id = e.employee_id WHERE a.attendance_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function checkDuplicate($pdo, $employee_id, $date, $exclude_id = null) {
    $sql = "SELECT attendance_id FROM attendance WHERE employee_id = ? AND attendance_date = ?";
    $params = [$employee_id, $date];
    if ($exclude_id) { $sql .= " AND attendance_id != ?"; $params[] = $exclude_id; }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function createAttendance($pdo, $employee_id, $date, $time_in, $time_out, $status, $remarks) {
    $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, attendance_date, time_in, time_out, status, remarks) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$employee_id, $date, $time_in, $time_out, $status, $remarks ?: null]);
}

function updateAttendance($pdo, $id, $employee_id, $date, $time_in, $time_out, $status, $remarks) {
    $stmt = $pdo->prepare("UPDATE attendance SET employee_id = ?, attendance_date = ?, time_in = ?, time_out = ?, status = ?, remarks = ? WHERE attendance_id = ?");
    return $stmt->execute([$employee_id, $date, $time_in, $time_out, $status, $remarks ?: null, $id]);
}

function deleteAttendance($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE attendance_id = ?");
    return $stmt->execute([$id]);
}

function getEmployees($pdo) {
    $stmt = $pdo->query("SELECT employee_id, first_name, last_name, employee_code FROM employees ORDER BY last_name, first_name");
    return $stmt->fetchAll();
}

function getEmployeesByName($pdo) {
    $stmt = $pdo->query("SELECT employee_id, first_name, last_name, employee_code FROM employees");
    $result = [];
    while ($row = $stmt->fetch()) {
        $fullName = strtolower(trim($row['first_name'] . ' ' . $row['last_name']));
        $result[$fullName] = $row;
        $result[strtolower($row['first_name'])] = $row;
        $result[strtolower($row['last_name'])] = $row;
    }
    return $result;
}

function getAttendanceStats($pdo, $date = null, $date_from = null, $date_to = null, $employee_id = null) {
    $sql = "SELECT COUNT(*) as total, 
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present, 
            SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent, 
            SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late, 
            SUM(CASE WHEN status = 'Half Day' THEN 1 ELSE 0 END) as half_day 
            FROM attendance WHERE 1=1";
    $params = [];

    if ($employee_id) {
        $sql .= " AND employee_id = ?";
        $params[] = $employee_id;
    }

    if ($date) {
        $sql .= " AND attendance_date = ?";
        $params[] = $date;
    } elseif ($date_from && $date_to) {
        $sql .= " AND attendance_date BETWEEN ? AND ?";
        $params[] = $date_from;
        $params[] = $date_to;
    } elseif ($date_from) {
        $sql .= " AND attendance_date >= ?";
        $params[] = $date_from;
    } elseif ($date_to) {
        $sql .= " AND attendance_date <= ?";
        $params[] = $date_to;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

// Employee card shown above the records when the list is filtered to ONE employee (picture + details).
// Never loads the image bytes: the picture is streamed by employee_photo.php.
function getEmployeeProfile($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT " . employeeColumnsNoPhoto($pdo, 'employees') . ", UNIX_TIMESTAMP(employees.photo_updated) AS photo_v FROM employees WHERE employee_id = ? LIMIT 1");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // photo columns not created yet (Employees page not opened since the update): load without a picture
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE employee_id = ? LIMIT 1");
        $stmt->execute([$id]);
    }
    return $stmt->fetch() ?: null;
}

// If the Name box matches exactly one employee, return that employee's id (otherwise null).
function findSingleEmployeeByName($employees, $term) {
    $term = strtolower(trim($term));
    if ($term === '') return null;
    $hits = [];
    foreach ($employees as $e) {
        $full1 = strtolower($e['first_name'] . ' ' . $e['last_name']);
        $full2 = strtolower($e['last_name'] . ' ' . $e['first_name']);
        if (strpos($full1, $term) !== false || strpos($full2, $term) !== false) $hits[] = (int)$e['employee_id'];
    }
    return count($hits) === 1 ? $hits[0] : null;
}

function tableExists($pdo) {
    try { $stmt = $pdo->query("SHOW TABLES LIKE 'attendance'"); return $stmt->rowCount() > 0; }
    catch(PDOException $e) { return false; }
}

function getAttendanceForDate($pdo, $date) {
    $stmt = $pdo->prepare("SELECT employee_id FROM attendance WHERE attendance_date = ?");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function cleanTimeString($timeStr) {
    if (empty($timeStr)) return null;
    $timeStr = preg_replace('/\s*\([^)]*\)\s*/', '', trim($timeStr));
    if (empty($timeStr)) return null;
    return $timeStr;
}

function convertTimeTo24Hour($timeStr) {
    if (empty($timeStr)) return null;
    $timeStr = cleanTimeString($timeStr);
    if (empty($timeStr)) return null;
    $timestamp = strtotime($timeStr);
    if ($timestamp !== false) return date('H:i:s', $timestamp);
    $timestamp = strtotime($timeStr . ' AM');
    if ($timestamp !== false) return date('H:i:s', $timestamp);
    $timestamp = strtotime($timeStr . ' PM');
    if ($timestamp !== false) return date('H:i:s', $timestamp);
    return null;
}

function determineStatus($time_in, $time_out, $default = 'Present') {
    if (empty($time_in) && empty($time_out)) return 'Absent';
    if (!empty($time_in)) {
        $timeInTimestamp = strtotime($time_in);
        $lateThreshold = strtotime('08:30:00');
        if ($timeInTimestamp > $lateThreshold) return 'Late';
    }
    if (!empty($time_in) && !empty($time_out)) {
        $start = strtotime($time_in);
        $end = strtotime($time_out);
        $diff = ($end - $start) / 3600;
        if ($diff < 4 && $diff > 0) return 'Half Day';
    }
    return $default;
}

function importAttendanceCSV($pdo, $file, $default_date = null) {
    $results = ['success' => 0, 'skipped' => 0, 'failed' => 0, 'errors' => []];
    $employeesByName = getEmployeesByName($pdo);

    if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
        $header = fgetcsv($handle);
        if (!$header) { $results['errors'][] = 'Invalid CSV file format'; return $results; }

        $header = array_map(function($col) { return strtolower(trim(str_replace(' ', '', $col))); }, $header);
        $columnMap = [];

        foreach ($header as $index => $col) {
            $colLower = strtolower($col);
            if (in_array($colLower, ['name', 'fullname', 'employee', 'employeename', 'employee_name'])) $columnMap['name'] = $index;
            elseif (in_array($colLower, ['date', 'day', 'attdate', 'attendance_date', 'attendance-date'])) $columnMap['date'] = $index;
            elseif (in_array($colLower, ['timein', 'time_in', 'time-in', 'in', 'time'])) $columnMap['timein'] = $index;
            elseif (in_array($colLower, ['timeout', 'time_out', 'time-out', 'out', 'timeout'])) $columnMap['timeout'] = $index;
        }

        if (!isset($columnMap['name'])) {
            $results['errors'][] = 'Required column "name" is missing. Found: ' . implode(', ', $header);
            fclose($handle); return $results;
        }

        if (!isset($columnMap['date'])) $columnMap['date'] = null;
        if (!isset($columnMap['timein'])) $columnMap['timein'] = null;
        if (!isset($columnMap['timeout'])) $columnMap['timeout'] = null;

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (empty(array_filter($row))) continue;

            try {
                $employee_name = trim($row[$columnMap['name']]);
                $attendance_date = $default_date ?: date('Y-m-d');

                if ($columnMap['date'] !== null && isset($row[$columnMap['date']]) && !empty($row[$columnMap['date']])) {
                    $dateStr = trim($row[$columnMap['date']]);
                    $dateParsed = false;

                    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateStr, $matches)) {
                        $day = intval($matches[1]); $month = intval($matches[2]); $year = intval($matches[3]);
                        if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 1900 && $year <= 2100) {
                            if ($day > 12) $dateParsed = mktime(0, 0, 0, $month, $day, $year);
                            else if ($month > 12) $dateParsed = mktime(0, 0, 0, $day, $month, $year);
                            else {
                                $dateParsed = mktime(0, 0, 0, $month, $day, $year);
                                if (date('d', $dateParsed) != $day || date('m', $dateParsed) != $month) {
                                    $dateParsed2 = mktime(0, 0, 0, $day, $month, $year);
                                    if (date('d', $dateParsed2) == $month && date('m', $dateParsed2) == $day) $dateParsed = $dateParsed2;
                                }
                            }
                        }
                    }

                    if ($dateParsed === false) $dateParsed = strtotime($dateStr);
                    if ($dateParsed === false || $dateParsed === -1) {
                        $dateStr2 = str_replace('/', '-', $dateStr);
                        $dateParsed = strtotime($dateStr2);
                    }

                    if ($dateParsed !== false && $dateParsed !== -1) {
                        $attendance_date = date('Y-m-d', $dateParsed);
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Row $rowNumber: Invalid date '$dateStr'";
                        continue;
                    }
                }

                $time_in = null; $time_out = null;
                if ($columnMap['timein'] !== null && isset($row[$columnMap['timein']]) && !empty($row[$columnMap['timein']])) {
                    $time_in = convertTimeTo24Hour($row[$columnMap['timein']]);
                }
                if ($columnMap['timeout'] !== null && isset($row[$columnMap['timeout']]) && !empty($row[$columnMap['timeout']])) {
                    $time_out = convertTimeTo24Hour($row[$columnMap['timeout']]);
                }

                if (empty($employee_name)) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Employee name is empty";
                    continue;
                }

                $employee_name_lower = strtolower(trim($employee_name));
                $employee = null;

                if (isset($employeesByName[$employee_name_lower])) {
                    $employee = $employeesByName[$employee_name_lower];
                } else {
                    foreach ($employeesByName as $key => $emp) {
                        if (strpos($key, $employee_name_lower) !== false || strpos($employee_name_lower, $key) !== false) {
                            $employee = $emp; break;
                        }
                    }
                }

                if (!$employee) {
                    $nameParts = explode(' ', $employee_name_lower);
                    foreach ($employeesByName as $key => $emp) {
                        $empFull = strtolower($emp['first_name'] . ' ' . $emp['last_name']);
                        $matchCount = 0;
                        foreach ($nameParts as $part) {
                            if (strlen($part) > 1 && strpos($empFull, $part) !== false) $matchCount++;
                        }
                        if ($matchCount > 0 && $matchCount >= count($nameParts) / 2) { $employee = $emp; break; }
                    }
                }

                if (!$employee) {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Employee '$employee_name' not found";
                    continue;
                }

                $employee_id = $employee['employee_id'];
                $status = determineStatus($time_in, $time_out, 'Present');
                if ($status === 'Absent') { $time_in = null; $time_out = null; }

                $duplicate = checkDuplicate($pdo, $employee_id, $attendance_date);
                if ($duplicate) { $results['skipped']++; continue; }

                $remarks = "Imported from CSV (Row $rowNumber)";
                if (createAttendance($pdo, $employee_id, $attendance_date, $time_in, $time_out, $status, $remarks)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Row $rowNumber: Failed to insert";
                }
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row $rowNumber: " . $e->getMessage();
            }
        }
        fclose($handle);
    } else {
        $results['errors'][] = 'Failed to open file';
    }

    return $results;
}

// ---------------------------------------------------------------
// TIME-CLOCK EXCEL IMPORT (.xls "Attendance Record" export)
// Layout: header row has "Employee ID | Card No. | Name | Department | 2026/10/01 | 2026/10/02 ...".
// Each day cell holds up to 4 "SW EW" lines, e.g. "08:15 17:37\n--:-- --:--\n..." (--:-- = no punch).
// ---------------------------------------------------------------
function normalizePersonName($name) {
    $name = strtolower(trim($name));
    $name = preg_replace('/[^a-z\s]/', ' ', $name);
    $tokens = array_values(array_filter(preg_split('/\s+/', $name)));
    sort($tokens);
    return $tokens;
}

// Safe name matcher: exact (any word order) first, then a UNIQUE containment match. Never guesses between people.
function findEmployeeForXls($name, $employeeList) {
    $want = normalizePersonName($name);
    if (count($want) === 0) return null;
    foreach ($employeeList as $emp) {
        if ($emp['tokens'] === $want) return $emp;
    }
    $candidates = [];
    foreach ($employeeList as $emp) {
        $small = count($want) <= count($emp['tokens']) ? $want : $emp['tokens'];
        $big   = count($want) <= count($emp['tokens']) ? $emp['tokens'] : $want;
        if (count($small) >= 2 && count(array_diff($small, $big)) === 0) $candidates[] = $emp;
    }
    return count($candidates) === 1 ? $candidates[0] : null;
}

// Turn one day cell into [time_in, time_out] (24h "HH:MM:SS" or null). First valid SW = in, last valid EW = out.
function parseClockCell($cell) {
    $in = null; $out = null;
    if (preg_match_all('/(\d{1,2}:\d{2}|--:--)\s+(\d{1,2}:\d{2}|--:--)/', (string)$cell, $m, PREG_SET_ORDER)) {
        foreach ($m as $pair) {
            if ($pair[1] !== '--:--' && $in === null) $in = str_pad($pair[1], 5, '0', STR_PAD_LEFT) . ':00';
            if ($pair[2] !== '--:--') $out = str_pad($pair[2], 5, '0', STR_PAD_LEFT) . ':00';
        }
    }
    return [$in, $out];
}

// Match by the clock's Employee ID against employees.employee_code: exact, case-insensitive, no guessing.
function findEmployeeByClockId($clockId, $employeeList) {
    $id = strtolower(trim((string)$clockId));
    if ($id === '') return null;
    foreach ($employeeList as $emp) {
        if (strtolower(trim((string)$emp['employee_code'])) === $id) return $emp;
    }
    return null;
}

function importAttendanceXls($pdo, $file, $recordBlankWeekdaysAsAbsent = true) {
    $results = ['success' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0, 'errors' => [], 'unmatched' => [], 'warnings' => 0];

    try {
        require_once __DIR__ . '/XlsReader.php';
        $rows = XlsReader::readFirstSheet($file['tmp_name']);
    } catch (Exception $e) {
        $results['errors'][] = $e->getMessage();
        return $results;
    }

    // Find the header row (the one containing "Name" and at least one date column)
    $headerRow = null; $nameCol = null; $idCol = null; $dateCols = [];
    foreach ($rows as $r => $cols) {
        $tmpName = null; $tmpId = null; $tmpDates = [];
        foreach ($cols as $c => $val) {
            $v = strtolower(trim($val));
            if ($v === 'name') $tmpName = $c;
            if ($v === 'employee id') $tmpId = $c;
            if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', trim($val), $m)) {
                $tmpDates[$c] = sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
            }
        }
        if ($tmpName !== null && count($tmpDates) > 0) { $headerRow = $r; $nameCol = $tmpName; $idCol = $tmpId; $dateCols = $tmpDates; break; }
    }
    if ($headerRow === null) {
        $results['errors'][] = 'Could not find the header row (needs a "Name" column and date columns like 2026/10/01).';
        return $results;
    }

    if ($idCol === null) {
        $results['errors'][] = 'The file needs an "Employee ID" column (employees are matched by ID = Employee Code).';
        return $results;
    }

    // Employees (pre-tokenised names)
    $employeeList = [];
    foreach ($pdo->query("SELECT employee_id, first_name, last_name, employee_code FROM employees") as $e) {
        $e['tokens'] = normalizePersonName($e['first_name'] . ' ' . $e['last_name']);
        $employeeList[] = $e;
    }

    $today = date('Y-m-d');
    $existingStmt = $pdo->prepare("SELECT attendance_id, time_in, time_out, remarks FROM attendance WHERE employee_id = ? AND attendance_date = ?");
    $updateStmt = $pdo->prepare("UPDATE attendance SET time_in = ?, time_out = ?, status = ?, remarks = ? WHERE attendance_id = ?");
    $seenUnmatched = [];

    $pdo->beginTransaction();
    try {
        foreach ($rows as $r => $cols) {
            if ($r <= $headerRow) continue;
            $name = trim($cols[$nameCol] ?? '');
            if ($name === '') continue;

            $clockId = $idCol !== null ? trim($cols[$idCol] ?? '') : '';
            // Match ONLY by the clock's Employee ID = employees.employee_code (exact, so 001 and 00001 stay different people).
            $employee = $clockId !== '' ? findEmployeeByClockId($clockId, $employeeList) : null;
            if (!$employee) {
                if (!isset($seenUnmatched[$name])) { $seenUnmatched[$name] = true; $results['unmatched'][] = $name . ($clockId !== '' ? " (ID $clockId)" : ''); }
                continue;
            }
            $employee_id = $employee['employee_id'];

            // Read the whole row first. Overnight shifts (e.g. in 8:00 AM, out 6:00 AM next day) are punched as
            // an in on day 1 and an out-only on day 2: join them so the out belongs to the day the shift started.
            $dayPunches = [];
            foreach ($dateCols as $c => $d) $dayPunches[$d] = parseClockCell($cols[$c] ?? '');
            ksort($dayPunches);
            $skipDay = [];
            $dayKeys = array_keys($dayPunches);
            for ($k = 0; $k + 1 < count($dayKeys); $k++) {
                $d1 = $dayKeys[$k]; $d2 = $dayKeys[$k + 1];
                if ($dayPunches[$d1][0] !== null && $dayPunches[$d1][1] === null
                    && $dayPunches[$d2][0] === null && $dayPunches[$d2][1] !== null
                    && $dayPunches[$d2][1] <= '06:00:00'
                    && date('Y-m-d', strtotime($d1 . ' +1 day')) === $d2) {
                    $dayPunches[$d1][1] = $dayPunches[$d2][1];
                    $skipDay[$d2] = true;
                }
            }

            foreach ($dateCols as $c => $date) {
                if (!empty($skipDay[$date])) continue;
                list($time_in, $time_out) = $dayPunches[$date];
                $hasPunch = ($time_in !== null || $time_out !== null);
                $dow = (int)date('N', strtotime($date));      // 6 = Sat, 7 = Sun

                if (!$hasPunch) {
                    // No punches: only mark Absent on past weekdays, and only if the option is on
                    if (!$recordBlankWeekdaysAsAbsent || $dow >= 6 || $date >= $today) continue;
                }

                $remarks = 'Imported from time clock';
                // Clocked in at or before 8:00 AM counts as 8:00 AM. (Before 5:00 AM is treated as a clock error: left as-is and flagged.)
                if ($time_in !== null && $time_in >= '05:00:00' && $time_in <= '08:00:00') {
                    if ($time_in !== '08:00:00') $remarks .= ' (in ' . substr($time_in, 0, 5) . ' counted as 08:00)';
                    $time_in = '08:00:00';
                }
                $status = $hasPunch ? determineStatus($time_in, $time_out, 'Present') : 'Absent';
                // Time clock rule: anything after 8:00 AM (8:01 and up) is Late and keeps its exact time.
                if ($status === 'Present' && $time_in !== null && $time_in > '08:00:00') $status = 'Late';
                // Leaving before 5:00 PM is noted as an early out (status/pay unchanged; hours already reflect it).
                if ($time_out !== null && $time_out < '17:00:00' && ($time_in === null || $time_out >= $time_in)) $remarks .= ' - early out (' . substr($time_out, 0, 5) . ')';
                if ($hasPunch && $time_in === null)  { $remarks .= ' - missing time-in, please review'; $results['warnings']++; }
                if ($hasPunch && $time_out === null && $date < $today) { $remarks .= ' - missing time-out, please review'; $results['warnings']++; }
                if ($time_in !== null && $time_in < '05:00:00') { $remarks .= ' - unusual time-in, please review'; $results['warnings']++; }

                $existingStmt->execute([$employee_id, $date]);
                $existing = $existingStmt->fetch();
                if ($existing) {
                    // Only refresh rows that an earlier time-clock import created (never overwrite manual edits),
                    // and only when the new data is more complete (e.g. time-out now available).
                    $fromClock = strpos((string)$existing['remarks'], 'Imported from time clock') === 0;
                    $moreComplete = $hasPunch && (($existing['time_in'] === null && $time_in !== null) || ($existing['time_out'] === null && $time_out !== null));
                    if ($fromClock && $moreComplete) {
                        $updateStmt->execute([$time_in, $time_out, $status, $remarks, $existing['attendance_id']]);
                        $results['updated']++;
                    } else {
                        $results['skipped']++;
                    }
                    continue;
                }

                if (createAttendance($pdo, $employee_id, $date, $time_in, $time_out, $status, $remarks)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "$name $date: failed to insert";
                }
            }
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $results['success'] = 0; $results['updated'] = 0;
        $results['errors'][] = 'Import cancelled, nothing was saved: ' . $e->getMessage();
    }
    return $results;
}

$attendanceRecords = [];
$employees = [];
$message = '';
$messageType = '';
$tableExists = false;
$filterDate = date('Y-m-d');
$filterEmployeeId = null;
$filterDateFrom = null;
$filterDateTo = null;
$filterEmployeeName = null;
$stats = null;
$profileEmployee = null;
$existingAttendance = [];
$importResults = null;

// --- PAGINATION ---
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? max(5, min(200, (int)$_GET['per_page'])) : 10;
$totalRecords = 0;
$totalPages = 1;

try {
    $pdo = getDBConnection();
    $tableExists = tableExists($pdo);

    if ($tableExists) {
        $employees = getEmployees($pdo);
        $existingAttendance = getAttendanceForDate($pdo, $filterDate);

        if (isset($_GET['date']) && !empty($_GET['date'])) {
            $filterDate = $_GET['date'];
            $existingAttendance = getAttendanceForDate($pdo, $filterDate);
        }
        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) $filterDateFrom = $_GET['date_from'];
        if (isset($_GET['date_to']) && !empty($_GET['date_to'])) $filterDateTo = $_GET['date_to'];
        if (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) $filterEmployeeId = intval($_GET['employee_id']);
        if (isset($_GET['employee_name']) && !empty($_GET['employee_name'])) $filterEmployeeName = trim($_GET['employee_name']);

        // CSV Import
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $csvDate = $_POST['csv_date'] ?? null;
            $uploadExt = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
            $isXls = ($uploadExt === 'xls');
            if ($isXls) {
                $importResults = importAttendanceXls($pdo, $_FILES['csv_file'], !empty($_POST['xls_blank_absent']));
            } else {
                $importResults = importAttendanceCSV($pdo, $_FILES['csv_file'], $csvDate);
            }
            $importLabel = $isXls ? 'Time clock import' : 'CSV Import';
            $importUpdated = $importResults['updated'] ?? 0;

            if ($importResults['success'] > 0 || $importUpdated > 0) {
                $message = "$importLabel completed: {$importResults['success']} records added";
                if ($importUpdated > 0) $message .= ", $importUpdated updated";
                if ($importResults['skipped'] > 0) $message .= ", {$importResults['skipped']} skipped (already exist)";
                if ($importResults['failed'] > 0) $message .= ", {$importResults['failed']} failed";
                if (!empty($importResults['warnings'])) $message .= ", {$importResults['warnings']} need review (see Remarks)";
                if (!empty($importResults['unmatched'])) $message .= ". Employees not found in system (skipped): " . implode(', ', $importResults['unmatched']);
                $messageType = ($importResults['failed'] > 0 || !empty($importResults['unmatched']) || !empty($importResults['warnings'])) ? 'warning' : 'success';

                if ($importResults['failed'] > 0 && !empty($importResults['errors'])) {
                    $errorCount = count($importResults['errors']);
                    $showErrors = array_slice($importResults['errors'], 0, 5);
                    $message .= ". Errors: " . implode('; ', $showErrors);
                    if ($errorCount > 5) $message .= " (+ " . ($errorCount - 5) . " more)";
                }
            } else {
                $message = "$importLabel failed: " . implode('; ', array_merge($importResults['errors'], !empty($importResults['unmatched']) ? ['Not found in system: ' . implode(', ', $importResults['unmatched'])] : []));
                if (empty($importResults['errors']) && empty($importResults['unmatched'])) $message = "$importLabel: nothing new to import (all records already exist).";
                $messageType = 'error';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['csv_file'])) {
            $action = $_POST['action'] ?? '';
            $submissionToken = $_POST['submission_token'] ?? '';
            $sessionToken = $_SESSION['last_submission_token'] ?? '';

            if ($submissionToken && $submissionToken !== $sessionToken) {
                $_SESSION['last_submission_token'] = $submissionToken;

                if ($action === 'create') {
                    $employee_id = intval($_POST['employee_id'] ?? 0);
                    $date = $_POST['attendance_date'] ?? '';
                    $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : null;
                    $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : null;
                    $status = $_POST['status'] ?? 'Present';
                    $remarks = trim($_POST['remarks'] ?? '');
                    if ($status === 'Absent') { $time_in = null; $time_out = null; }

                    if ($employee_id > 0 && !empty($date)) {
                        if ($status !== 'Absent' && (!$time_in || !$time_out)) {
                            $message = 'Please fill in Time In and Time Out for non-absent status.';
                            $messageType = 'error';
                        } else {
                            $duplicate = checkDuplicate($pdo, $employee_id, $date);
                            if ($duplicate) {
                                $message = 'Attendance already exists for this employee on ' . $date . '!';
                                $messageType = 'error';
                            } else {
                                if (createAttendance($pdo, $employee_id, $date, $time_in, $time_out, $status, $remarks)) {
                                    $message = 'Attendance recorded successfully!';
                                    $messageType = 'success';
                                    $filterDate = $date;
                                    $existingAttendance = getAttendanceForDate($pdo, $date);
                                }
                            }
                        }
                    } else {
                        $message = 'Please select an employee and date.';
                        $messageType = 'error';
                    }
                } elseif ($action === 'update') {
                    $id = intval($_POST['attendance_id'] ?? 0);
                    $employee_id = intval($_POST['employee_id'] ?? 0);
                    $date = $_POST['attendance_date'] ?? '';
                    $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : null;
                    $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : null;
                    $status = $_POST['status'] ?? 'Present';
                    $remarks = trim($_POST['remarks'] ?? '');
                    if ($status === 'Absent') { $time_in = null; $time_out = null; }

                    if ($id > 0 && $employee_id > 0 && !empty($date)) {
                        if ($status !== 'Absent' && (!$time_in || !$time_out)) {
                            $message = 'Please fill in Time In and Time Out for non-absent status.';
                            $messageType = 'error';
                        } else {
                            $duplicate = checkDuplicate($pdo, $employee_id, $date, $id);
                            if ($duplicate) {
                                $message = 'Another record already exists for this employee on ' . $date . '!';
                                $messageType = 'error';
                            } else {
                                if (updateAttendance($pdo, $id, $employee_id, $date, $time_in, $time_out, $status, $remarks)) {
                                    $message = 'Attendance updated successfully!';
                                    $messageType = 'success';
                                    $filterDate = $date;
                                    $existingAttendance = getAttendanceForDate($pdo, $date);
                                }
                            }
                        }
                    }
                } elseif ($action === 'delete') {
                    $id = intval($_POST['attendance_id'] ?? 0);
                    if ($id > 0 && deleteAttendance($pdo, $id)) {
                        $message = 'Attendance deleted successfully!';
                        $messageType = 'success';
                        $existingAttendance = getAttendanceForDate($pdo, $filterDate);
                    }
                } elseif ($action === 'bulk_create') {
                    $date = $_POST['attendance_date'] ?? '';
                    $status = $_POST['status'] ?? 'Present';
                    $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : '08:00';
                    $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : '17:00';
                    $employee_ids = $_POST['employee_ids'] ?? [];
                    if ($status === 'Absent') { $time_in = null; $time_out = null; }

                    if (!empty($date) && !empty($employee_ids)) {
                        $successCount = 0; $skipCount = 0;
                        foreach ($employee_ids as $emp_id) {
                            $emp_id = intval($emp_id);
                            if (!checkDuplicate($pdo, $emp_id, $date)) {
                                if (createAttendance($pdo, $emp_id, $date, $time_in, $time_out, $status, null)) $successCount++;
                            } else { $skipCount++; }
                        }
                        $msg = "Recorded for $successCount employees!";
                        if ($skipCount > 0) $msg .= " ($skipCount skipped - already exists)";
                        $message = $msg;
                        $messageType = 'success';
                        $filterDate = $date;
                        $existingAttendance = getAttendanceForDate($pdo, $date);
                    }
                }
            }
        }

        // --- TOTAL COUNT & PAGINATION BOUNDS ---
        $totalRecords = getAttendanceCount($pdo, null, $filterEmployeeId, $filterDateFrom, $filterDateTo, $filterEmployeeName);
        $totalPages = max(1, (int)ceil($totalRecords / $perPage));
        if ($page > $totalPages) $page = $totalPages;
        $offset = ($page - 1) * $perPage;

        // --- FETCH PAGINATED RECORDS ---
        $attendanceRecords = getAttendance($pdo, null, $filterEmployeeId, $filterDateFrom, $filterDateTo, $filterEmployeeName, $perPage, $offset);
        // Filtered to one employee (by the Employee box, or a Name that matches only one person)? -> show his profile card
        $profileId = $filterEmployeeId ?: ($filterEmployeeName ? findSingleEmployeeByName($employees, $filterEmployeeName) : null);
        if ($profileId) $profileEmployee = getEmployeeProfile($pdo, $profileId);
        $stats = getAttendanceStats($pdo, null, $filterDateFrom, $filterDateTo, $profileEmployee ? $profileId : null);
    }
} catch(PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
    $messageType = 'error';
}

$submissionToken = bin2hex(random_bytes(16));

// Helper to build pagination URLs preserving filters
function pageUrl($targetPage, $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName) {
    $params = ['page' => $targetPage, 'per_page' => $perPage];
    if ($filterDateFrom) $params['date_from'] = $filterDateFrom;
    if ($filterDateTo) $params['date_to'] = $filterDateTo;
    if ($filterEmployeeId) $params['employee_id'] = $filterEmployeeId;
    if ($filterEmployeeName) $params['employee_name'] = $filterEmployeeName;
    return 'attendance.php?' . http_build_query($params);
}
