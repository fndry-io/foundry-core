<?php


namespace Foundry\Core\Testing;


trait InteractsWithArrays
{
    /**
     * Assert that an array has the given keys in it
     *
     * @param $requiredKeys
     * @param $sourceArray
     */
    public function assertSubsetKeys($requiredKeys, $sourceArray)
    {
        $diff = array_diff($requiredKeys, array_keys($sourceArray));

        PHPUnit::assertEquals(
            0,
            count($diff),
            'Array does not have the required keys. Missing ' . implode(',', $diff)
        );
    }
}
