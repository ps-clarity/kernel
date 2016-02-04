<?php
namespace Clarity\Console\Make;

use Clarity\Console\SlayerCommand;
use Symfony\Component\Console\Input\InputArgument;

class ConsoleCommand extends SlayerCommand
{
    protected $name = 'make:console';

    protected $description = 'Generate a new console';

    public function slash()
    {
        $arg_name = ucfirst($this->input->getArgument('name'));

        $stub = file_get_contents(__DIR__ . '/stubs/makeConsole.stub');
        $stub = stubify(
            $stub, [
                'consoleName' => $arg_name,
            ]
        );

        $file_name = $arg_name . '.php';
        chdir(config()->path->console);
        $this->comment('Crafting Console...');

        if (file_exists($file_name)) {
            $this->error('   Console already exists!');
        } else {
            file_put_contents($file_name, $stub);
            $this->info('   Console has been created!');
        }
    }

    protected function arguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Console name to be used'],
        ];
    }
}
