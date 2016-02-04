<?php
namespace Clarity\Console\Clear;

use Clarity\Console\SlayerCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AllCommand extends SlayerCommand
{
    use ClearTrait;

    protected $name = 'clear:all';

    protected $description = 'Clear all listed';

    public function slash()
    {
        $this->clear(config()->path->cache);
        $this->clear(config()->path->logs);
        $this->clear(config()->path->session);
        $this->clear(config()->path->storage_views);
    }
}
