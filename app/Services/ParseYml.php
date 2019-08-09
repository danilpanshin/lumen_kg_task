<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ParseYml
{
    public function parse()
    {
        if (file_exists("../" . "Tmp/ap.xml")) {
            unlink("../" . "Tmp/ap.xml");
        }
        file_put_contents("../" . "Tmp/ap.xml", file_get_contents("http://armprodukt.ru/bitrix/catalog_export/yandex.php"));
        $reader = new \XMLReader();

        if (!$reader->open("../" . "Tmp/ap.xml")) {
            die("Failed to open 'data.xml'");
        }

        while($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'offer') {
                $url = $reader->getAttribute('url');
                $price = $reader->getAttribute('price');
                
                DB::table('items')->insert(
                    ['url' => $url, 'price' => $price]
                );
            }
        }

        $reader->close();
    }
}

$p = new ParseYml;
$p->parse();