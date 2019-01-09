<?php

namespace App\Controllers;

use Page;
use PageController;
use SilverStripe\Security\Member;

class RandomUsersPageController extends PageController
{
    private static $allowed_actions = ['fetchAll'];

    public function fetchAll()
    {
        $randomUsers = Member::get()->filter(
            [
                'Email:not' => 'root'
            ]
        );

        return $this->customise(
            [
                'RandomUsers' => $randomUsers
            ]
        )->renderWith(['RandomUsersPage', Page::class]);
    }
}
