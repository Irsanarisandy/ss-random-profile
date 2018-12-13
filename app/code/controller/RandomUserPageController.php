<?php

namespace App\Controller;

use Page;
use PageController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class RandomUserPageController extends PageController
{
    private static $allowed_actions = ['fetchRandom'];

    public function fetchRandom()
    {
        $client = new Client(['base_uri' => 'https://randomuser.me']);
        $params = [
            'query' => [
                'nat' => 'nz',
                'inc' => implode(
                    ',',
                    [
                        'name',
                        'email',
                        'cell',
                        'picture'
                    ]
                )
            ],
            'timeout' => 3
        ];

        try {
            $res = $client->request('GET', '/api', $params);
        } catch (ConnectException $err) {
            return $this->httpError(500, $err->getMessage());
        }

        $status = $res->getStatusCode();

        if ($status !== 200) {
            return $this->httpError($status, "Can't generate random user profile!");
        }

        $obj = json_decode($res->getBody(), true)['results'][0];

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
