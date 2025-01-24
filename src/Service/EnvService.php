<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EnvService
{
private $params;

public function __construct(ParameterBagInterface $params)
{
$this->params = $params;
}

public function get(string $name): mixed
{
    dd($this->params);
    return $this->params->get($name);
}
}