<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $mechanicId = $request->user()->id;
        $timezone = 'Asia/Manila';
        $today = CarbonImmutable::now($timezone);
        $period = $request->query('period', 'today');

        if (! in_array($period, ['today', 'week', 'month', 'year'], true)) {
            $period = 'today';
        }

        $completedJobs = fn () => Appointment::query()
            ->where('mechanic_id', $mechanicId)
            ->where('status', 'completed')
            ->whereNotNull('service_completed_at');

        $completedCounts = [
            'today' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfDay()))->count(),
            'week' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfWeek(CarbonInterface::MONDAY)))->count(),
            'month' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfMonth()))->count(),
            'year' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfYear()))->count(),
        ];

        $activeJobs = Appointment::query()
            ->where('mechanic_id', $mechanicId)
            ->whereIn('status', ['mechanic_assigned', 'checked_in', 'in_service', 'ready_for_release'])
            ->count();

        $recentJobs = Appointment::query()
            ->where('mechanic_id', $mechanicId)
            ->whereIn('status', ['mechanic_assigned', 'checked_in', 'in_service', 'ready_for_release'])
            ->with(['customer:id,name', 'motorcycle:id,brand,model', 'service:id,name'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(4)
            ->get();

        $chart = $this->completedChart($mechanicId, $period, $today, $timezone);
        $chartMax = max(1, ...array_column($chart, 'count'));

        return view('mechanic.dashboard', compact('activeJobs', 'completedCounts', 'recentJobs', 'period', 'chart', 'chartMax'));
    }

    private function completedChart(int $mechanicId, string $period, CarbonImmutable $today, string $timezone): array
    {
        [$length, $unit, $labelFormat] = match ($period) {
            'week' => [8, 'Week', 'M j'],
            'month' => [12, 'Month', 'M'],
            'year' => [5, 'Year', 'Y'],
            default => [7, 'Day', 'D'],
        };

        $subtract = 'sub'.$unit.'s';
        $add = 'add'.$unit;
        $currentStart = $this->periodStart($today, $unit);
        $chart = [];

        for ($index = $length - 1; $index >= 0; $index--) {
            $start = $currentStart->{$subtract}($index);
            $key = $start->format('Y-m-d');
            $chart[$key] = [
                'label' => $start->format($labelFormat),
                'count' => 0,
            ];
        }

        $start = $currentStart->{$subtract}($length - 1);
        $end = $currentStart->{$add}();

        $completed = Appointment::query()
            ->where('mechanic_id', $mechanicId)
            ->where('status', 'completed')
            ->whereNotNull('service_completed_at')
            ->where('service_completed_at', '>=', $this->utc($start))
            ->where('service_completed_at', '<', $this->utc($end))
            ->select(['id', 'service_completed_at'])
            ->lazyById(500);

        foreach ($completed as $appointment) {
            $localDate = $appointment->service_completed_at
                ->toImmutable()
                ->setTimezone($timezone);
            $key = $this->periodStart($localDate, $unit)->format('Y-m-d');

            if (isset($chart[$key])) {
                $chart[$key]['count']++;
            }
        }

        return array_values($chart);
    }

    private function utc(CarbonImmutable $date): string
    {
        return $date->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    private function periodStart(CarbonImmutable $date, string $unit): CarbonImmutable
    {
        if ($unit === 'Week') {
            return $date->startOfWeek(CarbonInterface::MONDAY);
        }

        $method = 'startOf'.$unit;

        return $date->{$method}();
    }
}
