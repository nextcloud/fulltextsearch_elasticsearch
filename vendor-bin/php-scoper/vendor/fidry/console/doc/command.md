## Console

- [Creating a command](#creating-a-command)
- [Configuring the Command](#configuring-the-command)
- [Registering the Command](#registering-the-command)
- [Executing the Command](#executing-the-command)
- [Console Output](#console-output)
- [Output Sections](#output-sections)
- [Console Input](#console-input)
- [Getting Services from the Service Container](#getting-services-from-the-service-container)
- [Command Lifecycle](#command-lifecycle)
- [Testing Commands](#testing-commands)
- [Logging Command Errors](#logging-command-errors)
- [Learn More](#learn-more)


### Creating a command

Commands are defined in classes implementing `Fidry\Console\Command\Command`. For example, you
may want to create a command to create a user:

```php
<?php declare(strict_types=1);

// src/Console/Command/CreateUserCommand.php
namespace App\Console\Command;

use Fidry\Console\ExitCode;
use Fidry\Console\IO;
use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;

final class CreateUserCommand implements Command
{
    public function getConfiguration(): Configuration
    {
        return new Configuration(
            // the name of the command (the part after "bin/console")
            'app:create-user',
            // ...
        );
    }

    public function execute(IO $io): int
    {
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return ExitCode::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return ExitCode::FAILURE;
    }
}
```

### Configuring the Command

You can optionally define a description, help message and the input options and arguments:

```php
<?php declare(strict_types=1);

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Fidry\Console\IO;
use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;

final class CreateUserCommand implements Command
{
    private bool $requirePassword;
    
    public function __construct(bool $requirePassword = false)
    {
        $this->requirePassword = $requirePassword;
    }
    
    public function getConfiguration(): Configuration
    {
        return new Configuration(
            // The name of the command (the part after "bin/console")
            'app:create-user',
            // The short description shown while running "php bin/console list"
            'Creates a new user.',
            // The full command description shown when running the command with
            // the "--help" option
            <<<'EOT'
            This command allows you to create a user...
            EOT,
            [
                new InputArgument(
                    'password',
                    $this->requirePassword ? InputArgument::REQUIRED : InputArgument::OPTIONAL,
                    'User password',
                ),
            ],
        );
    }
    
    public function execute(IO $io): int
    {
        // ...
    }
}
```

### Registering the Command

Symfony commands must be registered as services and tagged with the `webmozarts.console_command` 
tag. If you’re using the default `services.yaml` configuration, this is already done for you thanks
to autoconfiguration.

### Executing the Command

After configuring and registering the command, you can run it in the terminal:

```bash
php bin/console app:create-user
```

As you might expect, this command will do nothing as you didn't write any logic yet. Add your own
logic inside the `execute()` method.

### Console Output

The `execute()` method has access to the output stream to write messages to the console:

```php
// ...
public function execute(IO $io): int
{
    // outputs multiple lines to the console (adding "\n" at the end of each line)
    $io->writeln([
        'User Creator',
        '============',
        '',
    ]);

    // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
    // that generates and returns the messages with the 'yield' PHP keyword
    $io->writeln($this->someMethod());

    // outputs a message followed by a "\n"
    $io->writeln('Whoa!');

    // outputs a message without adding a "\n" at the end of the line
    $io->write('You are about to ');
    $io->write('create a user.');

    return ExitCode::SUCCESS;
}
```

Now, try executing the command:

```bash
php bin/console app:create-user
User Creator
============

Whoa!
You are about to create a user.
```

### Output Sections

The regular console output can be divided into multiple independent regions called “output sections”.
Create one or more of these sections when you need to clear and overwrite the output information.

Sections are created with the `ConsoleOutput::section()` method, which returns an instance of
`Symfony\Component\Console\Output\ConsoleSectionOutput`:

```php
// ...
use LogicException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

final class MyCommand implements Command
{
    public function execute(IO $io): int
    {
        if (!$io->getOutput() instanceof ConsoleOutputInterface) {
            throw new LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $section1 = $io->section();
        $section2 = $io->section();

        $section1->writeln('Hello');
        $section2->writeln('World!');
        // Output displays "Hello\nWorld!\n"

        // overwrite() replaces all the existing section contents with the given content
        $section1->overwrite('Goodbye');
        // Output now displays "Goodbye\nWorld!\n"

        // clear() deletes all the section contents...
        $section2->clear();
        // Output now displays "Goodbye\n"

        // ...but you can also delete a given number of lines
        // (this example deletes the last two lines of the section)
        $section1->clear(2);
        // Output is now completely empty!

        return ExitCode::SUCCESS;
    }
}
```

Note: A new line is appended automatically when displaying information in a section.

Output sections let you manipulate the Console output in advanced ways, such as displaying multiple
progress bars which are updated independently and appending rows to tables that have already been
rendered.

### Console Input

Use input options or arguments to pass information to the command:

```php
public function execute(IO $io): int
{
    $io->writeln([
        'User Creator',
        '============',
        '',
    ]);

    // retrieve the argument value using getArgument()
    $io->writeln('Username: '.$io->getStringArgument('username'));

    return ExitCode::SUCCESS;
}
```

Now, you can pass the username to the command:

```bash
php bin/console app:create-user Wouter
User Creator
============

Username: Wouter
```

See also: Read Console Input (Arguments & Options) for more information about console options and arguments.


### Getting Services from the Service Container

To actually create a new user, the command has to access some services. Since your command is
already registered as a service, you can use normal dependency injection. Imagine you have a
`App\Service\UserManager` service that you want to access:

```php
<?php declare(strict_types=1);

// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Service\UserManager;
use Fidry\Console\ExitCode;
use Fidry\Console\IO;
use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;

final class CreateUserCommand implements Command
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    // ...

    public function execute(IO $io): int
    {
        $this->userManager->create(
            $io->getStringArgument('username'),
        );

        $io->writeln('User successfully generated!');
        
        return ExitCode::SUCCESS;
    }
}
```


### Command Lifecycle

Commands have three lifecycle methods that are invoked when running the command:

- `initialize()` (when implementing `Fidry\Console\Command\InitializableCommand`).
  This method is executed before the `interact()` and the `execute()` methods. Its
  main purpose is to initialize variables that depends on the IO used in the rest
  of the command methods.
- `interact()` (when implementing `Fidry\Console\Command\InteractiveCommand`).
  This method is executed after `initialize()` and before `execute()`. Its purpose
  is to check if some of the options/arguments are missing and interactively ask
  the user for those values. This is the last place where you can ask for missing
  options/arguments. After this command, missing options/arguments will result in
  an error. Note that this method is ignored if the input is not interactive.
- `execute()`: This method is executed after `interact()` and `initialize()`. It
  contains the logic you want the command to execute and it must return an integer
  which will be used as the command exit status.


### Testing Commands

See [Symfony Testing Commands documentation][testing-commands].


### Logging Command Errors

Whenever an exception is thrown while running commands, Symfony adds a log message
for it including the entire failing command. In addition, Symfony registers an
event subscriber to listen to the `ConsoleEvents::TERMINATE` event and adds a
log message whenever a command does not finish with the `0` exit status.


### Learn More

- [How to Call Other Commands](./call-other-commands.md)

See [Symfony Learn More documentation][learn-more].


<br />
<hr />

« [Table of Contents](../README.md#table-of-contents) • [Application](application.md) »


[testing-commands]: https://symfony.com/doc/current/console.html#testing-commands
[learn-more]: https://symfony.com/doc/current/console.html#learn-more
