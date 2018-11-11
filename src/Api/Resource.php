<?php

namespace VdPoel\Concur\Api;

use VdPoel\Concur\Contracts\ResourceRoutes;

abstract class Resource extends Base implements ResourceRoutes
{
    /**
     * @return void
     * @throws \BadMethodCallException
     */
    public function all()
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param array $params
     * @return void
     * @throws \BadMethodCallException
     */
    public function get(array $params = [])
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param array $params
     * @return void
     * @throws \BadMethodCallException
     */
    public function create(array $params = [])
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param array $params
     * @return void
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
        return 'Bearer';
    }
}
