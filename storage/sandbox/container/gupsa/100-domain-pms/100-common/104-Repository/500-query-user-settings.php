<?php

class UserSettingsRepository
{
    private $dbPath;
    
    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../200-Database/release.sqlite';
    }
    
    public function getColumnSettings($screenType = 'table_view')
    {
        $db = new SQLite3($this->dbPath);
        
        $query = "SELECT * FROM user_column_settings WHERE screen_type = ? ORDER BY column_order ASC";
        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $screenType, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        $settings = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $settings[] = $row;
        }
        
        return $settings;
    }
    
    public function saveColumnSetting($screenType, $columnName, $isVisible)
    {
        $db = new SQLite3($this->dbPath);
        
        // 기존 설정 확인
        $checkStmt = $db->prepare('SELECT id FROM user_column_settings WHERE screen_type = ? AND column_name = ?');
        $checkStmt->bindValue(1, $screenType, SQLITE3_TEXT);
        $checkStmt->bindValue(2, $columnName, SQLITE3_TEXT);
        $checkResult = $checkStmt->execute();
        
        if ($checkResult->fetchArray()) {
            // 업데이트
            $stmt = $db->prepare('UPDATE user_column_settings SET is_visible = ? WHERE screen_type = ? AND column_name = ?');
            $stmt->bindValue(1, $isVisible, SQLITE3_INTEGER);
            $stmt->bindValue(2, $screenType, SQLITE3_TEXT);
            $stmt->bindValue(3, $columnName, SQLITE3_TEXT);
        } else {
            // 새로 생성
            $stmt = $db->prepare('INSERT INTO user_column_settings (screen_type, column_name, is_visible, column_order) VALUES (?, ?, ?, 0)');
            $stmt->bindValue(1, $screenType, SQLITE3_TEXT);
            $stmt->bindValue(2, $columnName, SQLITE3_TEXT);
            $stmt->bindValue(3, $isVisible, SQLITE3_INTEGER);
        }
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception('Failed to save settings: ' . $db->lastErrorMsg());
        }
        
        return true;
    }
    
    public function saveBulkColumnSettings($screenType, $columnSettings)
    {
        $db = new SQLite3($this->dbPath);
        
        // 기존 설정 삭제
        $deleteStmt = $db->prepare('DELETE FROM user_column_settings WHERE screen_type = ?');
        $deleteStmt->bindValue(1, $screenType, SQLITE3_TEXT);
        $deleteStmt->execute();
        
        // 새 설정 저장
        foreach ($columnSettings as $columnName => $settings) {
            $stmt = $db->prepare('INSERT INTO user_column_settings (screen_type, column_name, is_visible, column_order) VALUES (?, ?, ?, ?)');
            $stmt->bindValue(1, $screenType, SQLITE3_TEXT);
            $stmt->bindValue(2, $columnName, SQLITE3_TEXT);
            $stmt->bindValue(3, $settings['is_visible'] ? 1 : 0, SQLITE3_INTEGER);
            $stmt->bindValue(4, $settings['column_order'] ?? 0, SQLITE3_INTEGER);
            $stmt->execute();
        }
        
        return true;
    }
}