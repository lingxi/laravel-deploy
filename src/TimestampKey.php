<?php

namespace Lingxi\LaravelDeploy;

trait TimestampKey
{
    public function getEncryptedKey($key, $glue = '.', $reverse = false, $lastTime = false)
    {
        return md5($this->addKeyPerfix($key, $glue, $reverse, $lastTime));
    }

    public function addKeyPerfix($key, $glue = '.', $reverse = false, $lastTime = false)
    {
        if ($lastTime) {
            $timestamp = $this->getTimestamp(true);
        } else {
            $timestamp = $this->getTimestamp();
        }

        if (! $reverse) {
            return $timestamp.$glue.$key;
        } else {
            return $key.$glue.$timestamp;
        }
    }

    public function getTimestamp($lastTime = false)
    {
        $path = storage_path('deploy.config');

        if (file_exists($path)) {
            $deployConfig = unserialize(file_get_contents($path));

            if ($lastTime && isset($deployConfig['lastTimestamp'])) {
                $timestamp = $deployConfig['lastTimestamp'];
            } else {
                $timestamp = $deployConfig['timestamp'];
            }
        } else {
            $timestamp = time();

            file_put_contents($path, serialize([
                'timestamp' => $timestamp,
                'key' => config('app.key'),
            ]));
        }

        return $timestamp;
    }
}
