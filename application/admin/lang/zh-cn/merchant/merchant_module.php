<?php
$moduleLang = include __DIR__.'/../general/module.php';

$merchanModuleLang = [
    'Opened'                 => '已开通',
    'Module'                 => '功能模块',
    'Include selected items' => '显示所选种类',
    'Exclude selected items' => '不显示所选种类'
];

return array_merge($moduleLang, $merchanModuleLang);
