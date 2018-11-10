<?php

namespace VdPoel\Concur\Contracts;

use GuzzleHttp\Exception\GuzzleException;

interface ResourceRoutes
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
     * @param array $params
     * @return mixed
     */
    public function create(array $params = []);

    /**
     * @param array $params
     * @return mixed
     */
    public function update(array $params = []);
}
