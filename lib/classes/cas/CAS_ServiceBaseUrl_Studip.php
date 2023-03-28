<?php

class CAS_ServiceBaseUrl_Studip extends CAS_ServiceBaseUrl_AllowedListDiscovery
{
    public function __construct()
    {
        $protocol = $this->isHttps() ? 'https' : 'http';
        $allow_list = array_map(function($host) use ($protocol) {
            $host = preg_replace('/\/.*/', '', $host);
            return $protocol . '://' . $host;
        }, $GLOBALS['STUDIP_DOMAINS'] ?? []);

        parent::__construct($allow_list);
    }
}
