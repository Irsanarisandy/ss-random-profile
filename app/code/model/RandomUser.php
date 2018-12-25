<?php

namespace App\Model;

use SilverStripe\ORM\DataObject;

class RandomUser extends DataObject
{
    private static $table_name = 'RandomUser';

    private static $db = [
        'SortOrder' => 'Int',
        'FirstName' => 'Varchar(50)',
        'LastName' => 'Varchar(50)',
        'Email' => 'Varchar(50)',
        'CellNo' => 'Varchar(20)',
        'LargePhoto' => 'Varchar(100)',
        'MediumPhoto' => 'Varchar(100)',
        'SmallPhoto' => 'Varchar(100)',
    ];

    private static $default_sort = [
        'SortOrder' => 'ASC'
    ];
}
