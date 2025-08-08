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
        $user = Auth::user();

        // Total de documentos asignados al proveedor
        $totalDocuments = \App\Models\ProviderDocument::where('user_id', $user->id)->count();

        // Documentos pendientes de cargar (sin archivo)
        $pendingUpload = \App\Models\ProviderDocument::where('user_id', $user->id)
            ->whereNull('file_path')
            ->count();

        // Documentos aprobados
        $approvedCount = \App\Models\ProviderDocument::where('user_id', $user->id)
            ->whereHas('documentStatus', function ($q) {
                $q->where('is_complete', true);
            })
            ->count();

        // Documentos rechazados
        $rejectedCount = \App\Models\ProviderDocument::where('user_id', $user->id)
            ->whereHas('documentStatus', function ($q) {
                $q->where('name', 'Rechazado');
            })
            ->count();

        // Documentos en revisión (cargados pero no aprobados ni rechazados)
        $inReviewCount = \App\Models\ProviderDocument::where('user_id', $user->id)
            ->whereNotNull('file_path')
            ->whereHas('documentStatus', function ($q) {
                $q->where('is_complete', false)
                  ->where('name', '!=', 'Rechazado');
            })
            ->count();

        // Calcular porcentaje de cumplimiento (aprobados vs total requerido)
        $completionPercentage = ($totalDocuments > 0) 
            ? round(($approvedCount / $totalDocuments) * 100) 
            : 0;

        // Calcular porcentaje de aprobación (aprobados vs procesados)
        $processedCount = $approvedCount + $rejectedCount;
        $approvalRate = ($processedCount > 0) 
            ? round(($approvedCount / $processedCount) * 100) 
            : 0;

        return [
            Stat::make('Documentos Pendientes', $pendingUpload)
                ->description("De {$totalDocuments} documentos requeridos")
                ->descriptionIcon('heroicon-o-document-arrow-up')
                ->color($pendingUpload > 0 ? 'warning' : 'success'),
            
            Stat::make('Estado de Documentación', "{$completionPercentage}%")
                ->description("{$approvedCount} aprobados, {$rejectedCount} rechazados, {$inReviewCount} en revisión")
                ->descriptionIcon('heroicon-o-shield-check')
                ->color($this->getProgressColor($completionPercentage))
                ->chart($this->getProgressTrendData($user)),
            
            Stat::make('Tasa de Aprobación', "{$approvalRate}%")
                ->description("De documentos procesados")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($approvalRate >= 80 ? 'success' : ($approvalRate >= 50 ? 'warning' : 'danger')),
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

            // Total de documentos asignados hasta esta fecha
            $totalDocuments = \App\Models\ProviderDocument::where('user_id', $user->id)
                ->where('created_at', '<=', $date)
                ->count();

            if ($totalDocuments === 0) {
                $data[] = 0;
                continue;
            }

            // Documentos aprobados hasta esta fecha
            $approvedCount = \App\Models\ProviderDocument::where('user_id', $user->id)
                ->whereHas('documentStatus', function ($q) {
                    $q->where('is_complete', true);
                })
                ->where('created_at', '<=', $date)
                ->count();

            $percentage = round(($approvedCount / $totalDocuments) * 100);
            $data[] = $percentage;
        }

        return $data;
    }
}
