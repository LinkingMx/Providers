<?php

namespace App\Filament\Widgets\Provider;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Provider Statistics Widget
 *
 * This widget provides personalized statistics for the currently authenticated provider.
 * It displays document completion progress, showing how many documents have been
 * successfully approved out of the total required documents.
 *
 * The widget is designed to be loaded manually by the ProviderDashboard page
 * rather than globally, ensuring it only appears in the appropriate context.
 */
class ProviderStatsWidget extends BaseWidget
{
    /**
     * Get the statistics for the current provider's document progress.
     *
     * This method calculates the provider's document completion percentage
     * by analyzing their required documents and their current approval status.
     * It excludes documents marked as 'No Aplica' from the total count and
     * considers documents with 'is_complete' status as successfully completed.
     *
     * @return array Array containing the provider's document completion statistics
     */
    protected function getStats(): array
    {
        // Get the currently authenticated user (provider)
        // This assumes the user is authenticated and has the provider role
        $user = Auth::user();

        // Calculate total required documents, excluding 'No Aplica' status
        // Documents with 'No Aplica' status are not considered mandatory
        // and shouldn't count towards the completion percentage
        $totalRequired = \App\Models\ProviderDocument::where('user_id', $user->id)
            ->whereHas('documentStatus', function ($q) {
                $q->where('name', '!=', 'No Aplica');
            })
            ->count();

        // Calculate completed documents using the is_complete flag
        // Only documents with statuses marked as complete (like 'Aprobado')
        // are considered successfully completed for compliance purposes
        $completedCount = \App\Models\ProviderDocument::where('user_id', $user->id)
            ->whereHas('documentStatus', function ($q) {
                $q->where('is_complete', true);
            })
            ->count();

        // Calculate completion percentage with division by zero protection
        // If no documents are required, consider it 100% complete
        // Otherwise, calculate the actual percentage and round to nearest integer
        $percentage = ($totalRequired > 0) ? round(($completedCount / $totalRequired) * 100) : 100;

        return [
            // Single stat showing document completion progress
            Stat::make('Progreso de Documentación', "{$percentage}%")
                ->description("{$completedCount} de {$totalRequired} documentos aprobados")
                ->descriptionIcon('heroicon-o-shield-check')
                ->color($this->getProgressColor($percentage))
                ->chart($this->getProgressTrendData($user)),
        ];
    }

    /**
     * Get the appropriate color for the progress stat based on completion percentage.
     *
     * Provides visual feedback about the provider's compliance status:
     * - Green: Excellent compliance (80%+)
     * - Orange: Needs attention (50-79%)
     * - Red: Critical - requires immediate action (<50%)
     *
     * @param  int  $percentage  The completion percentage
     * @return string The color identifier for the stat
     */
    private function getProgressColor(int $percentage): string
    {
        if ($percentage >= 80) {
            return 'success';
        } elseif ($percentage >= 50) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Get trend data for the progress chart (last 7 days).
     *
     * Shows how the provider's completion percentage has changed over time,
     * helping them track their progress towards full compliance.
     *
     * @param  mixed  $user  The authenticated user
     * @return array Chart data showing completion percentage over time
     */
    private function getProgressTrendData($user): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->endOfDay();

            // Get total required documents at this point in time
            $totalRequired = \App\Models\ProviderDocument::where('user_id', $user->id)
                ->whereHas('documentStatus', function ($q) {
                    $q->where('name', '!=', 'No Aplica');
                })
                ->where('created_at', '<=', $date)
                ->count();

            if ($totalRequired === 0) {
                $data[] = 100;

                continue;
            }

            // Get completed documents at this point in time
            $completedCount = \App\Models\ProviderDocument::where('user_id', $user->id)
                ->whereHas('documentStatus', function ($q) {
                    $q->where('is_complete', true);
                })
                ->where('created_at', '<=', $date)
                ->count();

            $percentage = round(($completedCount / $totalRequired) * 100);
            $data[] = $percentage;
        }

        return $data;
    }
}
