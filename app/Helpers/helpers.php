<?php

if (! function_exists('get_mac_address')) {
    /**
     * Return MAC address for given IP (or server MAC if IP is server IP).
     *
     * @param  string|null  $ip
     * @return string|null
     */
    function get_mac_address(?string $ip = null): ?string
    {
        if($ip !== $_SERVER['SERVER_ADDR']) {
            $macAddr = false;
            $arp = `arp -a $ip`;
            $lines = explode("\n", $arp);
            foreach ($lines as $line) {
                $cols = preg_split('/\s+/', trim($line));
                if ($cols[0] == $ip) {
                    if(isset($cols[1])) {
                        $macAddr = strtoupper($cols[1]);
                    }else{
                        $macAddr = '';
                    }
                }
            }
        }else {
            $macAddr = substr(exec('getmac'), 0, 17);
        }

        return $macAddr;
    }
}