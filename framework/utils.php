<?php
require_once 'Configuration.php';
require_once 'View.php';

class Utils {

    private static function base64url_encode($data){
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64url_decode($data){
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
    
    public static function url_safe_encode($data){
        return self::base64url_encode(gzcompress(json_encode($data), 9));
    }

    public static function url_safe_decode($data){
        return json_decode(@gzuncompress(self::base64url_decode($data)), true, 512, JSON_OBJECT_AS_ARRAY);
    }
}