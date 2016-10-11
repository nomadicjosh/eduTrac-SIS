<?php
/**
 * Implement help command.
 *
 * ## EXAMPLES
 *
 *     # Show help screen.
 *     $ ./etsis help
 * 
 */
ETSIS_CLI::add_command('help', 'Help_Command');
class Help_Command extends ETSIS_CLI_Command
{

    public function __construct($args)
    {
        if (empty($args)) {
            $this->general_help();
            return;
        }

        $this->show_available_subcommands($args[0]);
    }

    private function show_available_subcommands($command)
    {
        $class = ETSIS_CLI::load_command($command);
        ETSIS_CLI_Command::describe_command($class, $command);
    }

    private function general_help()
    {
        ETSIS_CLI::line(<<<EOB

NAME

  etsis
      
DESCRIPTION
            
  Manage eduTrac SIS through command line.
            
SYNOPSIS
            
  etsis <command>

COMMANDS

  cli       Get information about ETSIS-CLI itself.
  core      Install, update and manage eduTrac SIS.
  help      Get help on ETSIS-CLI.
  db        Perform basic database operations.

OPTIONAL PARAMETERS    

  --require=<path>      Load a PHP file before running a command.
  --path=<path>         Path to eduTrac SIS files.
  --dir=<path>          Set path to locate file or where file should be saved.

FLAGS
  --verbose, -v         Turn on verbose output.
  --quiet, -q           Disable all output.
EOB
        );
    }
}
