<?php

if (!function_exists('getResponse')) {

    function getResponse($e){
        $resposes = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'data' => [],
        ];

        return response($resposes, 200);
    }
}
