<?php

/**
 * CacheClearer
 */
class CacheClearer
{
    /**
     * GET parameter name for cache clearing.
     * It must match $clear_cache_var setting in nginx config.
     */
    const CLEAR_CACHE_PARAM = 'yii_clear_cache_abc123';

    /**
     * Clear cache for $url
     * @param string $url absolute url
     */
    public static function clear($url)
    {
        //присоединить параметр для сброса кэша
        $param = self::CLEAR_CACHE_PARAM . '=1';
        if (strpos($url, '?') !== false) {
            $url .= '&' . $param;
        }
        else {
            $url .= '?' . $param;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
