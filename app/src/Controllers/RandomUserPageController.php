<?php

namespace App\Controllers;

use Page;
use PageController;
use GuzzleHttp\Client;
use SilverStripe\Core\Convert;
use SilverStripe\Security\Member;
use GuzzleHttp\Exception\ConnectException;

class RandomUserPageController extends PageController
{
    private static $allowed_actions = ['fetchRandom'];

    private function isValidData(array $data)
    {
        $firstName = $data['name']['first'];
        $surname = $data['name']['last'];
        $email = $data['email'];
        $cell = $data['cell'];
        $largePhoto = $data['picture']['large'];
        $mediumPhoto = $data['picture']['medium'];
        $smallPhoto = $data['picture']['thumbnail'];

        if (!isset($firstName) || !is_string($firstName) || !ctype_alpha($firstName)) {
            return $this->httpError(403, "First name must be alphabetic!");
        }

        if (!isset($surname) || !is_string($surname) || !ctype_alpha($surname)) {
            return $this->httpError(403, "Last name must be alphabetic!");
        }

        if (!isset($email) || !is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->httpError(403, "Email must be valid!");
        }

        if (!isset($cell) || !is_string($cell) || preg_match("/[a-zA-Z]/i", $cell)) {
            return $this->httpError(403, "Cellphone no. must be numbers!");
        }

        if (!isset($largePhoto) || !is_string($largePhoto) || !in_array(exif_imagetype($largePhoto), [2, 3])) {
            return $this->httpError(403, "Large photo must be in jpg/jpeg or png!");
        }

        if (!isset($mediumPhoto) || !is_string($mediumPhoto) || !in_array(exif_imagetype($mediumPhoto), [2, 3])) {
            return $this->httpError(403, "Medium photo must be in jpg/jpeg or png!");
        }

        if (!isset($smallPhoto) || !is_string($smallPhoto) || !in_array(exif_imagetype($smallPhoto), [2, 3])) {
            return $this->httpError(403, "Small photo must be in jpg/jpeg or png!");
        }

        return true;
    }

    private function isDuplicate(array $data)
    {
        $select = Member::get()->filterAny(
            [
                'FirstName' => ucfirst($data['name']['first']),
                'Surname' => ucfirst($data['name']['last']),
                'Email' => $data['email'],
                'CellNo' => $data['cell'],
                'LargePhoto' => $data['picture']['large'],
                'MediumPhoto' => $data['picture']['medium'],
                'SmallPhoto' => $data['picture']['thumbnail'],
            ]
        );

        return $select->count() > 0;
    }

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
            'timeout' => 3,
            'connect_timeout' => 3
        ];

        $duplicateData = true;
        while ($duplicateData) {
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

            if (!is_array($obj) || !$this->isValidData($obj)) {
                return $this->httpError(403, "Returned data is not a valid array!");
            }

            $duplicateData = $this->isDuplicate($obj);
        }

        $member = Member::create();

        $member->FirstName = Convert::raw2sql(ucfirst($obj['name']['first']));
        $member->Surname = Convert::raw2sql(ucfirst($obj['name']['last']));
        $member->Email = Convert::raw2sql($obj['email']);
        $member->CellNo = Convert::raw2sql($obj['cell']);
        $member->LargePhoto = Convert::raw2sql($obj['picture']['large']);
        $member->MediumPhoto = Convert::raw2sql($obj['picture']['medium']);
        $member->SmallPhoto = Convert::raw2sql($obj['picture']['thumbnail']);

        $member->write();

        return $this->customise(
            [
                'FirstName' => ucfirst($obj['name']['first']),
                'Surname' => ucfirst($obj['name']['last']),
                'Email' => $obj['email'],
                'CellNo' => $obj['cell'],
                'LargePhoto' => $obj['picture']['large'],
                'MediumPhoto' => $obj['picture']['medium'],
                'SmallPhoto' => $obj['picture']['thumbnail'],
            ]
        )->renderWith(['RandomUserPage', Page::class]);
    }
}
