<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require_once __DIR__ . '/../104-Repository/300-query-tasks.php';

// 칸반 보드용 태스크 조회
Route::get('projects/{projectId}/kanban', function (Request $request, $projectId) {
    try {
        $repository = new TasksRepository();
        $kanbanData = $repository->getKanbanData($projectId);
        
        if (!$kanbanData) {
            return response()->json(['error' => 'Project not found'], 404);
        }
        
        return response()->json($kanbanData);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 생성
Route::post('projects/{projectId}/tasks', function (Request $request, $projectId) {
    try {
        $repository = new TasksRepository();
        $taskId = $repository->create($projectId, $request->all());
        
        if (!$taskId) {
            return response()->json(['error' => 'Project not found'], 404);
        }
        
        // 생성된 태스크 반환
        $newTask = $repository->findById($taskId);
        
        return response()->json($newTask, 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 수정
Route::put('tasks/{id}', function (Request $request, $id) {
    try {
        $repository = new TasksRepository();
        
        $success = $repository->update($id, $request->all());
        
        if (!$success) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        
        // 수정된 태스크 반환
        $updatedTask = $repository->findById($id);
        
        return response()->json($updatedTask);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 상태 변경 (칸반 보드 드래그앤드롭용)
Route::patch('tasks/{id}/status', function (Request $request, $id) {
    try {
        $repository = new TasksRepository();
        
        $status = $request->input('status');
        $position = $request->input('position', 0);
        
        $repository->updateStatus($id, $status, $position);
        
        return response()->json(['message' => 'Task status updated successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 삭제
Route::delete('tasks/{id}', function (Request $request, $id) {
    try {
        $repository = new TasksRepository();
        $repository->delete($id);
        
        return response()->json(['message' => 'Task deleted successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 테이블 뷰용 태스크 목록 조회
Route::get('projects/{projectId}/tasks', function (Request $request, $projectId) {
    try {
        $repository = new TasksRepository();
        
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $status = $request->input('status');
        $priority = $request->input('priority');
        
        $result = $repository->getList($projectId, $perPage, $page, $status, $priority);
        
        return response()->json($result);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 갠트 차트용 데이터 조회
Route::get('projects/{projectId}/gantt', function (Request $request, $projectId) {
    try {
        $repository = new TasksRepository();
        $ganttData = $repository->getGanttData($projectId);
        
        if (!$ganttData) {
            return response()->json(['error' => 'Project not found'], 404);
        }
        
        return response()->json($ganttData);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 캘린더 뷰용 데이터 조회
Route::get('projects/{projectId}/calendar', function (Request $request, $projectId) {
    try {
        $repository = new TasksRepository();
        
        $month = $request->input('month', date('Y-m'));
        $calendarData = $repository->getCalendarData($projectId, $month);
        
        return response()->json($calendarData);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 계층구조 조회 (depth 포함)
Route::get('projects/{projectId}/tasks/tree', function (Request $request, $projectId) {
    try {
        $repository = new TasksRepository();
        $tree = $repository->getTreeStructure($projectId);
        
        return response()->json($tree);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 하위 태스크 생성
Route::post('tasks/{parentId}/children', function (Request $request, $parentId) {
    try {
        $repository = new TasksRepository();
        
        // 부모 태스크 존재 여부 확인
        $parent = $repository->findById($parentId);
        if (!$parent) {
            return response()->json(['error' => 'Parent task not found'], 404);
        }
        
        // 부모의 depth가 9 이상이면 더 이상 생성 불가
        if ($parent['depth'] >= 9) {
            return response()->json(['error' => 'Maximum depth (10) reached'], 400);
        }
        
        $taskData = $request->all();
        $taskData['parent_task_id'] = $parentId;
        $taskData['project_id'] = $parent['project_id']; // 부모의 project_id 상속
        
        $taskId = $repository->createChild($parentId, $taskData);
        $newTask = $repository->findById($taskId);
        
        return response()->json(['success' => true, 'data' => $newTask], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 이동 (부모 변경)
Route::patch('tasks/{id}/move', function (Request $request, $id) {
    try {
        $repository = new TasksRepository();
        $newParentId = $request->input('parent_task_id');
        
        $result = $repository->moveTask($id, $newParentId);
        
        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 400);
        }
        
        return response()->json(['success' => true, 'message' => 'Task moved successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 태스크 확장/축소 토글
Route::patch('tasks/{id}/toggle', function (Request $request, $id) {
    try {
        $repository = new TasksRepository();
        $isExpanded = $request->input('is_expanded');
        
        $repository->toggleExpansion($id, $isExpanded);
        
        return response()->json(['success' => true, 'message' => 'Task expansion updated']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});