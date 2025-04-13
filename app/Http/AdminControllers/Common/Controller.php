<?php
namespace App\Http\AdminControllers\Common;

class Controller {

    public function mapRequestToObject($object, $request) {
        foreach ($request as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }
        return $object;
    }
}