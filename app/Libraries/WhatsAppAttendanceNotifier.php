<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Exceptions\HTTPException;

class WhatsAppAttendanceNotifier
{
    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl = $this->normalizeApiUrl(trim((string) getenv('WHATSAPP_API_URL')));
        $this->apiToken = trim((string) getenv('WHATSAPP_API_TOKEN'));
    }

    public function isConfigured(): bool
    {
        return $this->missingConfiguration() === [];
    }

    public function missingConfiguration(): array
    {
        $missing = [];
        if ($this->apiUrl === '') {
            $missing[] = 'WHATSAPP_API_URL';
        }
        if ($this->apiToken === '') {
            $missing[] = 'WHATSAPP_API_TOKEN';
        }

        return $missing;
    }

    public function sendAttendance(string $phoneNumber, array $parameters): bool
    {
        $message = sprintf(
            "Yth. %s,\n\nKami informasikan kehadiran %s tercatat %s pada mata pelajaran %s di kelas %s tanggal %s.\n\nTerima kasih.",
            $parameters[0] ?? 'Orang Tua/Wali',
            $parameters[1] ?? 'siswa',
            $parameters[2] ?? '-',
            $parameters[3] ?? '-',
            $parameters[4] ?? '-',
            $parameters[5] ?? '-'
        );

        return $this->sendMessage($phoneNumber, $message);
    }

    public function sendRfidAttendance(
        string $phoneNumber,
        string $parentName,
        string $studentName,
        string $tapType,
        string $className,
        string $date,
        string $time
    ): bool {
        $tapLabel = $tapType === 'in' ? 'masuk sekolah' : 'pulang sekolah';
        $message = sprintf(
            "Yth. %s,\n\nKami informasikan %s telah melakukan tap RFID untuk %s pada %s pukul %s di kelas %s.\n\nTerima kasih.",
            $parentName !== '' ? $parentName : 'Orang Tua/Wali',
            $studentName,
            $tapLabel,
            $date,
            $time,
            $className !== '' ? $className : '-'
        );

        return $this->sendMessage($phoneNumber, $message);
    }

    private function sendMessage(string $phoneNumber, string $message): bool
    {
        $recipient = $this->normalizePhoneNumber($phoneNumber);
        if ($recipient === null) {
            log_message('warning', 'Fonnte attendance notification skipped because the parent number is invalid.');
            return false;
        }

        try {
            $response = service('curlrequest')->request(
                'POST',
                $this->apiUrl,
                [
                    'headers' => ['Authorization' => $this->apiToken],
                    'multipart' => [
                        'target' => $recipient,
                        'message' => $message,
                    ],
                    'http_errors' => false,
                    'timeout' => 15,
                ]
            );
        } catch (HTTPException $exception) {
            log_message('error', 'Fonnte attendance request failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        $statusCode = $response->getStatusCode();
        $result = json_decode($response->getBody(), true);
        if ($statusCode < 200 || $statusCode >= 300 || ! is_array($result) || ! in_array($result['status'] ?? false, [true, 1, 'true'], true)) {
            $detail = is_array($result) ? ($result['detail'] ?? $result['reason'] ?? 'Respons API tidak valid atau status pengiriman gagal.') : 'Respons API bukan JSON yang valid.';
            log_message('error', 'Fonnte attendance API failed with HTTP {status}: {detail}', [
                'status' => $statusCode,
                'detail' => mb_substr((string) $detail, 0, 500),
            ]);

            return false;
        }

        return true;
    }

    private function normalizeApiUrl(string $apiUrl): string
    {
        if ($apiUrl === '') {
            return '';
        }

        $parts = parse_url($apiUrl);
        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])) {
            return '';
        }

        $host = strtolower((string) $parts['host']);
        $path = rtrim((string) ($parts['path'] ?? ''), '/');
        if (! in_array($host, ['fonnte.com', 'api.fonnte.com'], true)
            || isset($parts['port'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])) {
            return '';
        }
        if ($host === 'fonnte.com' && $path === '') {
            return 'https://api.fonnte.com/send';
        }
        if ($host === 'api.fonnte.com' && in_array($path, ['', '/send'], true)) {
            return 'https://api.fonnte.com/send';
        }

        return '';
    }

    private function normalizePhoneNumber(string $phoneNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber);
        if (! is_string($digits) || $digits === '') {
            return null;
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return strlen($digits) >= 8 && strlen($digits) <= 15 ? $digits : null;
    }
}
