<?php
// DIC configuration

$container = $app->getContainer();

$container['Controllers\checkpoints'] = function ($c) {
    return new controllers\checkpoints($c['router']);
};