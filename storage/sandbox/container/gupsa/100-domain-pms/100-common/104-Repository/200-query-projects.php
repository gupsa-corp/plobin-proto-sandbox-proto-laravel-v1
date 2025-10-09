<?php

class ProjectsRepository
{
    private $dbPath;
    
    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../200-Database/release.sqlite';
    }
    
    public function create($data)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('INSERT INTO projects (name, description, status, progress, team_members, priority, start_date, end_date, client, category, budget, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime("now"), datetime("now"))');
        $stmt->bindValue(1, $data['name'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(2, $data['description'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(3, $data['status'] ?? 'pending', SQLITE3_TEXT);
        $stmt->bindValue(4, $data['progress'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(5, $data['team_members'] ?? 1, SQLITE3_INTEGER);
        $stmt->bindValue(6, $data['priority'] ?? 'medium', SQLITE3_TEXT);
        $stmt->bindValue(7, $data['start_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(8, $data['end_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(9, $data['client'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(10, $data['category'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(11, $data['budget'] ?? 0, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to create project: ' . $db->lastErrorMsg());
        }
        
        return $db->lastInsertRowID();
    }
    
    public function getList($perPage, $page, $status = null)
    {
        $db = new SQLite3($this->dbPath);
        
        $offset = ($page - 1) * $perPage;
        $whereClause = 'WHERE deleted_at IS NULL';
        $params = [];
        
        if ($status) {
            $whereClause .= ' AND status = ?';
            $params[] = $status;
        }
        
        $query = "SELECT id, name, description, status, progress, team_members, priority, start_date, end_date, client, category, budget FROM projects {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($query);
        
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param, SQLITE3_TEXT);
        }
        $stmt->bindValue($paramIndex++, $perPage, SQLITE3_INTEGER);
        $stmt->bindValue($paramIndex, $offset, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        $projects = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $projects[] = $row;
        }
        
        // 전체 개수 조회
        $countQuery = "SELECT COUNT(*) FROM projects {$whereClause}";
        $countStmt = $db->prepare($countQuery);
        $paramIndex = 1;
        foreach ($params as $param) {
            $countStmt->bindValue($paramIndex++, $param, SQLITE3_TEXT);
        }
        $countResult = $countStmt->execute();
        $total = $countResult->fetchArray(SQLITE3_NUM)[0];
        
        return [
            'data' => $projects,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function findById($id)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('SELECT * FROM projects WHERE id = ? AND deleted_at IS NULL');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        return $result->fetchArray(SQLITE3_ASSOC);
    }
    
    public function update($id, $data)
    {
        $db = new SQLite3($this->dbPath);
        
        // 프로젝트 존재 확인
        $checkStmt = $db->prepare('SELECT id FROM projects WHERE id = ? AND deleted_at IS NULL');
        $checkStmt->bindValue(1, $id, SQLITE3_INTEGER);
        if (!$checkStmt->execute()->fetchArray()) {
            return false;
        }
        
        $updateFields = [];
        $updateValues = [];
        
        // 기본 허용된 컬럼들
        $allowedBaseColumns = ['name', 'description', 'status', 'progress', 'team_members', 'priority', 'start_date', 'end_date', 'client', 'category', 'budget'];
        
        // 기본 컬럼 처리
        foreach ($allowedBaseColumns as $column) {
            if (isset($data[$column])) {
                $updateFields[] = "{$column} = ?";
                $updateValues[] = ['value' => $data[$column], 'type' => ($column === 'progress' ? SQLITE3_INTEGER : ($column === 'budget' ? SQLITE3_REAL : SQLITE3_TEXT))];
            }
        }
        
        // 커스텀 컬럼 처리
        foreach ($data as $key => $value) {
            if (strpos($key, 'custom_') === 0) {
                $updateFields[] = "{$key} = ?";
                $updateValues[] = ['value' => $value, 'type' => SQLITE3_TEXT];
            }
        }
        
        if (empty($updateFields)) {
            return false;
        }
        
        // 동적 UPDATE 쿼리 생성
        $updateQuery = "UPDATE projects SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $db->prepare($updateQuery);
        
        // 값들을 바인딩
        foreach ($updateValues as $index => $valueInfo) {
            $stmt->bindValue($index + 1, $valueInfo['value'], $valueInfo['type']);
        }
        // 마지막에 ID 바인딩
        $stmt->bindValue(count($updateValues) + 1, $id, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Update failed: ' . $db->lastErrorMsg());
        }
        
        return true;
    }
    
    public function delete($id)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('UPDATE projects SET deleted_at = datetime("now") WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to delete project: ' . $db->lastErrorMsg());
        }
        
        return true;
    }
    
    // 계층구조 트리 조회
    public function getTreeStructure()
    {
        $db = new SQLite3($this->dbPath);
        
        $query = "
            SELECT p1.*, 
                   (SELECT COUNT(*) FROM projects p2 WHERE p2.parent_id = p1.id AND p2.deleted_at IS NULL) as child_count
            FROM projects p1 
            WHERE p1.deleted_at IS NULL 
            ORDER BY p1.path, p1.id
        ";
        
        $result = $db->query($query);
        $projects = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $row['has_children'] = $row['child_count'] > 0;
            unset($row['child_count']);
            $projects[] = $row;
        }
        
        return $this->buildTree($projects);
    }
    
    // 하위 프로젝트 생성
    public function createChild($parentId, $data)
    {
        $db = new SQLite3($this->dbPath);
        
        // 부모 프로젝트 정보 조회
        $parentStmt = $db->prepare('SELECT depth, path FROM projects WHERE id = ? AND deleted_at IS NULL');
        $parentStmt->bindValue(1, $parentId, SQLITE3_INTEGER);
        $parentResult = $parentStmt->execute();
        $parent = $parentResult->fetchArray(SQLITE3_ASSOC);
        
        if (!$parent) {
            throw new Exception('Parent project not found');
        }
        
        $childDepth = $parent['depth'] + 1;
        $childPath = $parent['path'] ? $parent['path'] . '/' . $parentId : (string)$parentId;
        
        $stmt = $db->prepare('
            INSERT INTO projects (
                name, description, status, progress, team_members, priority, 
                start_date, end_date, client, category, budget, parent_id, 
                depth, path, has_children, is_expanded, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, datetime("now"), datetime("now"))
        ');
        
        $stmt->bindValue(1, $data['name'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(2, $data['description'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(3, $data['status'] ?? 'pending', SQLITE3_TEXT);
        $stmt->bindValue(4, $data['progress'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(5, $data['team_members'] ?? 1, SQLITE3_INTEGER);
        $stmt->bindValue(6, $data['priority'] ?? 'medium', SQLITE3_TEXT);
        $stmt->bindValue(7, $data['start_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(8, $data['end_date'] ?? null, SQLITE3_TEXT);
        $stmt->bindValue(9, $data['client'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(10, $data['category'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(11, $data['budget'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(12, $parentId, SQLITE3_INTEGER);
        $stmt->bindValue(13, $childDepth, SQLITE3_INTEGER);
        $stmt->bindValue(14, $childPath, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to create child project: ' . $db->lastErrorMsg());
        }
        
        // 부모의 has_children 업데이트
        $updateParentStmt = $db->prepare('UPDATE projects SET has_children = 1 WHERE id = ?');
        $updateParentStmt->bindValue(1, $parentId, SQLITE3_INTEGER);
        $updateParentStmt->execute();
        
        return $db->lastInsertRowID();
    }
    
    // 프로젝트 이동
    public function moveProject($id, $newParentId)
    {
        $db = new SQLite3($this->dbPath);
        
        // 이동할 프로젝트 조회
        $project = $this->findById($id);
        if (!$project) {
            return ['success' => false, 'message' => 'Project not found'];
        }
        
        // 새 부모 정보 조회 (null이면 루트로 이동)
        $newDepth = 0;
        $newPath = '';
        
        if ($newParentId) {
            $newParent = $this->findById($newParentId);
            if (!$newParent) {
                return ['success' => false, 'message' => 'New parent project not found'];
            }
            
            // 순환 참조 방지 (자기 자신이나 하위 프로젝트로는 이동 불가)
            if ($newParentId == $id || strpos($newParent['path'], $project['path'] . '/' . $id) === 0) {
                return ['success' => false, 'message' => 'Cannot move to descendant project'];
            }
            
            if ($newParent['depth'] >= 9) {
                return ['success' => false, 'message' => 'Maximum depth (10) would be exceeded'];
            }
            
            $newDepth = $newParent['depth'] + 1;
            $newPath = $newParent['path'] ? $newParent['path'] . '/' . $newParentId : (string)$newParentId;
        }
        
        // 프로젝트와 모든 하위 프로젝트의 depth와 path 업데이트
        $this->updateProjectHierarchy($id, $newParentId, $newDepth, $newPath);
        
        return ['success' => true, 'message' => 'Project moved successfully'];
    }
    
    // 확장/축소 토글
    public function toggleExpansion($id, $isExpanded)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('UPDATE projects SET is_expanded = ? WHERE id = ?');
        $stmt->bindValue(1, $isExpanded ? 1 : 0, SQLITE3_INTEGER);
        $stmt->bindValue(2, $id, SQLITE3_INTEGER);
        
        return $stmt->execute();
    }
    
    // 프로젝트 계층구조 업데이트 (재귀적)
    private function updateProjectHierarchy($id, $newParentId, $newDepth, $newPath)
    {
        $db = new SQLite3($this->dbPath);
        
        // 현재 프로젝트 업데이트
        $stmt = $db->prepare('UPDATE projects SET parent_id = ?, depth = ?, path = ? WHERE id = ?');
        $stmt->bindValue(1, $newParentId, $newParentId ? SQLITE3_INTEGER : SQLITE3_NULL);
        $stmt->bindValue(2, $newDepth, SQLITE3_INTEGER);
        $stmt->bindValue(3, $newPath, SQLITE3_TEXT);
        $stmt->bindValue(4, $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        // 하위 프로젝트들 조회 및 업데이트
        $childrenStmt = $db->prepare('SELECT id FROM projects WHERE parent_id = ? AND deleted_at IS NULL');
        $childrenStmt->bindValue(1, $id, SQLITE3_INTEGER);
        $childrenResult = $childrenStmt->execute();
        
        while ($child = $childrenResult->fetchArray(SQLITE3_ASSOC)) {
            $childPath = $newPath ? $newPath . '/' . $id : (string)$id;
            $this->updateProjectHierarchy($child['id'], $id, $newDepth + 1, $childPath);
        }
    }
    
    // 트리 구조 빌드
    private function buildTree($projects, $parentId = null)
    {
        $tree = [];
        
        foreach ($projects as $project) {
            if ($project['parent_id'] == $parentId) {
                $project['children'] = $this->buildTree($projects, $project['id']);
                $tree[] = $project;
            }
        }
        
        return $tree;
    }
}