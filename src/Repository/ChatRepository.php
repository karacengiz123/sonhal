<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\SiebelLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @throwable
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function createChatActivity(Chat $chat, $createActivityLink)
    {
        $em = $this->getEntityManager();

        $header = [
            'apikey' => 'jQmt1055jbbGjWeGJAQ4knAdqJ3auaMr',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $webChatHistory = "";
        $chatMessages = $chat->getChatMessages()->toArray();
        $citizenDecoded = json_decode($chat->getCitizen());
        $citizenName = $citizenDecoded->Name . " " . $citizenDecoded->Surname;

        try {
            $chatRequestBody = [
                'body' => [
                    'Description' => 'Sohbet Gelen Aktivitesi - ' . $chat->getCreatedAt()->format('m/d/Y H:i:s'),
                    'Planned' => $chat->getCreatedAt()->format('m/d/Y H:i:s'),
                    'Started' => $chat->getCreatedAt()->format('m/d/Y H:i:s'),
                    'PlannedCompletion' => $chat->getUpdatedAt()->format('m/d/Y H:i:s'),
                    'UserName' => $chat->getUser()->getUsername(),
                    'Type' => 'Sohbet - Gelen',
                    'ActivitySubtype' => 'Genel',
                    'Status' => 'Tamamlandı',
                    'ContactTckn' => $citizenDecoded->Uid,
                    'CallId' => "TbxPbxSystemChatIdNumber" . Uuid::uuid4()->toString()
                ]
            ];
        } catch (\Exception $e) {
        }
        /** @var ChatMessage $chatMessage */
        foreach ($chatMessages as $chatMessage) {
            if ($chatMessage->getSender() == 3) {
                 $webChatHistory .= "System [ " . $chatMessage->getCreatedAt()->format('d-m-Y H:i:s') . " ] \n";
                 $webChatHistory .= $chatMessage->getMessage() . "\n";
            } elseif ($chatMessage->getSender() == 2) {
                $webChatHistory .= $chatMessage->getChat()->getUser()->__toString() . " [" . $chatMessage->getCreatedAt()->format('d-m-Y H:i:s') . "] \n";
                $webChatHistory .= $chatMessage->getMessage() . "\n";
            } elseif ($chatMessage->getSender() == 1) {
                $webChatHistory .= $citizenName . " [ " . $chatMessage->getCreatedAt()->format('d-m-Y H:i:s') . " ] \n";
                $webChatHistory .= $chatMessage->getMessage() . "\n";
            }
        }
        $parseString = "---iobarnidante--";
        $wordWrappedMessages = wordwrap(json_encode($webChatHistory, JSON_UNESCAPED_UNICODE), 4000, $parseString);
        $messagesArray = explode($parseString, $wordWrappedMessages);


        foreach($messagesArray as $message) {
            $chatRequestBody['body']['WebChatHistory'] = $message;

            dump($chatRequestBody);


            $client = new \GuzzleHttp\Client(['headers' => $header]);
            $res = $client->post($createActivityLink, ['body' => json_encode($chatRequestBody,JSON_UNESCAPED_UNICODE)]);
            $result_1 = $res->getBody()->getContents();

            $result = json_decode($result_1, true);

            if (isset($result["ActivityNumber"])) {
                $activityIds[] = $result["ActivityNumber"];
            }
            $date = new \DateTime();
            $siebelLog = new SiebelLog();
            $siebelLog
                ->setCallid($chatRequestBody["body"]["CallId"])
                ->setResponse($result_1)
                ->setRequest(json_encode($chatRequestBody))
                ->setCreatedDate($date)
                ->setActivityId($result["ActivityNumber"]??'')
                ->setSRId("")
                ->setContactId("");
            $em->persist($siebelLog);
            $em->flush();
        }


        if(count($activityIds)>0) {
            $chat->setActivityId(substr(implode(" - ",$activityIds),0,254));
            $em->persist($chat);
            $em->flush();
        }

        return ["result" => $result, "body" => $chatRequestBody];
    }
}
