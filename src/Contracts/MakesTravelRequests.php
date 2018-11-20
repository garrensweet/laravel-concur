<?php

namespace VdPoel\Concur\Contracts;

interface MakesTravelRequests
{
    /**
     * @return string
     */
    public function firstName(): string;

    /**
     * @return string
     */
    public function lastName(): string;

    /**
     * @return string
     */
    public function email(): string;

    /**
     * @return string
     */
    public function ruleClass(): string;

    /**
     * @return string
     */
    public function loginID(): string;
}