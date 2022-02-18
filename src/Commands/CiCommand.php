<?php

namespace Muravian\CiModelGen\Commands;

use Muravian\CiModelGen\CiModelGen;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CiCommand extends BaseCommand
{

    protected $group = 'GenerateModeks';

    protected $name = 'CiModelGen:publish';

    protected $description = 'Publish Models in App\Models';

    protected $usage = 'CiModelGen:publish';

    public function run(array $params) {
        $CiModelGen = new CiModelGen();
        $CiModelGen->index();
    }

}