<?php

namespace App\Traits;

use App\Models\AuditoriaEspecifica;
use App\Models\AuditoriaAgenda;

trait CalculatesAuditHours
{
    /**
     * Calculate and update executed hours for an audit based on agenda progress.
     */
    public function updateExecutedHours($ae_id)
    {
        $audit = AuditoriaEspecifica::with(['agenda.checklists', 'equipo'])->find($ae_id);
        if (!$audit)
            return;

        $hoursMap = [];
        foreach ($audit->equipo as $member) {
            $hoursMap[$member->auditor_id] = 0;
        }

        foreach ($audit->agenda as $item) {
            // Cancelled items do not count
            if ($item->estado === 'Cancelada')
                continue;

            if (!$item->aea_hora_inicio || !$item->aea_hora_fin)
                continue;

            $start = \Carbon\Carbon::parse($item->aea_hora_inicio);
            $end = \Carbon\Carbon::parse($item->aea_hora_fin);
            $duration = abs($end->diffInMinutes($start, false)) / 60;

            // Calculate Progress %
            $progress = 0;
            if (in_array($item->aea_tipo, ['apertura', 'cierre', 'gabinete'])) {
                // Administrative: 100% if Concluded, else 0%
                $progress = ($item->estado === 'Concluida') ? 1 : 0;
            } else {
                // Execution: Based on checklist items
                if ($item->estado === 'Concluida') {
                    $progress = 1;
                } elseif ($item->estado === 'En Curso') {
                    // Check checklists count
                    $total = $item->checklists->count();
                    if ($total > 0) {
                        $completed = $item->checklists->whereNotNull('estado_cumplimiento')
                            ->where('estado_cumplimiento', '!=', 'Sin Evaluar')
                            ->count();
                        $progress = $completed / $total;
                    } else {
                        $progress = 0.1; // Started but no items?
                    }
                }
            }

            $executedDuration = $duration * $progress;

            if ($executedDuration > 0) {
                if (in_array($item->aea_tipo, ['apertura', 'cierre', 'gabinete'])) {
                    foreach ($audit->equipo as $member) {
                        $hoursMap[$member->auditor_id] += $executedDuration;
                    }
                } else {
                    if ($item->auditor_id && isset($hoursMap[$item->auditor_id])) {
                        $hoursMap[$item->auditor_id] += $executedDuration;
                    }
                    if ($item->observador_id && isset($hoursMap[$item->observador_id])) {
                        $hoursMap[$item->observador_id] += $executedDuration;
                    }
                }
            }
        }

        // Calculate Global Progress
        $totalItems = $audit->agenda->where('estado', '!=', 'Cancelada')->count();
        $totalProgressSum = 0;

        foreach ($audit->agenda as $item) {
            if ($item->estado === 'Cancelada')
                continue;

            // Re-calculate progress for this item logic (same as above)
            $progress = 0;
            if (in_array($item->aea_tipo, ['apertura', 'cierre', 'gabinete'])) {
                $progress = ($item->estado === 'Concluida') ? 1 : 0;
            } else {
                if ($item->estado === 'Concluida') {
                    $progress = 1;
                } elseif ($item->estado === 'En Curso') {
                    $checkTotal = $item->checklists->count();
                    if ($checkTotal > 0) {
                        $checkCompleted = $item->checklists->whereNotNull('estado_cumplimiento')
                            ->where('estado_cumplimiento', '!=', 'Sin Evaluar')
                            ->count();
                        $progress = $checkCompleted / $checkTotal;
                    }
                }
            }
            $totalProgressSum += $progress;
        }

        if ($totalItems > 0) {
            $globalProgress = ($totalProgressSum / $totalItems) * 100;
            $audit->ae_avance = round($globalProgress, 2);
            $audit->save();
        }
    }

    /**
     * Calculate and update programmed hours for an audit based on agenda.
     */
    public function updateCalculatedHours($ae_id)
    {
        $audit = AuditoriaEspecifica::with(['agenda', 'equipo'])->find($ae_id);
        if (!$audit)
            return;

        $hoursMap = [];
        foreach ($audit->equipo as $member) {
            $hoursMap[$member->auditor_id] = 0;
        }

        foreach ($audit->agenda as $item) {
            if ($item->estado === 'Cancelada')
                continue;

            if (!$item->aea_hora_inicio || !$item->aea_hora_fin)
                continue;

            $start = \Carbon\Carbon::parse($item->aea_hora_inicio);
            $end = \Carbon\Carbon::parse($item->aea_hora_fin);
            $duration = abs($end->diffInMinutes($start, false)) / 60;

            if (in_array($item->aea_tipo, ['apertura', 'cierre', 'gabinete'])) {
                // Add to all members
                foreach ($audit->equipo as $member) {
                    $hoursMap[$member->auditor_id] += $duration;
                }
            } else {
                // Execution
                // Add to Auditor
                if ($item->auditor_id && isset($hoursMap[$item->auditor_id])) {
                    $hoursMap[$item->auditor_id] += $duration;
                }
                // Add to Observer
                if ($item->observador_id && isset($hoursMap[$item->observador_id])) {
                    $hoursMap[$item->observador_id] += $duration;
                }
            }
        }

        foreach ($audit->equipo as $member) {
            $newHours = $hoursMap[$member->auditor_id] ?? 0;
            if ($member->aeq_horas_programadas != $newHours) {
                $member->aeq_horas_programadas = $newHours;
                $member->save();
            }
        }

        // Also update executed hours (since cancellation might affect it)
        $this->updateExecutedHours($ae_id);
    }
}
