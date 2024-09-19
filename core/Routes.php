<?php

namespace App\Core;

use Exception;

/**
 * This is class stores and fetch all the registered routes on Layout.php
 */
class Routes
{
    /** @var string */
    private const BASE_ASSET_URL = "http://localhost/student-mark-management/assets";

    /** @var array */
    private static $getMethodRoutes = [];
    private static $postMethodRoutes = [];

    /**
     * This method stores the get method routes on getMethodRoutes
     */
    public static function get($route)
    {
        if (! array_key_exists('url', $route) || ! array_key_exists('controller', $route) ||
            ! array_key_exists('method', $route)
        ) {
            throw new Exception('Invalid route!');
        }

        self::$getMethodRoutes[] = $route;
    }

    /**
     * This method stores the post method routes on postMethodRoutes
     */
    public static function post($route)
    {
        if (! array_key_exists('url', $route) || ! array_key_exists('controller', $route) ||
            ! array_key_exists('method', $route)
        ) {
            throw new Exception('Invalid route!');
        }

        self::$postMethodRoutes[] = $route;
    }

    /**
     * This method is used to get the registered routes
     * @return array of routes
     */
    public static function getRoutes($method)
    {
        return $method == 'GET' ? self::$getMethodRoutes : self::$postMethodRoutes;
    }

    /**
     * This method returns the base url for the files to be included externally
     * @return string
     */
    public static function getAssestsUrl()
    {
        return self::BASE_ASSET_URL;
    }

    /**
     * This method includes the specified view file when called.
     */
    public static function load($file)
    {
        include_once "app/View/$file.php";
    }
}
