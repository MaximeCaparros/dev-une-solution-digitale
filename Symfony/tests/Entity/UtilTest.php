<?php

namespace App\Tests\Entity;

use App\Entity\Rentabilite;
use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\ManagerConfigurator;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{

    public function testRenta()
    {
        $rentabilite = new Rentabilite();
        $date= new \DateTime();
        $benefice = 25000;

        $rentabilite->setDate($date)
                    ->setBenefice($benefice);
        $this->assertEquals($benefice, $rentabilite->getBenefice());
        $this->assertEquals($date, $rentabilite->getDate());
    }

    public function testTransa()
    {

        $transa = new Transactions();
        $date = new \DateTime();
        $transa->setName('BitCoin')
            ->setPrice(1520)
            ->setCreatedAt($date)
            ->setQuantity(10);
        $this->assertEquals('BitCoin', $transa->getName());
        $this->assertEquals($date, $transa->getCreatedAt());
        $this->assertEquals(1520, $transa->getPrice());
        $this->assertEquals(10, $transa->getQuantity());
    }
    public function testSearchKey()
    {
        $crypto=$this->searchKey(1);
        $this->assertEquals('Bitcoin', $crypto[1]);
    }




    /**
     * @return array
     */
    private function recupData() :array
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $parameters = [
            'start' => '1',
            'limit' => '10',
            'convert' => 'EUR',
            'CMC_PRO_API_KEY' => 'e5b51a79-00ba-4a11-9cd3-e5130fd01d9c'
        ];

        $headers = [
            'Accepts: application/json',

        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $someArray = json_decode($response, true);
        curl_close($curl); // Close request


        $crypto =  array();

        $count = 0;
        foreach ($someArray['data'] as $key => $value)
        {
            $crypto[$count][0] = $value['id'];
            $crypto[$count][1] = $value['name'];
            $crypto[$count][2] = $value['symbol'];
            $crypto[$count][3] = $value["quote"]["EUR"]["price"];
            $crypto[$count][4] = $value["quote"]["EUR"]["percent_change_1h"];
            $crypto[$count][5] = $value["quote"]["EUR"]["percent_change_24h"];
            $crypto[$count][6] = $value["quote"]["EUR"]["percent_change_7d"];
            $crypto[$count][7] = $value["quote"]["EUR"]["percent_change_30d"];



            $count++;

        }
        return $crypto;
    }

    /**
     * @param int $key
     * @return array|false
     */
    private function searchKey(int $key): array
    {
        $crypto = $this->recupData();

        $count = 0;
        while ($key != $crypto[$count][0] and $count < count($crypto))
        {
            $count++;
        }
        if ($count >= count($crypto)){
            return (false);
        }
        return ($crypto[$count]);
    }


}
