<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// 북마크 관리 API 라우트

// 북마크 목록 조회
Route::get('/bookmarks', function () {
    $dbPath = __DIR__ . '/../200-Database/release.sqlite';
    
    if (!file_exists($dbPath)) {
        return response()->json(['error' => 'Database not found'], 404);
    }
    
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 테이블이 없으면 생성
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookmarks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                type VARCHAR(10) NOT NULL DEFAULT 'bookmark',
                title VARCHAR(255) NOT NULL,
                url TEXT,
                parent_id INTEGER,
                order_num INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES bookmarks(id) ON DELETE CASCADE
            )
        ");
        
        $stmt = $pdo->query("SELECT * FROM bookmarks ORDER BY parent_id, order_num");
        $bookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return response()->json($bookmarks);
        
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// 북마크 생성
Route::post('/bookmarks', function (Request $request) {
    $dbPath = __DIR__ . '/../200-Database/release.sqlite';
    
    if (!file_exists($dbPath)) {
        return response()->json(['error' => 'Database not found'], 404);
    }
    
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $data = $request->all();
        
        $stmt = $pdo->prepare("
            INSERT INTO bookmarks (type, title, url, parent_id, order_num, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        
        $stmt->execute([
            $data['type'] ?? 'bookmark',
            $data['title'],
            $data['url'] ?? null,
            $data['parent_id'] ?? null,
            $data['order'] ?? 0
        ]);
        
        $bookmarkId = $pdo->lastInsertId();
        
        // 생성된 북마크 반환
        $stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE id = ?");
        $stmt->execute([$bookmarkId]);
        $bookmark = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return response()->json($bookmark, 201);
        
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// 북마크 수정
Route::put('/bookmarks/{id}', function (Request $request, $id) {
    $dbPath = __DIR__ . '/../200-Database/release.sqlite';
    
    if (!file_exists($dbPath)) {
        return response()->json(['error' => 'Database not found'], 404);
    }
    
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $data = $request->all();
        
        $stmt = $pdo->prepare("
            UPDATE bookmarks 
            SET title = ?, url = ?, parent_id = ?, order_num = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['title'],
            $data['url'] ?? null,
            $data['parent_id'] ?? null,
            $data['order'] ?? 0,
            $id
        ]);
        
        // 수정된 북마크 반환
        $stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE id = ?");
        $stmt->execute([$id]);
        $bookmark = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$bookmark) {
            return response()->json(['error' => 'Bookmark not found'], 404);
        }
        
        return response()->json($bookmark);
        
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// 북마크 삭제
Route::delete('/bookmarks/{id}', function ($id) {
    $dbPath = __DIR__ . '/../200-Database/release.sqlite';
    
    if (!file_exists($dbPath)) {
        return response()->json(['error' => 'Database not found'], 404);
    }
    
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 하위 북마크들도 삭제 (CASCADE는 SQLite에서 제대로 작동하지 않을 수 있음)
        $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE id = ? OR parent_id = ?");
        $stmt->execute([$id, $id]);
        
        return response()->json(['message' => 'Bookmark deleted successfully']);
        
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// 북마크 순서 업데이트
Route::post('/bookmarks/reorder', function (Request $request) {
    $dbPath = __DIR__ . '/../200-Database/release.sqlite';
    
    if (!file_exists($dbPath)) {
        return response()->json(['error' => 'Database not found'], 404);
    }
    
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $bookmarks = $request->input('bookmarks', []);
        
        $pdo->beginTransaction();
        
        foreach ($bookmarks as $bookmark) {
            $stmt = $pdo->prepare("
                UPDATE bookmarks 
                SET parent_id = ?, order_num = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $stmt->execute([
                $bookmark['parent_id'] ?? null,
                $bookmark['order'] ?? 0,
                $bookmark['id']
            ]);
        }
        
        $pdo->commit();
        
        return response()->json(['message' => 'Order updated successfully']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
});