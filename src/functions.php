<?php
// src/functions.php

/**
 * Sanitasi input secara sederhana
 */
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper Pagination
 * @param PDO $pdo
 * @param int $user_id
 * @param int $page
 * @param int $perPage
 * @return array ['tasks' => array, 'total' => int, 'pages' => int]
 */
function get_tasks_paginated($pdo, $user_id, $page = 1, $perPage = 5, $filter_status = '', $sort_by = 'created_at', $sort_order = 'DESC') {
    // Whitelist sorting
    $allowed_sorts = ['created_at', 'due_date', 'priority'];
    if (!in_array($sort_by, $allowed_sorts)) {
        $sort_by = 'created_at';
    }
    $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

    // Hitung offset
    $offset = ($page - 1) * $perPage;
    
    // Base query
    $sql = "SELECT * FROM tasks WHERE user_id = :uid";
    $params = [':uid' => $user_id];
    
    // Filtering
    if (!empty($filter_status) && in_array($filter_status, ['todo', 'doing', 'done'])) {
        $sql .= " AND status = :status";
        $params[':status'] = $filter_status;
    }

    // Sorting
    if ($sort_by === 'priority') {
        // High > Medium > Low
        $sql .= " ORDER BY CASE priority 
                  WHEN 'high' THEN 1 
                  WHEN 'medium' THEN 2 
                  WHEN 'low' THEN 3 
                  ELSE 4 END $sort_order, created_at DESC";
    } else {
        $sql .= " ORDER BY $sort_by $sort_order";
    }

    $sql .= " LIMIT :lim OFFSET :off";
            
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    // Bind limit/offset specifically as INT
    $stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll();

    // 2. Ambil Total Baris (with filter)
    $countSql = "SELECT COUNT(*) FROM tasks WHERE user_id = :uid";
    $countParams = [':uid' => $user_id];
    
    if (!empty($filter_status)) {
         $countSql .= " AND status = :status";
         $countParams[':status'] = $filter_status;
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetchColumn();
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'data' => $tasks,
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'last_page' => $totalPages
    ];
}

/**
 * Helper Flash Message (untuk alert sederhana jika tidak pakai full JS)
 * Namun di sini kita akan mengandalkan return array ke view dan pakai JS.
 * Ini hanya helper untuk menyimpan pesan di session jika diperlukan.
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
