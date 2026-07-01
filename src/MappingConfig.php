<?php

namespace Mapping;

class MappingConfig 
{
    public static function getConfig(): array
    {
        return include(dirname(dirname(dirname(dirname(__FILE__)))).'/config/plugins/mapping.php');
    }
}