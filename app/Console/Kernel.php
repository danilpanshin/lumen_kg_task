<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use LireinCore\YMLParser\YML;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        $schedule->call(function () {
            $urls = ["http://www.trenazhery.ru/market2.xml", "http://static.ozone.ru/multimedia/yml/facet/div_soft.xml"];
            $yml = new YML();
            try {
                foreach ($urls as $url) {
                $yml->parse($url);
                    foreach ($yml->getOffers() as $offer) {
                        
                            //$offerCategoryHierarchy = $shop->getCategoryHierarchy($offer->getCategoryId());
                            $offerData = $offer->getData();
                             DB::table('items')->insert(
                                ['url' => $offerData['url'], 
                                'price' => $offerData['price'],
                                'name' => ($offerData['name'] ?? $offerData['model']),
                                'picture' => ($offerData['pictures'][0] ?? 'default'),
                                'description' => ($offerData['description'] ?? 'default')
                                ]
                             );
                    
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
                        
            
            
            // $xml = new \XMLReader();
            // $xml->open("http://armprodukt.ru/bitrix/catalog_export/yandex.php");    
            // var_dump($xml);
            // while($xml->localName === 'offer') {
            //     $element = new \SimpleXMLElement($xml->readOuterXML());
              

            //     var_dump(11);
                    
            //         // DB::table('items')->insert(
            //         //     ['url' => $url, 'price' => $price]
            //         // );
                
            // }

            // $xml->close();
        })->everyMinute();
    }
}
