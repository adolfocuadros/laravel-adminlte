<?php
namespace App\Lib;

class Helper
{
    public static function RoutesCRUD($uri, $controller, $name, $item = '{id}')
    {
        \Route::get($uri, $controller.'@showIndex')->name($name);
        \Route::post($uri, $controller.'@store');
        \Route::get($uri.'/list', $controller.'@index')->name($name.'.list');
        \Route::get($uri.'/'.$item, $controller.'@show')->name($name.'.show');
        \Route::patch($uri.'/'.$item, $controller.'@update');
        \Route::delete($uri.'/'.$item, $controller.'@delete');
    }
}