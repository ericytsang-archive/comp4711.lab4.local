<?php

function replace_placeholders($string,$placeholder,$replacement)
{
    $pattern = '/({'.$placeholder.'})/';
    return preg_replace($pattern,$replacement,$string);
}
