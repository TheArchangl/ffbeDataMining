<?php
    /**
     * User: aEnigma
     * Date: 15.02.2017
     * Time: 17:52
     */

    namespace Sol\FFBE;

    class UpdaterJP extends Updater {
        const DISCOVER_LIMIT = 5;
        //
        // public function run($filter = [], $find_ver = false) {
        //     $update_files = [];
        //
        //     $files = static::FILE_TYPE['mst'];
        //     $type  = 'mst';
        //
        //     if (count($filter) > 0)
        //         $files = array_intersect($files, $filter);
        //
        //     foreach ($files as $file) {
        //         $result = $this->updateFile($file, $type, $find_ver);
        //
        //         if ($result['new'] < $result['old'])
        //             echo "\r{$file} not found! (wrong type {$type}?)\n";
        //
        //         elseif ($result['new'] == $result['old'])
        //             echo "\r{$file} is up-to-date.";
        //
        //         else {
        //             $name = GameFile::getEntry($file);
        //             $name = $name instanceof GameFile
        //                 ? $name->getName()
        //                 : 'Unknown';
        //
        //             echo "\r{$file} updated! ({$result['old']}->{$result['new']}) ({$name})\n";
        //             $update_files[] = $file;
        //         }
        //     }
        //
        //     return $update_files;
        // }

        protected function buildUri($type, $hashname, string $language, $version) {
            return "http://cdnbase.resource.exvius.com/lapis/resource/mst/Ver{$version}_{$hashname}.dat";
        }
    }