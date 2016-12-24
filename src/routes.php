<?php
// Routes

$app->post('/alexa/getCheckpoint[/]', 'controllers\checkpoints:alexaGetCheckpoint')->setName('alexaCheckpoint');



