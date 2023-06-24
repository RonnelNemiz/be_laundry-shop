<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function paginated($query, $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        return $query->paginate($limit);
    }

    public function deliverNotification($customer, $message)
    {
        $to = $customer->contact_number;
        $from = env("SEMAPHORE_FROM");

        $client = new Client();

        $response = $client->request('POST', 'https://api.semaphore.co/api/v4/messages', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env("SEMAPHORE_API_KEY"),
            ],
            'json' => [
                'number' => $to,
                'apikey' => env('SEMAPHORE_API_KEY'),
                'from' => $from,
                'message' => $message,
            ],
        ]);

        
        $statusCode = $response->getStatusCode();
        $responseData = json_decode($response->getBody(), true);
    }
}
