<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public string $baseURL = ''; 

    public array $allowedHostnames = [];

    public string $indexPage = '';

    public string $uriProtocol = 'REQUEST_URI';

    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    public string $defaultLocale = 'id';

    public bool $negotiateLocale = false;

    public array $supportedLocales = ['en', 'id'];

    public string $appTimezone = 'Asia/Jakarta';

    public string $charset = 'UTF-8';

    public bool $forceGlobalSecureRequests = false;

    public array $proxyIPs = [
        '0.0.0.0/0' => 'X-Forwarded-For'
    ];

    public bool $CSPEnabled = false;

    public function __construct()
    {
        parent::__construct();

        // Deteksi secara agresif apakah request datang dari HTTPS Proxy ngrok
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $protocol = 'https';
            // Paksa port lokal disembunyikan agar tidak memicu :8080 di link ngrok
            $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8080');
            $host = str_replace(':8080', '', $host); 
        } else {
            $protocol = 'http';
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8080';
        }

        $this->baseURL = $protocol . '://' . $host . '/';
    }


}
