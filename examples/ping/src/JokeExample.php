<?php

namespace Colore\Examples\Ping;

use Colore\Interfaces\RequestHelper;

/**
 * The JokeExample class is an example class for remote calls.
 */
class JokeExample {
    private $jokes = [
        'A programmer was found death in the shower. The instructions read: lather, rise, repeat',
        "!false - it's funny because it's true!",
        "Why did the programmer quit his job? Because he couldn't get arrays...",
        "A programmer's wife tells him \"go to the store and get a gallon of milk," .
        " and if they have eggs, get a dozen\". He returns with 13 gallons of milk",
        "Why was the empty array stuck outside? It didn't have any keys"
    ];

    public function randomJoke(RequestHelper $cro) {
        $cro->setRenderProperty('joke', $this->jokes[array_rand($this->jokes)]);
    }

    public function getJoke(RequestHelper $cro) {
        $id = $cro->getRequestArgument('id');

        if (is_null($id) || !is_numeric((int) $id)) {
            $id = array_rand($this->jokes);
        }

        $cro->setRenderProperty('joke', $this->jokes[$id]);
    }
}
