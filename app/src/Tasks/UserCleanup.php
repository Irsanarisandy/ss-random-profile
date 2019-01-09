<?php

namespace App\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;

class UserCleanup extends BuildTask
{
    protected $title = 'User Cleanup';

    protected $description = 'Delete users that has name beginning with "M".';

    protected $enabled = true;

    public function run($request)
    {
        $query = Member::get()->where(
            "FirstName LIKE 'M%' OR Surname LIKE 'M%'"
        );

        if ($query->count() > 0) {
            echo '<p>Total names beginning with "M": '.$query->count().' names</p>';
            echo '<p>These names are:</p>';

            foreach ($query as $row) {
                echo '<p>'.$row->FirstName.' '.$row->Surname.'</p>';
                $row->delete();
            }

            echo '<p>These names have been deleted.</p>';
        } else {
            echo '<p>No name beginning with "M" existed in the database.</p>';
        }
    }
}
