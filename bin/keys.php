<?php
    /**
     * User: aEnigma
     * Date: 12.02.2018
     * Time: 23:14
     */

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . "/bootstrap.php";

    $blubb = [
        'jp' => <<<EOF
EOF
        ,
        'gl' => <<<EOF
EOF
        ,
    ];

    GameFile::init();

    foreach ($blubb as $region => $data) {
        $region = strtoupper($region);
        $data   = explode("\n", $data);
        $data   = array_map('trim', $data);
        foreach ($data as $line) {
            if (empty($line))
                continue;

            $row = explode("\t", $line);
            // $type = array_pop($row);
            $type = 'mst';

            switch ($type) {
                case 'mst':
                case 'txt':
                    [$name, $class, $hash, $pass] = $row;

                    if ($name == '-' && $hash == '-')
                        continue 2;

                    $entry = GameFile::getEntry($name)
                        ?? GameFile::getEntry($hash);

                    $type = strpos($name, 'F_TEXT') !== false ? 'txt' : 'mst';
                    if ($entry == null) {
                        $entry = new GameFile($name, $hash, $pass, $class, [$type]);
                        GameFile::addEntry($entry);
                    }
                    else {
                        if ($class != '-')
                            $entry->setClass($class);
                        if ($hash != '-')
                            $entry->setFile($hash);
                        if ($pass != '-')
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

                    break;

                default:
                    throw new LogicException($type);
            }
        }
    }

    GameFile::save();

    foreach (GameFile::getEntries() as $entry)
        if (in_array('', [$entry->getKey(), $entry->getName(), $entry->getFile()], true) ||
            in_array('-', [$entry->getKey(), $entry->getName(), $entry->getFile()], true))
            continue;
        else
            echo "{\"{$entry->getName()}\", new GameFileEntry(\"{$entry->getFile()}\", \"{$entry->getKey()}\")},\n";