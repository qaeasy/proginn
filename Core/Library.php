<?php


namespace hitachi\Phrest\Core;

use Phalcon\Exception as PhalconException;
use hitachi\Phrest\Core\Utils;
use hitachi\Phrest\Models\VwEquipments;

class Library
{
    public function getMetaDataAttributes($data)
    {
        return $data->getModelsMetaData()->getAttributes($data);
    }

    public function getMetaDataTypes($data)
    {
        return $data->getModelsMetaData()->getDataTypes($data);
    }
}

// EOF
