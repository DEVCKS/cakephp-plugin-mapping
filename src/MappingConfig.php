<?php

namespace Mapping;

class MappingConfig 
{
    static public function getConfig(): array
    {
        return include(dirname(dirname(dirname(dirname(__FILE__)))).'/config/plugins/mapping.php');
    }
}