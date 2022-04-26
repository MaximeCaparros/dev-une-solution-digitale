<?php

namespace App\Controller;


use App\Entity\Rentabilite;
use App\Entity\Transactions;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use function Sodium\add;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name ="default")
     * @return Response
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $crypto = $this->recupData();
        $em = $doctrine->getManager();
        $venteId = $em->getRepository(Transactions::class)->findAll();
        $somme=0;
        if ($venteId) {

            foreach ($venteId as $vente){
                if(!$vente->getsolded()){
                    foreach ($crypto as $crypt){
                        if($crypt[1] == $vente->getName()){
                            $somme += ($vente->getPrice()  - $crypt[3])* $vente->getQuantity();
                        }
                    }

                }
            }

        }

        $renta = new Rentabilite();
        $rentadujour = $em->getRepository(Rentabilite::class)->findOneBySomeDate( new \DateTime());

        if($rentadujour){
            if($rentadujour->getBenefice() <> $somme ){
                $rentadujour->setBenefice($somme);
                $rentadujour->setDate(new \DateTime());
                $em->persist($rentadujour);
                $em->flush();
            }else
            {
                return $this->render('default/index.html.twig', [
                    'allCrypto'=> $crypto,
                    'somme'=>$somme,

                ]);
            }
        }
        else
        {
            $renta->setDate(new \DateTime())
                ->setBenefice($somme);
            $em->persist($renta);
            $em->flush();
        }

      return $this->render('default/index.html.twig', [
            'allCrypto'=> $crypto,
            'somme'=>$somme,

        ]);

    }

    /**
     * @Route("/graph",methods={"GET"} , name="app_graph")
     */
   public function graph(ManagerRegistry $doctrine, ChartBuilderInterface $chartBuilder)
    {
        $em = $doctrine->getManager();
        $rentadujour = $em->getRepository(Rentabilite::class)->findAll();
        $date= [];
        $benef=  [];
        $count=0;
        if ($rentadujour){
            foreach ($rentadujour as $renta) {
                $date[$count]= date_format($renta->getDate(),'Y-m-d');
                $benef[$count]= $renta->getBenefice();
                $count++;
            }

        }


        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $date,
            'datasets' => [
                [
                    'label' => "Vos gains :",
                    'backgroundColor' => 'rgb(0, 0, 0)',
                    'borderColor' => 'rgb(31,195,108)',
                    'data' => $benef,
                ],
            ],
        ]);



    return  $this->render('default/graphique.html.twig',[
    'chart' => $chart,

    ]);

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