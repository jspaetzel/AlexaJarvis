<?php
// Routes

$app->post('/get_checkpoint[/]', 'Controllers\Checkpoints:alexaGetCheckpoint')->setName('alexaCheckpoint');