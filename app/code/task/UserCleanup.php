<?php

namespace App\Task;

use App\Model\RandomUser;
use SilverStripe\Dev\BuildTask;

class UserCleanup extends BuildTask
{
    protected $title = 'User Cleanup';

    protected $description = 'Delete users that has name beginning with "M".';

    protected $enabled = true;

    public function run($request)
    {
        $query = RandomUser::get()->where(
            "FirstName LIKE 'M%' OR LastName LIKE 'M%'"
        );

        if ($query->count() > 0) {
            echo '<p>Total names beginning with "M": '.$query->count().' names</p>';
            echo '<p>These names are:</p>';

            foreach ($query as $row) {
                echo '<p>'.$row->FirstName.' '.$row->LastName.'</p>';
                $row->delete();
            }

            echo '<p>These names have been deleted.</p>';
        } else {
            echo '<p>No name beginning with "M" existed in the database.</p>';
        }
    }
}
