<?php

/**
 * Base class for ETSIS-CLI commands
 *
 * @package etsis-cli
 */
abstract class ETSIS_CLI_Command {

    /**
     * Transfers the handling to the appropriate method
     *
     * @param array $args
     * @param array $assoc_args
     */
    public function __construct( $args, $assoc_args ) {
        $no_args = empty( $args );

        $subcommand = array_shift( $args );

        if ( !method_exists( $this, $subcommand ) ) {
            // This if for reserved keywords in php (like list, isset)
            $subcommand = '_' . $subcommand;
        }

        if ( !method_exists( $this, $subcommand ) ) {
            if( !$no_args ) {
                $subcommand = ltrim( $subcommand, '_' );
                ETSIS_CLI::line( sprintf( 'etsis %s %s : not a valid command', ETSIS_CLI_COMMAND, $subcommand ) );
            }
            self::describe_command( get_class( $this ), ETSIS_CLI_COMMAND );
        } else {
            $this->$subcommand( $args, $assoc_args );
        }
    }
    
    static function describe_command( $class, $command ) {
        if ( method_exists( $class, 'help' ) ) {
            $class::help();
            return;
        }

        $methods = self::get_subcommands( $class );

        $out = "Usage: etsis $command";

        if ( empty( $methods ) ) {
            ETSIS_CLI::line( $out );
        } else {
            $out .= ' [' . implode( '|', $methods ) . ']';

            ETSIS_CLI::line( $out );
        }
    }

    /**
     * Get the list of subcommands for a class.
     *
     * @param string $class
     * @return array The list of methods
     */
    static function get_subcommands( $class ) {
        $reflection = new ReflectionClass( $class );

        $methods = array();

        foreach ( $reflection->getMethods() as $method ) {
            if ( !$method->isPublic() || $method->isStatic() || $method->isConstructor() )
                continue;

            $name = $method->name;

            // If reserved PHP keywords (eg list, isset...) using a leading underscore for the method (eg _list)
            if ( strpos( $name, '_' ) === 0 ) {
                $name = substr( $name, 1 );
            }

            $methods[] = $name;
        }

        return $methods;
    }

}

