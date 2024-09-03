<?php
namespace Studip\Cli\Commands\Make;

use Nette\PhpGenerator\Printer;

final class StudipClassPrinter extends Printer
{
    /** length of the line after which the line will break */
    public int $wrapLength = 120;
    /** indentation character, can be replaced with a sequence of spaces */
    public string $indentation = '    ';
    /** number of blank lines between properties */
    public int $linesBetweenProperties = 0;
    /** number of blank lines between methods */
    public int $linesBetweenMethods = 1;

    public string $returnTypeColon = ': ';
}
