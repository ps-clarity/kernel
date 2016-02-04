<?php
namespace Clarity\Console\DB;

use Clarity\Support\DB\Factory;
use Clarity\Console\SlayerCommand;

class SeedFactory extends SlayerCommand
{
    protected $name = 'db:seed:factory';

    protected $description = 'Seed based on the factories';

    public function slash()
    {
        $files = folder_files(config()->path->database . 'factories');

        $factory = new Factory;

        if ( !empty($files) ) {

            foreach ( $files as $file ) {

                $this->comment('Processing ' . basename($file) . '...');

                require $file;

                $this->info('Done.' . "\n");
            }
        }
    }
}
