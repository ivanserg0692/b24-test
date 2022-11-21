<?php

namespace IvanSerg\Main\Activities\Properties;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

abstract class AbstractRequiredProperties
{

    static  abstract public function getArFieldKeys():array;
}