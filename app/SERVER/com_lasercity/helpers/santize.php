<?php defined('_JEXEC') or die;

function sanitize_output($buffer) {
    $buffer = preg_replace('/(?:(?<=\>)|(?<=\/\>))\s+(?=\<\/?)/', '', $buffer);
    if (strpos($buffer, '<pre') === false) {
        $buffer = preg_replace('/\s+/', ' ', $buffer);
    }
    $buffer = preg_replace('/[\t\r]\s+/', ' ', $buffer);
    $buffer = preg_replace('/<!(--)([^\[|\|])^(<!-->.*<!--.*-->)/', '', $buffer);
    $buffer = preg_replace('/\/\*.*?\*\//', '', $buffer);
    $buffer = preg_replace('/<!--[^\[].*-->/', '', $buffer);
    return $buffer;
}
