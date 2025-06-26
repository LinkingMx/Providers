<?php

namespace App\Filament\Widgets\Admin;

use App\Models\ProviderDocument;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    /**
     * Get the statistics for the admin dashboard.
     *
     * Provides key metrics about document compliance and workflow status
     * using our dynamic status system for real-time insights.
     */
    protected function getStats(): array
    {
        return [
            // Documents currently under review - critical for admin workload management
            Stat::make('En Revisión', $this->getDocumentsInReview())
                ->description('Documentos pendientes de revisión')
                ->descriptionIcon('heroicon-m-document-magnifying-glass')
                ->color('warning')
                ->chart($this->getReviewTrendData()),

            // Overall compliance percentage - key performance indicator
            Stat::make('Cumplimiento General', $this->getCompliancePercentage().'%')
                ->description('Documentos completados exitosamente')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($this->getComplianceColor())
                ->chart($this->getComplianceTrendData()),

            // Additional helpful stat: Documents expiring soon
            Stat::make('Vencen Pronto', $this->getExpiringSoonCount())
                ->description('Documentos que vencen en 30 días')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger')
                ->chart($this->getExpirationTrendData()),
        ];
    }

    /**
     * Get the count of documents currently in review status.
     *
     * @return int Number of documents with 'En Revisión' status
     */
    private function getDocumentsInReview(): int
    {
        return ProviderDocument::whereHas('documentStatus', function ($query) {
            $query->where('name', 'En Revisión');
        })->count();
    }

    /**
     * Calculate the overall compliance percentage.
     *
     * @return int Percentage of documents with completed status
     */
    private function getCompliancePercentage(): int
    {
        $totalDocuments = ProviderDocument::count();

        if ($totalDocuments === 0) {
            return 0;
        }

        $completedDocuments = ProviderDocument::whereHas('documentStatus', function ($query) {
            $query->where('is_complete', true);
        })->count();

        return round(($completedDocuments / $totalDocuments) * 100);
    }

    /**
     * Get the count of documents expiring within 30 days.
     *
     * @return int Number of documents expiring soon
     */
    private function getExpiringSoonCount(): int
    {
        return ProviderDocument::where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays(30))
            ->whereHas('documentStatus', function ($query) {
                $query->where('is_complete', true);
            })
            ->count();
    }

    /**
     * Get the color for compliance stat based on percentage.
     *
     * @return string Color indicator based on compliance level
     */
    private function getComplianceColor(): string
    {
        $percentage = $this->getCompliancePercentage();

        if ($percentage >= 80) {
            return 'success';
        } elseif ($percentage >= 60) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Get trend data for documents in review (last 7 days).
     *
     * @return array Chart data showing review volume trend
     */
    private function getReviewTrendData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = ProviderDocument::whereHas('documentStatus', function ($query) {
                $query->where('name', 'En Revisión');
            })
                ->whereDate('updated_at', $date)
                ->count();

            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get trend data for compliance percentage (last 7 days).
     *
     * @return array Chart data showing compliance trend
     */
    private function getComplianceTrendData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->endOfDay();

            $totalDocuments = ProviderDocument::where('created_at', '<=', $date)->count();

            if ($totalDocuments === 0) {
                $data[] = 0;

                continue;
            }

            $completedDocuments = ProviderDocument::whereHas('documentStatus', function ($query) {
                $query->where('is_complete', true);
            })
                ->where('created_at', '<=', $date)
                ->count();

            $percentage = round(($completedDocuments / $totalDocuments) * 100);
            $data[] = $percentage;
        }

        return $data;
    }

    /**
     * Get trend data for expiring documents (last 7 days).
     *
     * @return array Chart data showing expiration trend
     */
    private function getExpirationTrendData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = ProviderDocument::where('expires_at', '>', $date)
                ->where('expires_at', '<=', $date->copy()->addDays(30))
                ->whereHas('documentStatus', function ($query) {
                    $query->where('is_complete', true);
                })
                ->count();

            $data[] = $count;
        }

        return $data;
    }
}
