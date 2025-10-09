<?php

class PermissionsRepository
{
    public function getCurrentUserPermissions($user, $projectId)
    {
        if (!$projectId) {
            throw new Exception('project_id parameter is required');
        }

        // 프로젝트 정보 조회
        $project = \App\Models\Project::find($projectId);
        
        if (!$project) {
            throw new Exception('Project not found');
        }

        // 현재 프로젝트에서의 역할 조회 (안전한 방식)
        $roles = [];
        $abilities = [];
        $canEdit = false;
        $canDelete = false;
        $canManageMembers = false;
        
        try {
            // Bouncer 역할 조회
            if (method_exists($user, 'getRoles')) {
                $userRoles = $user->getRoles();
                if ($userRoles) {
                    $projectRoles = $userRoles->where('entity_type', \App\Models\Project::class)
                                            ->where('entity_id', $projectId)
                                            ->pluck('name')->toArray();
                    
                    if (!empty($projectRoles)) {
                        $roles = $projectRoles;
                    } else {
                        // 글로벌 역할 확인
                        $globalRoles = $userRoles->whereNull('entity_type')->pluck('name')->toArray();
                        $roles = $globalRoles;
                    }
                }
            }
        } catch (\Exception $e) {
            // Bouncer 오류 시 기본 역할 설정
            $roles = ['user'];
        }

        // 프로젝트 소유자 체크 (기본적인 확인)
        if ($project->user_id == $user->id) {
            $roles[] = 'project-owner';
            $abilities[] = 'own-project';
            $canEdit = true;
            $canDelete = true;
            $canManageMembers = true;
        }

        // Bouncer 권한 체크 (안전한 방식)
        try {
            if (method_exists($user, 'can')) {
                if ($user->can('manage-project', $project)) {
                    $abilities[] = 'manage-project';
                    $canEdit = true;
                }
                
                if ($user->can('manage-project-settings', $project)) {
                    $abilities[] = 'manage-project-settings';
                }
                
                if ($user->can('manage-project-members', $project)) {
                    $abilities[] = 'manage-project-members';
                    $canManageMembers = true;
                }
                
                if ($user->can('view-project', $project)) {
                    $abilities[] = 'view-project';
                }
                
                if ($user->can('edit-project', $project)) {
                    $abilities[] = 'edit-project';
                    $canEdit = true;
                }
                
                if ($user->can('delete-project', $project)) {
                    $abilities[] = 'delete-project';
                    $canDelete = true;
                }

                // Bouncer의 owns 메서드 체크
                if (method_exists($user, 'owns') && $user->owns($project)) {
                    $abilities[] = 'own-project';
                    $canEdit = true;
                    $canDelete = true;
                    $canManageMembers = true;
                }
            }
        } catch (\Exception $e) {
            // Bouncer 메서드 오류 시 기본 권한 설정
            if ($project->user_id == $user->id) {
                $abilities[] = 'basic-owner-access';
                $canEdit = true;
                $canDelete = true;
                $canManageMembers = true;
            } else {
                $abilities[] = 'basic-user-access';
            }
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'current_project' => [
                'id' => $project->id,
                'name' => $project->name
            ],
            'current_permissions' => [
                'roles' => array_values(array_filter(array_unique($roles))),
                'abilities' => array_values(array_filter(array_unique($abilities))),
                'can_edit' => $canEdit,
                'can_delete' => $canDelete,
                'can_manage_members' => $canManageMembers
            ],
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}