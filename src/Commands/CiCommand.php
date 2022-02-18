<?php

namespace Muravian\CiModelGen\Commands;

use Muravian\CiModelGen\CiModelGen;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CiCommand extends BaseCommand
{

    protected $group = 'MURAVIAN';

    protected $name = 'GenModel:publish';

    protected $description = 'Publish Models in App\Models';

    protected $usage = 'GenModel:publish';

    public function run(array $params) {
        $CiModelGen = new CiModelGen();
        $CiModelGen->index();
    }

}