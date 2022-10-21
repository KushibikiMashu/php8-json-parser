<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class PHPUnitManager
{
    public function run(array $classes): string
    {
        if (count($classes) === 0) {
            return 'No test executed.';
        }

        $className = $this->join($classes);
        $className = $this->format($className);

        exec("./Vendor/phpunit/phpunit/phpunit tests --filter '($className)'", $output);

        return implode(PHP_EOL, $output);
    }

    private function join(array $classes): string
    {
        return implode('|', $classes);
    }

    private function format(string $className): string
    {
        return str_replace('\\', '\\\\', $className);
    }
}
