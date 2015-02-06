<?php

function inject_property($array,$key,$value)
{
    foreach($array as $element)
    {
        $element->$key = $value;
    }
    return $array;
}
