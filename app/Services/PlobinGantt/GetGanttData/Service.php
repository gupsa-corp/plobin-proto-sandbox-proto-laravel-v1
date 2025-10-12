<?php

namespace App\Services\PlobinGantt\GetGanttData;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(int $projectId): array
    {
        $workPackages = DB::table('plobin_gantt_work_packages as wp')
            ->join('plobin_gantt_statuses as s', 'wp.status_id', '=', 's.id')
            ->join('plobin_gantt_work_package_types as t', 'wp.type_id', '=', 't.id')
            ->where('wp.project_id', $projectId)
            ->whereNull('wp.deleted_at')
            ->select(
                'wp.id',
                'wp.subject as name',
                'wp.start_date',
                'wp.due_date',
                'wp.done_ratio as progress',
                'wp.parent_id',
                't.is_milestone',
                's.color',
                'wp.position'
            )
            ->orderBy('wp.position')
            ->get();

        $relations = DB::table('plobin_gantt_relations')
            ->whereIn('from_id', $workPackages->pluck('id'))
            ->where('relation_type', 'precedes')
            ->select('from_id', 'to_id')
            ->get();

        $tasks = [];
        foreach ($workPackages as $wp) {
            $dependencies = $relations
                ->where('to_id', $wp->id)
                ->pluck('from_id')
                ->join(', ');

            $tasks[] = [
                'id' => (string) $wp->id,
                'name' => $wp->name,
                'start' => $wp->start_date,
                'end' => $wp->due_date,
                'progress' => $wp->progress,
                'dependencies' => $dependencies,
                'custom_class' => $wp->is_milestone ? 'bar-milestone' : ''
            ];
        }

        return $tasks;
    }
}
