<?php

namespace App\Controller;

use Page;
use PageController;
use App\Model\RandomUser;

class RandomUsersPageController extends PageController
{
    private static $allowed_actions = ['fetchAll'];

    public function fetchAll()
    {
        $randomUsers = RandomUser::get();

        return $this->customise(
            [
                'RandomUsers' => $randomUsers
            ]
        )->renderWith(['RandomUsersPage', Page::class]);
    }
}
