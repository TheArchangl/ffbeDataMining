<?php
    /**
     * User: aEnigma
     * Date: 12.02.2018
     * Time: 23:14
     */

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . '/bootstrap.php';

    $blubb = array_filter([
        'gl' => file_get_contents(__DIR__ . '/keys.gl.txt'),
        'jp' => @file_get_contents(__DIR__ . '/keys.jp.txt') ?: '',
    ]);

    GameFile::init();

    foreach ($blubb as $region => $data) {
        $region = strtoupper($region);
        $data   = explode("\n", $data);
        $data   = array_map('trim', $data);
        foreach ($data as $line) {
            if (empty($line))
                continue;

            $row  = explode("\t", $line);
            $type = array_pop($row);


            switch ($type) {
                case 'mst':
                case 'txt':
                    [$name, $class, $hash, $pass] = $row;

                    if ($name === '-' && $hash === '-')
                        continue 2;

                    $entry = GameFile::getEntry($name)
                             ?? GameFile::getEntry($hash);

                    $type = str_contains($name, 'F_TEXT') ? 'txt' : 'mst';
                    if ($entry === null) {
                        $entry = new GameFile($name, $hash, $pass, $class, [$type]);
                        GameFile::addEntry($entry);
                    }
                    else {
                        if ($class !== '-')
                            $entry->setClass($class);
                        if ($hash !== '-')
                            $entry->setFile($hash);
                        if ($pass !== '-')
                            $entry->setKey($pass);
                    }

                    if (empty($entry->getNotes()) || in_array($entry->getNotes(), [['-'], ['']]))
                        $entry->setNotes([$type]);

                    // if (! in_array($region, $entry->getNotes())) {
                    //     $notes   = $entry->getNotes();
                    //     $notes[] = $region;
                    //     sort($notes);
                    //
                    //     $entry->setNote($notes);
                    // }

                    break;

                case 'request':
                    [$class, $hash, $pass, $url] = $row;


                    $class = str_replace('::', '', $class);
                    if (class_exists("\\Solaris\\FFBE\\Request\\{$class}"))
                        continue 2;

                    $path = ROOT_DIR . "/../ffbe-discord/src/Solaris/FFBE/Request/{$class}.php";
                    $url  = preg_replace('~/actionSymbol/(.*?).php~', '$1', $url);

                    file_put_contents($path, /** @lang text */ <<<EOF
<?php
    namespace Solaris\FFBE\Request;


    class {$class} extends BaseRequest {
        public static function getUrl(): string {
            return '{$url}';
        }
        
        public static function getRequestID(): string
        {
            return '{$hash}';
        }

        public static function getKey(): string {
            return '{$pass}';
        }
    }
EOF
                    );
                    break;

                default:
                    throw new LogicException($type);
            }
        }
    }

    // c# export
    GameFile::save();
    #die();
    foreach (GameFile::getEntries() as $entry)
        if (in_array('', [$entry->getKey(), $entry->getName(), $entry->getFile()], true) ||
            in_array('-', [$entry->getKey(), $entry->getName(), $entry->getFile()], true))
            continue;
        else
            echo "{\"{$entry->getName()}\", new GameFileInfo(\"{$entry->getFile()}\", \"{$entry->getKey()}\")},\n";