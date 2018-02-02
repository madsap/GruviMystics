<?php

namespace app\components\interfaces;

/**
 * Interface SocialInterface
 * @author Aleksandr Mokhonko
 * Date: 05.06.17
 */
interface SocialInterface
{
    const ERROR_FAIL_CREDENTIAL     = 1;
    const ERROR_SOCIAL              = 2;
    const ERROR_SOFT                = 3;

    /**
     * @param array $credentials
     * @return $this
     */
    public function setCredentials($credentials = []);

    /**
     * @return mixed
     */
    public function getInfo();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return bool
     */
    public function hasErrors();
}