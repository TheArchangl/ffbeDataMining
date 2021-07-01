<?php
    /**
     * User: aEnigma
     * Date: 15.02.2017
     * Time: 17:52
     */

    namespace Sol\FFBE;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use JetBrains\PhpStorm\ArrayShape;
    use Solaris\FFBE\AES;
    use Solaris\FFBE\Client\BaseClient;

    class Updater {
        public const DISCOVER_LIMIT = 10;

        /** @var string */
        protected string $directory;

        /** @var Client */
        protected Client     $guzzle;
        protected BaseClient $client;

        public function __construct(BaseClient $client, string $directory) {
            $this->client    = $client;
            $this->directory = $directory;
            $this->guzzle    = new Client([
                                              'version' => 1.1,
                                              'headers' => [
                                                  'User-Agent'       => 'CRI SBX Ver.0.86.00',
                                                  'Content-Encoding' => 'identity',
                                              ],
                                          ]);
        }

        public function getVersions(string $realname, string $language): array {
            $versions = array_merge(
                glob("{$this->directory}/{$realname}_v*.txt"),
                glob("{$this->directory}/{$language}/{$realname}_v*.txt")
            );

            if (empty($versions))
                return [0];

            $versions = array_map(static function ($file) {
                $file = basename($file, '.txt');
                $file = substr($file, strrpos($file, '_v') + 2);

                assert(is_numeric($file));

                return (int) $file;
            }, $versions);

            natsort($versions);

            return $versions;
        }

        public function run(array $files = [], bool $find_ver = false): array {
            $update_files = [];

            if (empty($files))
                $files = $this->client->files->getFiles();


            foreach ($files as $name) {
                [$name, $lang] = explode(':', $name) + [$name, '__'];

                $entry = GameFile::getEntry($name);
                if (! $entry)
                    continue;

                $version = max($this->getVersions($name, $lang));
                $result  = $this->updateFile($entry, $lang, $version, $find_ver);

                if ($result['new'] < $result['old'])
                    echo "\r{$entry->getName()} not found! (wrong type {$entry->getDlType()}?)\n";

                elseif ($result['new'] === $result['old'])
                    echo "\r{$entry->getName()} is up-to-date.";

                else {
                    echo "\r{$entry->getName()} updated! ({$result['old']}->{$result['new']})\n";
                    $update_files[$entry->getName()] = range($result['old'] + 1, $result['new']);
                }
            }

            return $update_files;
        }

        #[ArrayShape(['old' => 'int', 'new' => 'int'])]
        public function updateFile(GameFile $entry, string $language, int $version = 0, bool $discover = false): array {
            // check if next version exists
            $next_version   = $version + 1;
            $server_version = $version;

            if ($discover) {
                while (($next_version - $version) < static::DISCOVER_LIMIT) {
                    printf("\rTrying {$entry->getName()} v{$next_version}");
                    $download = $this->download($entry, $language, $next_version);

                    if ($download)
                        // set to latest server version
                        $server_version = $next_version;

                    $next_version++;
                }
            }
            else {
                while ($this->download($entry, $next_version))
                    $next_version++;

                $server_version = $next_version - 1;
            }

            return ['old' => $version, 'new' => $server_version];
        }


        protected function buildUri(string $type, string $hashname, string $language, int $version): string {
            /** @noinspection HttpUrlsUsage */

            return $language === '__'
                ? "http://lapis-dlc.gumi.sg/dlc_assets_prod/{$type}/Ver{$version}_{$hashname}.dat"
                : "http://lapis-dlc.gumi.sg/dlc_assets_prod/{$type}/{$language}/Ver{$version}_{$hashname}.dat";
        }

        protected function checkLength($uri): int {
            $response = $this->guzzle->head($uri);
            if ($response->getStatusCode() !== 200)
                return 0;

            $length = $response->getHeaderLine('Content-Length');
            return (int) $length;
        }

        protected function download(GameFile $entry, string $language, int $version): bool {
            try {
                $file = $language !== '__'
                    ? "{$this->directory}/{$language}/{$entry->getName()}_v{$version}.txt"
                    : "{$this->directory}/{$entry->getName()}_v{$version}.txt";

                if (file_exists($file))
                    return true;

                $uri    = $this->buildUri($entry->getDlType(), $entry->getFile(), $language, $version);
                $length = $this->checkLength($uri);
                if ($length === 0)
                    return false;

                // request
                $range    = sprintf('0-%d', $length - 1);
                $response = $this->guzzle->get($uri, ['headers' => ['Range' => $range]]);
                if ($response->getStatusCode() !== 200)
                    return false;

                // decode
                $data = $response->getBody()->getContents();
                $data = AES::decode($data, $entry->getKey(), $this->client::IV);

                // save
                file_put_contents($file, $data);

                return true;
            }
            catch (RequestException) {
                return false;
            }
        }
    }