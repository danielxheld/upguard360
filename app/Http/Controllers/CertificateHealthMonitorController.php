<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;

class CertificateHealthMonitorController extends Controller
{
    public function checkCertificate($url): array
    {
        try {
            $urlComponents = parse_url($url);
            $targetHost = $urlComponents['host'];
            $targetPort = $urlComponents['port'] ?? 443;

            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $client = stream_socket_client("ssl://{$targetHost}:{$targetPort}", $errNo, $errStr, 10, STREAM_CLIENT_CONNECT, $context);

            if ($client) {
                $certificateContext = stream_context_get_params($client);
                if (isset($certificateContext['options']['ssl']['peer_certificate'])) {
                    $certificate = openssl_x509_parse($certificateContext['options']['ssl']['peer_certificate']);
                    if ($certificate) {
                        return [
                            'status' => 'success',
                            'certificate' => $this->formatCertificateInfo($certificate),
                        ];
                    } else {
                        return [
                            'status' => 'error',
                            'message' => 'Failed to parse SSL certificate information. Error: ' . openssl_error_string(),
                        ];
                    }
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to retrieve SSL certificate information. No certificate data found.',
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => "Failed to connect to {$url}. Error: {$errNo} - {$errStr}",
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

    }

    private function formatCertificateInfo($certificate): array
    {
        $issuerCN = $certificate['issuer']['CN'] ?? '';
        $subjectCN = $certificate['subject']['CN'] ?? '';
        $validFrom = date('Y-m-d H:i:s', $certificate['validFrom_time_t']);
        $validTo = date('Y-m-d H:i:s', $certificate['validTo_time_t']);
        $daysRemaining = floor(($certificate['validTo_time_t'] - time()) / (60 * 60 * 24));
        $lifetime = floor(($certificate['validTo_time_t'] - $certificate['validFrom_time_t']) / (60 * 60 * 24));

        $isSelfSigned = $issuerCN === $subjectCN;
        $isSHA1 = $certificate['signatureTypeLN'] === 'sha1WithRSAEncryption';

        $domains = [];
        if (isset($certificate['extensions']['subjectAltName'])) {
            $altNames = explode(', ', $certificate['extensions']['subjectAltName']);
            foreach ($altNames as $altName) {
                if (str_starts_with($altName, 'DNS:')) {
                    $domains[] = substr($altName, 4);
                }
            }
        }


        return [
            'issuer' => $issuerCN,
            'subject' => $subjectCN,
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
            'days_remaining' => $daysRemaining,
            'lifetime' => $lifetime,
            'serial_number' => $certificate['serialNumber'],
            'signature_algorithm' => $certificate['signatureTypeLN'],
            'extensions' => $certificate['extensions'],
            'is_self_signed' => $isSelfSigned,
            'is_sha1' => $isSHA1,
            'domains' => $domains,
        ];
    }
}
