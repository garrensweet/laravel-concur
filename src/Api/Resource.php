<?php

namespace VdPoel\Concur\Api;

use VdPoel\Concur\Contracts\ResourceRoutes;

abstract class Resource extends Base implements ResourceRoutes
{
    /**
     * @var string
     */
    protected $tokenType = 'Bearer';

    /**
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function all()
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function get(array $params = [])
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function create(array $params = [])
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function update(array $params = [])
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @return string
     */
    protected function tokenType(): string
    {
        return $this->tokenType;
    }
}
