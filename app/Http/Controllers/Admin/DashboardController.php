<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalMechanics = User::query()->where('role', 'mechanic')->count();
        $activeMechanics = User::query()->where('role', 'mechanic')->where('status', 'active')->count();
        $totalCustomers = User::query()->where('role', 'customer')->count();
        $pendingAppointments = Appointment::query()->where('status', 'pending')->count();
        $activeServices = Service::query()->where('is_active', true)->count();
        $pendingOrders = Order::query()->where('status', 'pending')->count();
        $lowStockProducts = Product::query()
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->count();

        $timezone = 'Asia/Manila';
        $today = CarbonImmutable::now($timezone);
        $period = $request->query('period', 'today');

        if (! in_array($period, ['today', 'week', 'month', 'year'], true)) {
            $period = 'today';
        }

        $completedJobs = fn () => Appointment::query()
            ->where('status', 'completed')
            ->whereNotNull('service_completed_at');

        $completedCounts = [
            'today' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfDay()))->count(),
            'week' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfWeek(CarbonInterface::MONDAY)))->count(),
            'month' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfMonth()))->count(),
            'year' => $completedJobs()->where('service_completed_at', '>=', $this->utc($today->startOfYear()))->count(),
        ];

        $chart = $this->completedChart($period, $today, $timezone);
        $chartMax = max(1, ...array_column($chart, 'count'));

        return view('admin.dashboard', compact(
            'totalMechanics',
            'activeMechanics',
            'totalCustomers',
            'pendingAppointments',
            'activeServices',
            'pendingOrders',
            'lowStockProducts',
            'completedCounts',
            'chart',
            'chartMax',
            'period'
        ));
    }

    private function completedChart(string $period, CarbonImmutable $today, string $timezone): array
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
            $chart[$start->format('Y-m-d')] = [
                'label' => $start->format($labelFormat),
                'count' => 0,
            ];
        }

        $start = $currentStart->{$subtract}($length - 1);
        $end = $currentStart->{$add}();

        $completed = Appointment::query()
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

    private function periodStart(CarbonImmutable $date, string $unit): CarbonImmutable
    {
        if ($unit === 'Week') {
            return $date->startOfWeek(CarbonInterface::MONDAY);
        }

        $method = 'startOf'.$unit;

        return $date->{$method}();
    }

    private function utc(CarbonImmutable $date): string
    {
        return $date->setTimezone('UTC')->format('Y-m-d H:i:s');
    }
}
