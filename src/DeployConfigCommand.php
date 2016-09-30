<?php

namespace Lingxi\LaravelDeploy;

use Illuminate\Support\Str;
use Illuminate\Foundation\Console\KeyGenerateCommand;

class DeployConfigCommand extends KeyGenerateCommand
{
    protected $key;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key and update deploy config.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->key = $this->getRandomKey($this->laravel['config']['app.cipher']);

        if ($this->option('show')) {
            return $this->line('<comment>'.$this->key.'</comment>');
        }

        $path = base_path('.env');

        if (file_exists($path)) {
            $content = str_replace('APP_KEY='.$this->laravel['config']['app.key'], 'APP_KEY='.$this->key, file_get_contents($path));

            if (! Str::contains($content, 'APP_KEY')) {
                $content = sprintf("%s\nAPP_KEY=%s\n", $content, $this->key);
            }

            file_put_contents($path, $content);
        }

        $this->laravel['config']['app.key'] = $this->key;

        $this->info("Application key [$this->key] set successfully.");

        $this->updateDeployConfig();
    }

    protected function updateDeployConfig()
    {
        $path = storage_path('deploy.config');

        $initData = [
            'timestamp' => time(),
            'key' => $this->key,
        ];

        if (file_exists($path)) {
            $lastDeployConfig = unserialize(file_get_contents($path));

            $initData['lastTimestamp'] = $lastDeployConfig['timestamp'];
            $initData['lastKey'] = $lastDeployConfig['key'];
        }

        $content = serialize($initData);
        file_put_contents(storage_path('deploy.config'), $content);

        $this->info('Deploy config writed successfully!');
    }
}
