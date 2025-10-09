<?php

class ColumnsRepository
{
    private $dbPath;
    
    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../200-Database/release.sqlite';
    }
    
    public function getList($type = 'all')
    {
        $db = new SQLite3($this->dbPath);
        
        $whereClause = '';
        if ($type === 'custom') {
            $whereClause = 'WHERE is_system = 0';
        } elseif ($type === 'system') {
            $whereClause = 'WHERE is_system = 1';
        }
        
        $query = "SELECT * FROM custom_columns {$whereClause} ORDER BY sort_order ASC, id ASC";
        $result = $db->query($query);
        
        $columns = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $columns[] = $row;
        }
        
        return $columns;
    }
    
    public function create($data)
    {
        $db = new SQLite3($this->dbPath);
        
        $columnName = $data['column_name'];
        $columnLabel = $data['column_label'];
        $frontendType = $data['column_type'] ?? 'text';
        
        // 프론트엔드 타입을 데이터베이스 타입으로 매핑
        $typeMapping = [
            'text' => 'TEXT',
            'select' => 'TEXT',
            'checkbox' => 'BOOLEAN'
        ];
        $columnType = $typeMapping[$frontendType] ?? 'TEXT';
        
        // 프론트엔드 타입을 display_type으로 매핑
        $displayTypeMapping = [
            'text' => 'input',
            'select' => 'select',
            'checkbox' => 'checkbox'
        ];
        $displayType = $displayTypeMapping[$frontendType] ?? 'input';
        $isRequired = $data['is_required'] ?? 0;
        $isActive = $data['is_active'] ?? 1;
        $options = $data['options'] ?? '';
        
        // 컬럼명 중복 확인
        $checkStmt = $db->prepare('SELECT id FROM custom_columns WHERE column_name = ?');
        $checkStmt->bindValue(1, $columnName, SQLITE3_TEXT);
        $checkResult = $checkStmt->execute();
        
        if ($checkResult->fetchArray()) {
            throw new Exception('이미 존재하는 컬럼명입니다.');
        }
        
        // 컬럼 정보 저장
        $stmt = $db->prepare('INSERT INTO custom_columns (column_name, column_label, column_type, display_type, options, is_required, is_active, is_system, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 0, datetime("now"))');
        $stmt->bindValue(1, $columnName, SQLITE3_TEXT);
        $stmt->bindValue(2, $columnLabel, SQLITE3_TEXT);
        $stmt->bindValue(3, $columnType, SQLITE3_TEXT);
        $stmt->bindValue(4, $displayType, SQLITE3_TEXT);
        $stmt->bindValue(5, $options, SQLITE3_TEXT);
        $stmt->bindValue(6, $isRequired, SQLITE3_INTEGER);
        $stmt->bindValue(7, $isActive, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to create column: ' . $db->lastErrorMsg());
        }
        
        // 실제 projects 테이블에 컬럼 추가
        $alterTableQuery = "ALTER TABLE projects ADD COLUMN custom_{$columnName} {$columnType}";
        try {
            $db->exec($alterTableQuery);
        } catch (Exception $e) {
            // 이미 컬럼이 존재하는 경우 무시
            if (strpos($e->getMessage(), 'duplicate column name') === false) {
                throw new Exception('Failed to add column to table: ' . $e->getMessage());
            }
        }
        
        return $db->lastInsertRowID();
    }
    
    public function findById($id)
    {
        $db = new SQLite3($this->dbPath);
        
        $stmt = $db->prepare('SELECT * FROM custom_columns WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        return $result->fetchArray(SQLITE3_ASSOC);
    }
    
    public function update($id, $data)
    {
        $db = new SQLite3($this->dbPath);
        
        $column = $this->findById($id);
        if (!$column) {
            return false;
        }
        
        // 활성/비활성 상태만 변경하는 경우
        if (isset($data['is_active']) && count($data) === 1) {
            $stmt = $db->prepare('UPDATE custom_columns SET is_active = ? WHERE id = ?');
            $stmt->bindValue(1, $data['is_active'], SQLITE3_INTEGER);
            $stmt->bindValue(2, $id, SQLITE3_INTEGER);
            $stmt->execute();
            
            return true;
        }
        
        // 전체 컬럼 정보 수정
        $stmt = $db->prepare('UPDATE custom_columns SET column_label = ?, column_type = ?, display_type = ?, is_required = ? WHERE id = ?');
        $stmt->bindValue(1, $data['column_label'], SQLITE3_TEXT);
        $stmt->bindValue(2, $data['column_type'], SQLITE3_TEXT);
        $stmt->bindValue(3, $data['display_type'], SQLITE3_TEXT);
        $stmt->bindValue(4, $data['is_required'] ?? 0, SQLITE3_INTEGER);
        $stmt->bindValue(5, $id, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to update column: ' . $db->lastErrorMsg());
        }
        
        return true;
    }
    
    public function delete($id)
    {
        $db = new SQLite3($this->dbPath);
        
        // 컬럼 정보 조회
        $checkStmt = $db->prepare('SELECT * FROM custom_columns WHERE id = ? AND is_system = 0');
        $checkStmt->bindValue(1, $id, SQLITE3_INTEGER);
        $checkResult = $checkStmt->execute();
        $column = $checkResult->fetchArray(SQLITE3_ASSOC);
        
        if (!$column) {
            throw new Exception('Column not found or system column cannot be deleted');
        }
        
        // 커스텀 컬럼 삭제
        $stmt = $db->prepare('DELETE FROM custom_columns WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        return true;
    }
}