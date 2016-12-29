<?php
// DIC configuration

$container = $app->getContainer();

$container['Controllers\Checkpoints'] = function ($c) {
    return new controllers\checkpoints($c['router']);
};