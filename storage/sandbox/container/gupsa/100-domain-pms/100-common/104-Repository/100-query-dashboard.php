<?php

class DashboardRepository
{
    private $dbPath;
    
    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../200-Database/release.sqlite';
    }
    
    public function getStats()
    {
        $db = new SQLite3($this->dbPath);
        
        $projectsCount = $db->querySingle('SELECT COUNT(*) FROM projects WHERE deleted_at IS NULL');
        $tasksCount = $db->querySingle('SELECT COUNT(*) FROM tasks');
        $completedTasksCount = $db->querySingle("SELECT COUNT(*) FROM tasks WHERE status = 'done'");
        
        return [
            'projects_count' => $projectsCount,
            'tasks_count' => $tasksCount,
            'completed_tasks_count' => $completedTasksCount,
            'completion_rate' => $tasksCount > 0 ? round(($completedTasksCount / $tasksCount) * 100, 1) : 0,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}