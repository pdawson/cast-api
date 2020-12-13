<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use RuntimeException;

class Generator
{
    /**
     * Generates a new config file based on stub and array of replacements
     *
     * @param string $stub
     * @param array $config
     * @return string
     */
    public function generate(string $stub, array $config): string
    {
        return $this->replaceVariables(
            $this->replaceConditionals(
                $this->getStubFile($stub),
                $config
            ),
            $config
        );
    }

    /**
     * Retrieves a stub file from the resources directory
     *
     * @param string $stub
     * @return false|string
     */
    protected function getStubFile(string $stub)
    {
        $path = resource_path("stubs/$stub");

        if (!file_exists($path)) {
            throw new RuntimeException(sprintf("Stub file [%s] does not exist!", $path));
        }

        return file_get_contents($path);
    }

    /**
     * Evaluates conditionals based on setting values.
     * If the setting evaluates to true, the inner content is retained
     * else, the inner content is discarded (setting off).
     *
     * @param string $contents
     * @param array $config
     * @return string|string[]|null
     */
    protected function replaceConditionals(string $contents, array $config)
    {
        return preg_replace_callback(
            '/{{#((?:[^}]|}[^}])+)}}([\S\s](?:[^{]|{[^{]+.*)+)*?{{\/(?:[^}]|}[^}])+}}/',
            static function ($match) use ($config) {
                [$original, $key, $contents] = $match;

                $condition = isset($config[$key])
                    ? filter_var($config[$key], FILTER_VALIDATE_BOOL)
                    : false;

                return $condition
                    ? $contents
                    : '';
            },
            $contents
        );
    }

    /**
     * Replaces variables within the stub file with the values from settings
     *
     * @param string $contents
     * @param array $config
     * @return string|string[]|null
     */
    protected function replaceVariables(string $contents, array $config)
    {
        return preg_replace_callback(
            '/{{((?:[^}]|}[^}])+)}}/',
            static function ($match) use ($config) {
                [$original, $key] = $match;
                $cast = null;

                if (strpos($key, ':') !== false) {
                    [$cast, $key] = explode(':', $key, 2);
                }

                if ($key === 'generation-time') {
                    return Carbon::now()->toIso8601ZuluString();
                }

                if (isset($config[$key])) {
                    $value = $config[$key];

                    if ($cast === 'b') {
                        return filter_var($value, FILTER_VALIDATE_BOOL)
                            ? 'on'
                            : 'off';
                    }

                    return $config[$key];
                }

                // Debug
                dd($match);

                return $match;
            },
            $contents
        );
    }
}
