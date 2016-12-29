<?php
// DIC configuration

$container = $app->getContainer();

$container['controllers\checkpoints'] = function ($c) {
    return new controllers\checkpoints($c['router']);
};