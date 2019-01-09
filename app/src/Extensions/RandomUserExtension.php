<?php

namespace App\Extensions;

use SilverStripe\ORM\DataExtension;

class RandomUserExtension extends DataExtension
{
    private static $db = [
        'CellNo' => 'Varchar(20)',
        'LargePhoto' => 'Varchar(100)',
        'MediumPhoto' => 'Varchar(100)',
        'SmallPhoto' => 'Varchar(100)'
    ];
}
