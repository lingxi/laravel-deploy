<?php

use Lingxi\Deploy\Timestamp\TimestampKey;

class TimestampKeyTest extends PHPUnit_Framework_TestCase
{
    use TimestampKey;

    const KEY = 'deploy';

    const FILE_PATH = 'deploy.config';

    public function test_can_write_or_update_timestamp_to_a_file()
    {
        $excepted = time();
        $timestamp = $this->getTimestamp();

        $this->assertTrue(file_exists(storage_path(static::FILE_PATH)));
        $this->assertEquals($excepted, $timestamp);
    }

    public function test_twice_get_same_md5_string()
    {
        $this->assertEquals($this->getEncryptedKey(static::KEY), $this->getEncryptedKey(static::KEY));
    }

    public function tearDown()
    {
        unlink(storage_path(static::FILE_PATH));
    }
}

function storage_path($name)
{
    return __dir__.'/'.$name;
}

function config()
{
    return 'random_str';
}
