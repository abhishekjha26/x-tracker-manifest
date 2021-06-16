<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use function OpenApi\scan;

class SwaggerScan extends Command
{
    protected $signature = 'swg:scan';
    public function handle()
    {
        $path = dirname(dirname(__DIR__));
        $outputPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'public/swagger.json';
        $this->info('Scanning ' . $path);
        $openApi = scan($path);
        header('Content-Type: application/json');
        file_put_contents($outputPath, $openApi->toJson());
        $this->info('Output ' . $outputPath);
    }
}
