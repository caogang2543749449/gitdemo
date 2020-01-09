<?php
$moduleLang = include __DIR__.'/../general/module.php';

$merchanModuleLang = [
    'Opened'      => '利用中',
    'Module'      => 'モジュール',
    'Include selected items' => '選択されたカテゴリーを表示する',
    'Exclude selected items' => '選択されたカテゴリーを表示しない'
];

return array_merge($moduleLang, $merchanModuleLang);