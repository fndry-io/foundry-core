<?php

namespace Foundry\Core\Console\Commands\Traits;

use Illuminate\Support\Facades\File;

trait MakesFilesFromStubs
{
    /**
     * @param string $stub The filename and path to the stub file
     * @return false|string
     */
    protected function getStubContents(string $stub)
    {
        return file_get_contents($stub);
    }

    /**
     * @param array $variables The array of variables. Keys are the variables to find, values are what should be replaced with
     * @param string $source The filename and path to the stub file
     * @return mixed
     */
    protected function makeStub(array $variables, string $source)
    {
        return str_replace(
            array_map(function($key){
                return '{{' . $key . '}}';
            }, array_keys($variables)),
            array_values($variables),
            $this->getStubContents($source)
        );
    }

    /**
     * Write the stub
     *
     * @param $filename
     * @param $path
     * @param $content
     * @return bool|false|int
     */
    protected function writeStub($filename, $path, $content)
    {
        $result = false;
        if (!file_exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $file = $path . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($file)) {
            if (!$this->confirm(sprintf('File %s exists, overwrite?', $file), 'yes')) {
                return false;
            }
        }

        $result = file_put_contents($file, $content);
        $this->line(sprintf('Made file %s at %s', $filename, $file));

        return $result;
    }

    /**
     * Append to file
     *
     * @param $filename
     * @param $path
     * @param $content
     * @return bool|false|int
     * @throws \Exception
     */
    protected function appendStub($filename, $path, $content)
    {
        $file = $path . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($file)) {
            throw new \Exception(sprintf('File %s not yet created! Did you forget?', $file));
        }

        $result = file_put_contents($file, file_get_contents($file) . $content);
        $this->line(sprintf('Updated file %s at %s', $filename, $file));

        return $result;
    }
}
