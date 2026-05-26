<?php

function getServices(): array
{
    return [
        'male' => [
            'cut'   => ['name' => 'Hair cut', 'duration' => 30],
            'style' => ['name' => 'Styling',  'duration' => 60],
        ],
        'female' => [
            'ends'  => ['name' => 'Trim ends', 'duration' => 30],
            'style' => ['name' => 'Styling',   'duration' => 60],
            'color' => ['name' => 'Coloring',  'duration' => 120],
        ]
    ];
}

function getServiceDuration(string $gender, string $service): int
{
    $services = getServices();

    if (!isset($services[$gender][$service])) {
        throw new Exception("Invalid service");
    }

    return $services[$gender][$service]['duration'];
}
