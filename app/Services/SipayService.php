<?php

namespace App\Services;

use App\Models\Invoice;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\RequestException;

class SipayService
{
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $app_code;
    protected $tokenCacheKey = 'sipay_token';
    protected $tokenCacheTime = 60; // in seconds

    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.sipay.base_url') . '/api/v1';
        $this->username = config('services.sipay.username');
        $this->password = config('services.sipay.password');
        $this->app_code = config('services.sipay.app_code');
    }

    /**
     * Login to Sipay API and get authorization token
     *
     * @return string|null
     */
    public function login()
    {
        // Check if we have a cached token
        if (Cache::has($this->tokenCacheKey)) {
            return decrypt(Cache::get($this->tokenCacheKey));
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/login', [
                'username' => $this->username,
                'password' => $this->password,
                'app_code' => $this->app_code,
            ]);

            // Check if the response is valid
            $body = json_decode($response->getBody(), true);

            if ($response->getStatusCode() == 200 && isset($body['data'])) {
                $token = $body['data'];

                // Store token in cache
                Cache::put($this->tokenCacheKey, encrypt($token), $this->tokenCacheTime);

                return $token;
            }

            Log::error('Sipay login failed', [
                'status' => $response->getStatusCode(),
                'response' => $body,
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('Sipay login exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get authorization headers with token
     *
     * @return array
     */
    protected function getAuthHeaders()
    {
        $this->token = $this->login();
        if (!isset($this->token['access_key'])) {
            throw new \Exception('Failed to fetch access_key');
        }

        return [
            'Authorization' => 'Bearer ' . $this->token['access_key'],
            'Accept' => 'application/json',
        ];
    }

    protected function getSecretKey()
    {
        $this->token = $this->login();
        if (!isset($this->token['secret_key'])) {
            throw new \Exception('Failed to fetch secret_key');
        }

        return $this->token['secret_key'];
    }

    /**
     * Create virtual account
     *
     * @param array $data
     * @return array|null
     */
    public function createVirtualAccount(Invoice $invoice)
    {
        $data = [
            'payment_type' => 'va-bpd',
            'total_amount' => (int)$invoice->amount,
            'id_billing' => $invoice->invoice_number,
            'nama' => $invoice->customer_name,
            'ket_1_val' => 'Badan Pendapatan Daerah',
            'ket_2_val' => 'Pembayaran EPBBKB',
            'ket_3_val' => 'va-bpd',
            'ket_4_val' => $invoice->invoice_number,
            'ket_5_val' => 'SISTEM EPBBKB',
            'unit_id' => 25,
            'type' => 'langsung',
            'rincian_tagihan' => [
                'kwitansi' => [
                    [
                        'nomor_kwitansi' => $invoice->receipt_number,
                        'kode_tujuan_pelimpahan' => "0110",
                        'nominal' => $invoice->amount,
                        'qty' => 1,
                        'keterangan' => $invoice->description,
                    ],
                ],
            ],
            // custom attribute
            'custom_attribute' => [
                'nama' => $invoice->customer_name,
                'email' => $invoice->customer_email,
                'alamat' => $invoice->customer_address,
                'no_tlp' => $invoice->customer_phone,
                'periode' => $invoice->description,
                'bulan' => $invoice->month,
                'tahun' => $invoice->year,
            ],
        ];

        // inject secret_key to data
        try {
            $data['secret_key'] = $this->getSecretKey();
            $response = Http::withHeaders($this->getAuthHeaders())
                ->post($this->baseUrl . '/transaction/va/create', $data);

            $body = json_decode($response->getBody(), true);
            // Check if the response is valid
            if ($response->getStatusCode() == 200 && isset($body['data']['data'])) {
                return $body['data']['data'];
            }
            Log::error('Sipay create virtual account failed', [
                'status' => $response->getStatusCode(),
                'response' => $body,
                'request_data' => $data,
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('Sipay create virtual account exception', [
                'message' => $e->getMessage(),
                'request_data' => $data,
            ]);

            return null;
        }
    }

    /**
     * Cancel invoice
     *
     * @param string $invoiceId
     * @return array|null
     */
    public function cancelInvoice(Invoice $invoice)
    {
        try {
            $response = Http::withHeaders($this->getAuthHeaders())
                ->post($this->baseUrl . '/transaction/cancel_invoice', [
                    'invoice_id' => $invoice->invoice_id,
                    'secret_key' => config('services.sipay.secret_key'),
                ]);
            // Check if the response is valid
            $body = json_decode($response->getBody(), true);
            if ($response->getStatusCode() == 200 && isset($body['data'])) {
                return $body['data'];
            }

            Log::error('Sipay cancel invoice failed', [
                'status' => $response->getStatusCode(),
                'response' => $body,
                'invoice_id' => $invoiceId,
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('Sipay cancel invoice exception', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoiceId,
            ]);

            return null;
        }
    }

    /**
     * Check transaction status
     *
     * @param string $invoiceId
     * @return array|null
     */
    public function checkTransactionStatus($invoiceId)
    {
        try {
            $response = $this->client->get('/transaction/status', [
                'headers' => $this->getAuthHeaders(),
                'query' => [
                    'invoice_id' => $invoiceId,
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            if ($response->getStatusCode() == 200) {
                return $body;
            }

            Log::error('Sipay check transaction status failed', [
                'status' => $response->getStatusCode(),
                'response' => $body,
                'invoice_id' => $invoiceId,
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('Sipay check transaction status exception', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoiceId,
            ]);

            return null;
        }
    }

    /**
     * Clear token from cache (logout)
     */
    public function clearToken()
    {
        Cache::forget($this->tokenCacheKey);
    }
}
