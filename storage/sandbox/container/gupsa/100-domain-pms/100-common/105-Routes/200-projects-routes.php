<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require_once __DIR__ . '/../104-Repository/200-query-projects.php';

// 프로젝트 생성
Route::post('projects', function (Request $request) {
    try {
        $repository = new ProjectsRepository();
        $projectId = $repository->create($request->all());
        
        // 생성된 프로젝트 반환
        $newProject = $repository->findById($projectId);
        
        return response()->json(['success' => true, 'data' => $newProject], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 프로젝트 목록 조회
Route::get('projects', function (Request $request) {
    try {
        $repository = new ProjectsRepository();
        
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $status = $request->input('status');
        
        $result = $repository->getList($perPage, $page, $status);
        
        return response()->json($result);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 특정 프로젝트 조회
Route::get('projects/{id}', function (Request $request, $id) {
    try {
        $repository = new ProjectsRepository();
        $project = $repository->findById($id);
        
        if ($project) {
            return response()->json(['success' => true, 'data' => $project]);
        }
        
        return response()->json(['success' => false, 'error' => 'Project not found'], 404);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 프로젝트 수정
Route::put('projects/{id}', function (Request $request, $id) {
    try {
        $repository = new ProjectsRepository();
        
        $success = $repository->update($id, $request->all());
        
        if (!$success) {
            return response()->json(['error' => 'Project not found or no valid fields to update'], 404);
        }
        
        // 수정된 프로젝트 반환
        $updatedProject = $repository->findById($id);
        
        return response()->json($updatedProject);
    } catch (Exception $e) {
        return response()->json(['error' => 'Update failed: ' . $e->getMessage()], 500);
    }
});

// 프로젝트 삭제
Route::delete('projects/{id}', function (Request $request, $id) {
    try {
        $repository = new ProjectsRepository();
        $repository->delete($id);
        
        return response()->json(['success' => true, 'message' => 'Project deleted successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 프로젝트 계층구조 조회 (depth 포함)
Route::get('projects/tree', function (Request $request) {
    try {
        $repository = new ProjectsRepository();
        $tree = $repository->getTreeStructure();
        
        return response()->json($tree);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 하위 프로젝트 생성
Route::post('projects/{parentId}/children', function (Request $request, $parentId) {
    try {
        $repository = new ProjectsRepository();
        
        // 부모 프로젝트 존재 여부 확인
        $parent = $repository->findById($parentId);
        if (!$parent) {
            return response()->json(['error' => 'Parent project not found'], 404);
        }
        
        // 부모의 depth가 9 이상이면 더 이상 생성 불가
        if ($parent['depth'] >= 9) {
            return response()->json(['error' => 'Maximum depth (10) reached'], 400);
        }
        
        $projectData = $request->all();
        $projectData['parent_id'] = $parentId;
        
        $projectId = $repository->createChild($parentId, $projectData);
        $newProject = $repository->findById($projectId);
        
        return response()->json(['success' => true, 'data' => $newProject], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 프로젝트 이동 (부모 변경)
Route::patch('projects/{id}/move', function (Request $request, $id) {
    try {
        $repository = new ProjectsRepository();
        $newParentId = $request->input('parent_id');
        
        $result = $repository->moveProject($id, $newParentId);
        
        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 400);
        }
        
        return response()->json(['success' => true, 'message' => 'Project moved successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 프로젝트 확장/축소 토글
Route::patch('projects/{id}/toggle', function (Request $request, $id) {
    try {
        $repository = new ProjectsRepository();
        $isExpanded = $request->input('is_expanded');
        
        $repository->toggleExpansion($id, $isExpanded);
        
        return response()->json(['success' => true, 'message' => 'Project expansion updated']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});