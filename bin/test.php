<?php
    $data = file_get_contents("data/items.json");
    $data = json_decode($data, true);


    $csv = [];
    foreach ($data as $id => $row) {
        $csv[] = join("\t", [$row['type'], $id, $row['name'], $row['strings']['desc_short'][0] ?? '']);
    }


    file_put_contents("items.tsv", join("\n", $csv));