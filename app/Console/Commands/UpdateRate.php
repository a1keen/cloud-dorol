<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Console\Command;

class UpdateRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update metal rate in usd and uah';

    /**
     * Execute the console command.
     */
    public function handle(
        \App\Settings\GeneralSettings $settings
    )
    {
        $response = $this->getRate();
        $responseUAH = $this->getRateInUAH();

        // Если успешно, обновляем
        if (isset($response->rates->USD)) {
            $rates = [
                'usd' => number_format($response->rates->USD, 2, '.', ''),
                'uah' => 0
            ];

            if (isset($responseUAH)) {
                $rates['uah'] = number_format($response->rates->USD * $responseUAH, 2, '.', '');
            }

            $settings->rate = $rates;
            $settings->save();
            $this->info('Success updated');
        } else {
            $this->error('Error code: ' . $response->error->statusCode . ' | ' . $response->error->message);
        }
    }

    public function getRate()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://metals-api.com/api/latest?access_key=' . config('api.metal_price.token') . '&base=XAU&symbols=USD',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function getRateInUAH()
    {
        $privateBank = (new Client())
            ->sendAsync(
                new GuzzleRequest(
                    method: 'GET',
                    uri: 'https://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11'
                )
            )->wait();
        $data = json_decode($privateBank->getBody()->getContents(),true);

        return $data[1]['sale'] ?? null;
    }
}
