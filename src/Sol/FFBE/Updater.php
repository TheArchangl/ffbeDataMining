<?php
    /**
     * User: aEnigma
     * Date: 15.02.2017
     * Time: 17:52
     */

    namespace Sol\FFBE;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;

    class Updater {
        const DISCOVER_LIMIT = 10;

        protected $files = [];

        /** @var string */
        protected $directory;

        /** @var Client */
        protected $client;

        public function __construct($directory) {
            $this->directory = $directory;
            $this->client    = new Client([
                                              'version' => 1.1,
                                              'headers' => [
                                                  'User-Agent'       => 'CRI SBX Ver.0.86.00',
                                                  'Content-Encoding' => 'identity',
                                              ],
                                          ]);

            $this->files = [];
            foreach (GameFile::getEntries() as $entry) {
                $this->files[$entry->getName()] = $entry;
            }
        }

        function getVersions($realname) {
            $versions = glob("{$this->directory}/{$realname}_v*.txt");
            if (empty($versions))
                return [];

            $versions = array_map(function ($file) {
                $file = basename($file, '.txt');
                $file = substr($file, strrpos($file, "_v") + 2);

                assert(is_numeric($file));

                return (int)$file;
            }, $versions);

            natsort($versions);

            return $versions;
        }

        public function run($filter = [], $find_ver = false) {
            $update_files = [];

            foreach (GameFile::getEntries() as $entry) {
                if (!empty($filter) && !in_array($entry->getName(), $filter) && !in_array($entry->getFile(), $filter))
                    continue;

                $result = $this->updateFile($entry, $find_ver);

                if ($result['new'] < $result['old'])
                    echo "\r{$entry->getName()} not found! (wrong type {$entry->getDlType()}?)\n";

                elseif ($result['new'] == $result['old'])
                    echo "\r{$entry->getName()} is up-to-date.";

                else {
                    echo "\r{$entry->getName()} updated! ({$result['old']}->{$result['new']})\n";
                    $update_files[$entry->getName()] = range($result['old'] + 1, $result['new'], 1);
                }
            }

            return $update_files;
        }

        public function updateFile(GameFile $entry, $discover = false) {
            // get newest installed version
            $versions      = $this->getVersions($entry->getName());
            $local_version = empty($versions)
                ? 0
                : array_pop($versions);

            // check if next version exists
            $next_version   = $local_version + 1;
            $server_version = $local_version;

            if ($discover) {
                while (($next_version - $local_version) < static::DISCOVER_LIMIT) {
                    printf("\rTrying {$entry->getName()}_{$next_version}");
                    $download = $this->download($entry, $next_version);

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

            return ['old' => $local_version, 'new' => $server_version];
        }


        protected function buildUri($type, $hashname, $version) {
            return "http://lapis-dlc.gumi.sg/dlc_assets_prod/{$type}/Ver{$version}_{$hashname}.dat";
        }

        protected function checkLength($uri) {
            $response = $this->client->head($uri);
            if ($response->getStatusCode() !== 200)
                return 0;

            $length = $response->getHeaderLine('Content-Length');
            return (int)$length;
        }

        protected function download(GameFile $entry, $version) {
            try {
                $file = "{$this->directory}/{$entry->getName()}_v{$version}.txt";
                if (file_exists($file))
                    return true;

                $uri    = $this->buildUri($entry->getDlType(), $entry->getFile(), $version);
                $length = $this->checkLength($uri);
                if ($length == 0)
                    return false;

                // request
                $range    = sprintf("0-%d", $length - 1);
                $response = $this->client->get($uri, ['headers' => ['Range' => $range]]);
                if ($response->getStatusCode() !== 200)
                    return false;

                // save
                file_put_contents($file, $response->getBody());

                return true;
            }
            catch (RequestException $e) {
                return false;
            }
        }
    }