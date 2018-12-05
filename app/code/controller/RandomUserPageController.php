<?php

namespace App\Controller;

use PageController;
use Page;

class RandomUserPageController extends PageController
{
    private static $allowed_actions = ['fetchRandom'];

    public function fetchRandom()
    {
        $json = file_get_contents('https://randomuser.me/api/?nat=nz&inc=name,email,cell,picture', false);
        $obj = json_decode($json, true)['results'][0];
        return $this->customise(
            [
                'FirstName' => ucfirst($obj['name']['first']),
                'LastName' => ucfirst($obj['name']['last']),
                'Email' => $obj['email'],
                'CellNo' => $obj['cell'],
                'LargePhoto' => $obj['picture']['large'],
                'MediumPhoto' => $obj['picture']['medium'],
                'SmallPhoto' => $obj['picture']['thumbnail'],
            ]
        )->renderWith(['RandomUserPage', Page::class]);
    }
}
