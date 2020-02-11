<?php


function changeUIPhoneNumberToDbFormat($phone)
{
    $replacedText = ['(', ')', ' ', '-', '_'];
    return str_replace($replacedText, '', $phone);
}

function changeUIIbanNumberToDBFormat($ibanNumber)
{
    $replacedText = ['(', ')', ' ', '-', '_', '/', '['];
    return str_replace($replacedText, '', $ibanNumber);
}

function carSlugReplaceForJsonUnSlugLowerCase($value)
{
    $replaceTo = ['(', ')', ' ', '_', '/', '['];
    $value = str_replace($replaceTo, '', strtolower($value));
    return str_replace('-', ' ', $value);
}

function sqlInjectionProtection($sql)
{
    $sql = strtolower($sql);
    $replaceTo = ['insert', 'update', 'delete', 'drop', 'where', ' or '];
    str_replace($replaceTo, '', $sql);

    if ($sql == "" || $sql == '')
        return null;
    return preg_replace("/[\-]{2,}|[;]|[']|[\\\*]/", '', $sql);
}

function curLang()
{
    return session()->get('lang', config('app.locale'));
}
