<?php


namespace App\Utils;


use JetBrains\PhpStorm\Pure;

class Utils
{
    public static function notNull(...$items): bool
    {
        $response = true;

        foreach ($items as $item){
            if($item = null) {
                $response = false;
                break;
            }
        }

        return $response;
    }

}
