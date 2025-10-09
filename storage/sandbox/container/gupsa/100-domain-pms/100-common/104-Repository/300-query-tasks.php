<?php

class TasksRepository
{
    private $dbPath;
    
    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../200-Database/release.sqlite';
    }
    
    public function getKanbanData($projectId)
    {
        $db = new SQLite3($this->dbPath);
        
        // 프로젝트 존재 확인
        $projectStmt = $db->prepare('SELECT id, name FROM projects WHERE id = ? AND deleted_at IS NULL');
        $projectStmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $projectResult = $projectStmt->execute();
        
        if (!$projectResult->fetchArray()) {
            return false;
        }
        
        // 태스크 조회
        $stmt = $db->prepare('SELECT * FROM tasks WHERE project_id = ? ORDER BY position ASC, created_at ASC');
        $stmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $kanbanData = [
            'todo' => [],
            'in_progress' => [],
            'review' => [],
            'done' => []
        ];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $status = $row['status'];
            if (isset($kanbanData[$status])) {
                $kanbanData[$status][] = $row;
            }
        }
        
        return [
            'project_id' => $projectId,
            'columns' => $kanbanData
        ];
    }
    
    public function create($projectId, $data)
    {
        $db = new SQLite3($this->dbPath);
        
        // 프로젝트 존재 확인
        $projectStmt = $db->prepare('SELECT id FROM projects WHERE id = ? AND deleted_at IS NULL');
        $projectStmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        if (!$projectStmt->execute()->fetchArray()) {
            return false;
        }
        
        $stmt = $db->prepare('INSERT INTO tasks (project_id, title, description, status, priority, assignee, due_date, estimated_hours, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $stmt->bindValue(2, $data['title'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(3, $data['description'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(4, $data['status'] ?? 'todo', SQLITE3_TEXT);
        $stmt->bindValue(5, $data['priority'] ?? 'medium', SQLITE3_TEXT);
        $stmt->bindValue(6, $data['assignee'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(7, $data['due_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(8, $data['estimated_hours'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(9, $data['position'] ?? 0, SQLITE3_INTEGER);
        $stmt->execute();
        
        return $db->lastInsertRowID();
    }
    
    public function findById($id)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        return $result->fetchArray(SQLITE3_ASSOC);
    }
    
    public function update($id, $data)
    {
        $db = new SQLite3($this->dbPath);
        
        // 태스크 존재 확인
        $checkStmt = $db->prepare('SELECT id FROM tasks WHERE id = ?');
        $checkStmt->bindValue(1, $id, SQLITE3_INTEGER);
        if (!$checkStmt->execute()->fetchArray()) {
            return false;
        }
        
        $stmt = $db->prepare('UPDATE tasks SET title = ?, description = ?, status = ?, priority = ?, assignee = ?, due_date = ?, estimated_hours = ?, position = ? WHERE id = ?');
        $stmt->bindValue(1, $data['title'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(2, $data['description'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(3, $data['status'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(4, $data['priority'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(5, $data['assignee'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(6, $data['due_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(7, $data['estimated_hours'] ?? null, SQLITE3_INTEGER);
        $stmt->bindValue(8, $data['position'] ?? null, SQLITE3_INTEGER);
        $stmt->bindValue(9, $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        return true;
    }
    
    public function updateStatus($id, $status, $position = 0)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('UPDATE tasks SET status = ?, position = ? WHERE id = ?');
        $stmt->bindValue(1, $status, SQLITE3_TEXT);
        $stmt->bindValue(2, $position, SQLITE3_INTEGER);
        $stmt->bindValue(3, $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        return true;
    }
    
    public function delete($id)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        return true;
    }
    
    public function getList($projectId, $perPage, $page, $status = null, $priority = null)
    {
        $db = new SQLite3($this->dbPath);
        
        $offset = ($page - 1) * $perPage;
        $whereClause = 'WHERE project_id = ?';
        $params = [$projectId];
        
        if ($status) {
            $whereClause .= ' AND status = ?';
            $params[] = $status;
        }
        
        if ($priority) {
            $whereClause .= ' AND priority = ?';
            $params[] = $priority;
        }
        
        $query = "SELECT * FROM tasks {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($query);
        
        $paramIndex = 1;
        foreach ($params as $param) {
            if ($paramIndex === 1) {
                $stmt->bindValue($paramIndex++, $param, SQLITE3_INTEGER);
            } else {
                $stmt->bindValue($paramIndex++, $param, SQLITE3_TEXT);
            }
        }
        $stmt->bindValue($paramIndex++, $perPage, SQLITE3_INTEGER);
        $stmt->bindValue($paramIndex, $offset, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        $tasks = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $tasks[] = $row;
        }
        
        $countQuery = "SELECT COUNT(*) FROM tasks {$whereClause}";
        $countStmt = $db->prepare($countQuery);
        $paramIndex = 1;
        foreach ($params as $param) {
            if ($paramIndex === 1) {
                $countStmt->bindValue($paramIndex++, $param, SQLITE3_INTEGER);
            } else {
                $countStmt->bindValue($paramIndex++, $param, SQLITE3_TEXT);
            }
        }
        $countResult = $countStmt->execute();
        $total = $countResult->fetchArray(SQLITE3_NUM)[0];
        
        return [
            'data' => $tasks,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function getGanttData($projectId)
    {
        $db = new SQLite3($this->dbPath);
        
        // 프로젝트 정보 조회
        $projectStmt = $db->prepare('SELECT id, name, start_date, end_date FROM projects WHERE id = ? AND deleted_at IS NULL');
        $projectStmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $projectResult = $projectStmt->execute();
        
        if (!$projectData = $projectResult->fetchArray(SQLITE3_ASSOC)) {
            return false;
        }
        
        // 태스크 조회
        $stmt = $db->prepare('SELECT id, title, status, assignee, due_date, estimated_hours, created_at FROM tasks WHERE project_id = ? ORDER BY created_at ASC');
        $stmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $tasks = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $tasks[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'status' => $row['status'],
                'assignee' => $row['assignee'],
                'start_date' => $row['created_at'],
                'end_date' => $row['due_date'],
                'estimated_hours' => $row['estimated_hours']
            ];
        }
        
        return [
            'project' => $projectData,
            'tasks' => $tasks
        ];
    }
    
    public function getCalendarData($projectId, $month)
    {
        $db = new SQLite3($this->dbPath);
        
        // 해당 월의 태스크 조회
        $stmt = $db->prepare("SELECT id, title, status, priority, assignee, due_date FROM tasks WHERE project_id = ? AND due_date LIKE ? ORDER BY due_date ASC");
        $stmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $stmt->bindValue(2, $month . '%', SQLITE3_TEXT);
        $result = $stmt->execute();
        
        $events = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($row['due_date']) {
                $events[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'date' => $row['due_date'],
                    'status' => $row['status'],
                    'priority' => $row['priority'],
                    'assignee' => $row['assignee']
                ];
            }
        }
        
        return [
            'events' => $events,
            'month' => $month
        ];
    }
    
    // 태스크 계층구조 트리 조회
    public function getTreeStructure($projectId)
    {
        $db = new SQLite3($this->dbPath);
        
        $query = "
            SELECT t1.*, 
                   (SELECT COUNT(*) FROM tasks t2 WHERE t2.parent_task_id = t1.id) as child_count
            FROM tasks t1 
            WHERE t1.project_id = ? 
            ORDER BY t1.path, t1.id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $projectId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $tasks = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $row['has_children'] = $row['child_count'] > 0;
            unset($row['child_count']);
            $tasks[] = $row;
        }
        
        return $this->buildTree($tasks);
    }
    
    // 하위 태스크 생성
    public function createChild($parentId, $data)
    {
        $db = new SQLite3($this->dbPath);
        
        // 부모 태스크 정보 조회
        $parentStmt = $db->prepare('SELECT depth, path, project_id FROM tasks WHERE id = ?');
        $parentStmt->bindValue(1, $parentId, SQLITE3_INTEGER);
        $parentResult = $parentStmt->execute();
        $parent = $parentResult->fetchArray(SQLITE3_ASSOC);
        
        if (!$parent) {
            throw new Exception('Parent task not found');
        }
        
        $childDepth = $parent['depth'] + 1;
        $childPath = $parent['path'] ? $parent['path'] . '/' . $parentId : (string)$parentId;
        
        $stmt = $db->prepare('
            INSERT INTO tasks (
                project_id, title, description, status, priority, assignee, 
                due_date, estimated_hours, position, parent_task_id, 
                depth, path, has_children, is_expanded, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, datetime("now"), datetime("now"))
        ');
        
        $stmt->bindValue(1, $parent['project_id'], SQLITE3_INTEGER);
        $stmt->bindValue(2, $data['title'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(3, $data['description'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(4, $data['status'] ?? 'todo', SQLITE3_TEXT);
        $stmt->bindValue(5, $data['priority'] ?? 'medium', SQLITE3_TEXT);
        $stmt->bindValue(6, $data['assignee'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(7, $data['due_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(8, $data['estimated_hours'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(9, $data['position'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(10, $parentId, SQLITE3_INTEGER);
        $stmt->bindValue(11, $childDepth, SQLITE3_INTEGER);
        $stmt->bindValue(12, $childPath, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to create child task: ' . $db->lastErrorMsg());
        }
        
        // 부모의 has_children 업데이트
        $updateParentStmt = $db->prepare('UPDATE tasks SET has_children = 1 WHERE id = ?');
        $updateParentStmt->bindValue(1, $parentId, SQLITE3_INTEGER);
        $updateParentStmt->execute();
        
        return $db->lastInsertRowID();
    }
    
    // 태스크 이동
    public function moveTask($id, $newParentId)
    {
        $db = new SQLite3($this->dbPath);
        
        // 이동할 태스크 조회
        $task = $this->findById($id);
        if (!$task) {
            return ['success' => false, 'message' => 'Task not found'];
        }
        
        // 새 부모 정보 조회 (null이면 루트로 이동)
        $newDepth = 0;
        $newPath = '';
        
        if ($newParentId) {
            $newParent = $this->findById($newParentId);
            if (!$newParent) {
                return ['success' => false, 'message' => 'New parent task not found'];
            }
            
            // 순환 참조 방지 (자기 자신이나 하위 태스크로는 이동 불가)
            if ($newParentId == $id || strpos($newParent['path'], $task['path'] . '/' . $id) === 0) {
                return ['success' => false, 'message' => 'Cannot move to descendant task'];
            }
            
            if ($newParent['depth'] >= 9) {
                return ['success' => false, 'message' => 'Maximum depth (10) would be exceeded'];
            }
            
            // 프로젝트가 같은지 확인
            if ($newParent['project_id'] != $task['project_id']) {
                return ['success' => false, 'message' => 'Cannot move task to different project'];
            }
            
            $newDepth = $newParent['depth'] + 1;
            $newPath = $newParent['path'] ? $newParent['path'] . '/' . $newParentId : (string)$newParentId;
        }
        
        // 태스크와 모든 하위 태스크의 depth와 path 업데이트
        $this->updateTaskHierarchy($id, $newParentId, $newDepth, $newPath);
        
        return ['success' => true, 'message' => 'Task moved successfully'];
    }
    
    // 확장/축소 토글
    public function toggleExpansion($id, $isExpanded)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('UPDATE tasks SET is_expanded = ? WHERE id = ?');
        $stmt->bindValue(1, $isExpanded ? 1 : 0, SQLITE3_INTEGER);
        $stmt->bindValue(2, $id, SQLITE3_INTEGER);
        
        return $stmt->execute();
    }
    
    // 태스크 계층구조 업데이트 (재귀적)
    private function updateTaskHierarchy($id, $newParentId, $newDepth, $newPath)
    {
        $db = new SQLite3($this->dbPath);
        
        // 현재 태스크 업데이트
        $stmt = $db->prepare('UPDATE tasks SET parent_task_id = ?, depth = ?, path = ? WHERE id = ?');
        $stmt->bindValue(1, $newParentId, $newParentId ? SQLITE3_INTEGER : SQLITE3_NULL);
        $stmt->bindValue(2, $newDepth, SQLITE3_INTEGER);
        $stmt->bindValue(3, $newPath, SQLITE3_TEXT);
        $stmt->bindValue(4, $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        // 하위 태스크들 조회 및 업데이트
        $childrenStmt = $db->prepare('SELECT id FROM tasks WHERE parent_task_id = ?');
        $childrenStmt->bindValue(1, $id, SQLITE3_INTEGER);
        $childrenResult = $childrenStmt->execute();
        
        while ($child = $childrenResult->fetchArray(SQLITE3_ASSOC)) {
            $childPath = $newPath ? $newPath . '/' . $id : (string)$id;
            $this->updateTaskHierarchy($child['id'], $id, $newDepth + 1, $childPath);
        }
    }
    
    // 트리 구조 빌드
    private function buildTree($tasks, $parentId = null)
    {
        $tree = [];
        
        foreach ($tasks as $task) {
            if ($task['parent_task_id'] == $parentId) {
                $task['children'] = $this->buildTree($tasks, $task['id']);
                $tree[] = $task;
            }
        }
        
        return $tree;
    }
}