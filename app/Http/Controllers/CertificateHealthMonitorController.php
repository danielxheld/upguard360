<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CertificateHealthMonitorController extends Controller
{
    public function checkCertificate(Request $request): JsonResponse
    {
        $url = $request->input('url');

        try {
            $client = new Client();
            $response = $client->request('GET', $url, [
                'http_errors' => false,
                'verify' => false,
                'stream' => true,
                'connect_timeout' => 10,
                'timeout' => 10,
                'on_stats' => function ($stats) use (&$sslInfo) {
                    $sslInfo = $stats->getHandlerStats();
                },
            ]);

            if (isset($sslInfo['ssl_certificate'])) {
                $certificate = openssl_x509_parse($sslInfo['ssl_certificate']);
                if ($certificate) {
                    return response()->json([
                        'status' => 'success',
                        'certificate' => $this->formatCertificateInfo($certificate),
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve SSL certificate information. ' . openssl_error_string(),
            ]);

        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function formatCertificateInfo($certificate)
    {
        return [
            'issuer' => $certificate['issuer'],
            'subject' => $certificate['subject'],
            'valid_from' => date('Y-m-d H:i:s', $certificate['validFrom_time_t']),
            'valid_to' => date('Y-m-d H:i:s', $certificate['validTo_time_t']),
            'serial_number' => $certificate['serialNumber'],
            'signature_algorithm' => $certificate['signatureTypeLN'],
            'extensions' => $certificate['extensions'],
        ];
    }
}
