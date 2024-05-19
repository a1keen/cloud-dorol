<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateRateHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-rate-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        \App\Settings\GeneralSettings $settings
    )
    {
        // Получаем текущую дату
        $endDate = Carbon::now();

        // Вычитаем год из текущей даты, чтобы получить начальную дату
        $startDate = $endDate->copy()->subYear();

        // Инициализируем пустой массив для хранения дат
        $dateRange = [];

        // Создаем диапазон дат с разницей в 29 дней
        while ($startDate->lessThanOrEqualTo($endDate)) {

            $endDateRange = $startDate->copy()->addDays(28);

            if ($endDateRange->greaterThan(Carbon::yesterday())) {
                $endDateRange = Carbon::yesterday();
            }

            $dateRange[] = [
                'start_date' => $startDate->copy()->format('Y-m-d'),
                'end_date'   => $endDateRange->format('Y-m-d'),
            ];
            $startDate->addDays(29);
        }

        $items = [];

        // Теперь $dateRange содержит массив дат с разницей в 29 дней за последний год
        foreach ($dateRange as $range) {
            $data = json_decode(file_get_contents('https://metals-api.com/api/timeseries?access_key=' . config('api.metal_price.token') . '&start_date=' . $range['start_date'] . '&end_date=' . $range['end_date'] . '&symbols=USD&base=XAU'), true);
            if (isset($data['success']) && $data['success'] === true) {
                foreach ($data['rates'] as $date => $value) {
                    $items[$date] = number_format($value['USD'], 2, '.', '');
                }
            } else {
                Log::error('Error timeline', [$data, $range]);
            }
        }

        $settings->rate_time_series = $items;
        $settings->save();
        $this->info('Success updated');
    }
}
