<?php

namespace App\Controller;

use Page;
use PageController;
use GuzzleHttp\Client;
use SilverStripe\ORM\Queries\SQLInsert;
use GuzzleHttp\Exception\ConnectException;
use App\Model\RandomUser;

class RandomUserPageController extends PageController
{
    private static $allowed_actions = ['fetchRandom'];

    private function isValidData(array $data)
    {
        $firstName = $data['name']['first'];
        $lastName = $data['name']['first'];
        $email = $data['email'];
        $cell = $data['cell'];
        $largePhoto = $data['picture']['large'];
        $mediumPhoto = $data['picture']['medium'];
        $smallPhoto = $data['picture']['thumbnail'];

        if (!isset($firstName) || !is_string($firstName) || !ctype_alpha($firstName)) {
            return $this->httpError(403, "First name must be alphabetic!");
        }

        if (!isset($lastName) || !is_string($lastName) || !ctype_alpha($lastName)) {
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
        $select = RandomUser::get()->where(
            "FirstName = '".ucfirst($data['name']['first']).
            "' OR LastName = '".ucfirst($data['name']['last']).
            "' OR Email = '".$data['email'].
            "' OR CellNo = '".$data['cell'].
            "' OR LargePhoto = '".$data['picture']['large'].
            "' OR MediumPhoto = '".$data['picture']['medium'].
            "' OR SmallPhoto = '".$data['picture']['thumbnail']."'"
        );

        // $rawSQL = $select->sql();
        // var_dump($rawSQL);

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
            'timeout' => 3
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

        $insert = SQLInsert::create('RandomUser');

        $insert->addRow(
            [
                'FirstName' => ucfirst($obj['name']['first']),
                'LastName' => ucfirst($obj['name']['last']),
                'Email' => $obj['email'],
                'CellNo' => $obj['cell'],
                'LargePhoto' => $obj['picture']['large'],
                'MediumPhoto' => $obj['picture']['medium'],
                'SmallPhoto' => $obj['picture']['thumbnail'],
            ]
        );

        // $rawSQL = $insert->sql();
        // var_dump($rawSQL);

        $insert->execute();

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
