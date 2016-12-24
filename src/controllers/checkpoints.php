<?php
namespace controllers;

use DateTime;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class checkpoints extends controllers
{
    public function alexaGetCheckpoint(Request $request, Response $response, $args)
    {
        $message = "";
        $duration = new DateTime("@" . strval(18000 - (time() % 18000)));

        if (isset($duration)) {
            $seconds = $duration->getTimestamp();
            $message = (new DateTime("@0"))->diff($duration)->format(
                ($seconds >= 86400 ? '%d days, ' : '') .
                ($seconds >= 3600 ? '%h hours, ' : '') .
                ($seconds >= 60 ? '%i minutes, ' : '') .
                '%s seconds');
        }

        $response_object = array(
            "response" => array(
                "outputSpeech" => array(
                    "type" => "PlainText",
                    "text" => $message,
                ),
                "shouldEndSession" => true
            )
        );
        $response->getBody()->write(json_encode($response_object, false));
        return $response
            ->withStatus(200)
            ->withHeader('Content-type', 'application/json;charset=UTF-8');
    }
}