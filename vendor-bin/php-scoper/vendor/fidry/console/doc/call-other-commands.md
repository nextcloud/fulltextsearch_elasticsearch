## How to Call Other Commands

If a command depends on another one being run before it, you can call in the 
console command itself. This is useful if a command depends on another command 
or if you want to create a "meta" command that runs a bunch of other commands
(for instance, all commands that need to be run when the project's code has
changed on the production servers: clearing the cache, generating Doctrine
proxies, dumping web assets, …).

Use the `get()` or `find()` method of `Fidry\Console\Command\CommandRegistry` to
find the command you want to run by passing the command name. Then, create a new 
`Symfony\Component\Console\Input\ArrayInput` with the arguments and options you
want to pass to the command.

Eventually, calling the `execute()` method actually runs the command and returns
the returned code from the command (return value from command's `execute()` method):

```php
<?php declare(strict_types=1);

// src/Console/Command/CreateUserCommand.php
namespace App\Console\Command;

use Fidry\Console\Command\CommandAware;
use Fidry\Console\Command\CommandAwareness;
use Fidry\Console\IO;
use Fidry\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

final class CreateUserCommand implements Command, CommandAware
{
    use CommandAwareness;

    // ...

    public function execute(IO $io): int
    {
        $command = $this->getCommandRegistry()->find('demo:greet');
        
        $arguments = [
            'name'    => 'Fabien',
            '--yell'  => true,
        ];

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->execute(
            new IO(
                $greetInput,
                $io->getOutput(),
            ),
        );
        
        // ...
    }
}
```


<br />
<hr />

« [Console](command.md) • [Application](application.md) »
