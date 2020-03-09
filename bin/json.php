<?php
    /**
     * User: aEnigma
     * Date: 30.05.2017
     * Time: 23:51
     */

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';

    $data = @file_get_contents('json.json') ?: '{}';
    $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    $data = GameFile::replaceKeysRecursive($data);

    $data = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512);
    file_put_contents('json.json', $data);
    echo $data;