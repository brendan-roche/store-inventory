<?php

namespace Inventory;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        // Currently we are just logging to standard output
        if ($level !== LogLevel::INFO) {
            echo ucwords($level) . ": ";
        }
        echo "$message\n";
    }
}
