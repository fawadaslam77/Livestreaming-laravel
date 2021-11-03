<?php
/**
 * Created by PhpStorm.
 * User: rehmanali
 * Date: 4/28/2017
 * Time: 5:51
 */

namespace App\Helpers;


class Helper
{

    public static function curlRequest($url, $method="GET", $data=[], $headers=[]){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($method=="POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            if(is_string($data)){
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($data);
            }
        }
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($ch);
        return ['headers'=>self::http_parse_headers($header), 'body'=>$body];
    }
    public static function http_parse_headers( $raw_headers )
    {
        $headers = array();
        $key = '';
        foreach(explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);
            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    //$headers[0] = trim($h[0]);
                    $http = explode(' ',trim($h[0]), 3);
                    if(count($http)==3) {
                        $headers['http-version'] = $http[0];
                        $headers['status-code'] = $http[1];
                        $headers['status-message'] = $http[2];
                    }
                }
            }
        }
        return $headers;
    }
}