<?php
declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Participant as ParticipantService;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/api/v1/participants", name="fetch_participants", methods={"POST"})
     */
    public function fetchParticipants(Request $request, ParticipantService $service) : Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new Response('Bad input', 400);
        }

        if (!isset($data['terms']) || !isset($data['fields'])) {
            return new Response('Missing terms and/or fields in input', 400);
        }

        $limit = (int) ($data['limit'] ?? 100);;
        $offset = (int) ($data['offset'] ?? 0);;

        $data = $service->search($data['terms'], $data['fields'], $offset, $limit);

        if (in_array('application/json', $request->getAcceptableContentTypes(), true)) {
            return new JsonResponse($data);
        }

        return new Response(print_r($data, true));
    }

    /**
     * @Route("/api/v1/participants", name="participant_search_meta", methods={"OPTIONS"})
     */
    public function participantSearchMeta(ParticipantService $service) : Response
    {
        $data = [
            [
                'name' => 'Participant',
                'fields' => [
                    [
                        'name' => 'participant__id',
                        'displayName' => 'Id',
                        'checked' => false,
                        'removable' => false,
                    ],
                    [
                        'name' => 'participant__name',
                        'displayName' => 'Name',
                        'checked' => false,
                        'removable' => true,
                    ],
                    [
                        'name' => 'participant__email',
                        'displayName' => 'Email',
                        'checked' => false,
                        'removable' => true,
                    ],
                    [
                        'name' => 'participant__city',
                        'displayName' => 'City',
                        'checked' => false,
                        'removable' => true,
                    ],
                    [
                        'name' => 'language__name',
                        'displayName' => 'Language',
                        'checked' => false,
                        'removable' => true,
                    ],
                    [
                        'name' => 'country__name',
                        'displayName' => 'Country',
                        'checked' => false,
                        'removable' => true,
                    ],
                ],
            ],
        ];

        return new JsonResponse($data);
    }
}
