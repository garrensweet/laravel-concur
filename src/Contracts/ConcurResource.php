<?php

namespace VdPoel\Concur\Contracts;

use GuzzleHttp\Exception\GuzzleException;

interface ConcurResource
{
    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function all();

    /**
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function get(array $params = []);

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function create();

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function update();
}
