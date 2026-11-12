<?php

$f = fopen('C:/Users/maike/.gemini/antigravity/brain/258f7896-a475-4931-90ad-e95ec5627c35/.system_generated/logs/transcript_full.jsonl', 'r');
$idx = 0;
while (($l = fgets($f)) !== false) {
    if (strpos($l, 'TUGAS PRAKTIKUM MODUL V') !== false) {
        $d = json_decode($l, true);
        if (isset($d['content'])) {
            file_put_contents(__DIR__.'/modul5_spec_full.txt', $d['content']);
            echo "Saved modul5_spec_full.txt from idx $idx, length: ".strlen($d['content'])."\n";
            break;
        }
    }
    $idx++;
}
fclose($f);
