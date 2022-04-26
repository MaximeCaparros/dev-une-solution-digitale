<?php

namespace App\Controller;

use App\Form\AddTransactionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Transactions;
use Doctrine\Persistence\ObjectManager;


class TransactionController extends AbstractController
{
    /**
     * @Route("/transaction/addtransaction", name="addtransaction")
     */
    public function addTransaction(Request $requete,ManagerRegistry $doctrine )
    {



        $crypto = $this->recupData();

        if (!empty($_POST)){
            $split=explode(':',$requete->request->get('choixCrypto'));

          $this->entityManager = $doctrine->getManager();
            $transaction = new Transactions();


            $transaction->setName($split[0])
                ->setPrice($split[1])
                ->setQuantity($requete->request->get('quantity'))
                ->setCreatedAt(new \DateTime())
                ->setSolded(false);
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();

            return $this->redirectToRoute('transaction_liste');

        }



        return $this->renderForm('transaction/addtransaction.html.twig', [
            'allCrypto'=> $crypto,
        ]);
    }


    /**
     * @Route("/transaction", name="transaction_liste")
     */

    public function index(ManagerRegistry $doctrine): Response
    {

        $crypto = $this->recupData();



        $alltransaction = $doctrine->getRepository(Transactions::class)->findAll();
        return $this->render('transaction/transaction.html.twig', [
            'allCrypto'=> $crypto,
            'alltransaction' => $alltransaction,
        ]);

    }

    /**
     * @Route("/transaction/addvente/{id}/{name}", name="add_vente", requirements={"id"="\d+"}))
     */
    public function transaction(Transactions $id,string $name, Request $requete,ManagerRegistry $doctrine){

        $crypto = $this->recupData();

        $em = $doctrine->getManager();

        $venteId = $em->getRepository(Transactions::class)->find($id);






        if (!$venteId) {
            throw $this->createNotFoundException(
                'Pas de transaction pour cette crypto : '.$id
            );
        }
        if (!empty($_POST)){
            $priceAchat = $requete->request->get('prixAchat');
            $priceVente = $requete->request->get('prixVente');
            $quantity = $requete->request->get('quantity');

            $benefice = (($priceVente - $priceAchat) * $quantity);

            $venteId->setSolded(true)
                ->setBenefit($benefice)
                ->setSoldedAt(new \DateTime());

            $em->persist($venteId);
            $em->flush();


            return $this->redirectToRoute('transaction_liste');


        }
        $cryp = $this->searchKey($name);

        return $this->render('transaction/addvente.html.twig', [
            "TransactionID"=> $id,
            "crypto" =>$cryp,
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
    private function searchKey(string $key): array
    {
        $crypto = $this->recupData();

        $count = 0;
        while ($key != $crypto[$count][1] and $count < count($crypto))
        {
            $count++;
        }
        if ($count >= count($crypto)){
            return (false);
        }
        return ($crypto[$count]);
    }
}

