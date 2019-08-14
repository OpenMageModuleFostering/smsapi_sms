<?php

use SMSApi\Api\UserFactory;

class User
{

    private $userFactory;

    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    public function getPoints()
    {
        $actionPoints = $this->userFactory->actionGetPoints();

        return $actionPoints->execute()->getPoints();
    }
}
