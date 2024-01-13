<?php
/*
 * Copyright (C) 2021 Kacper Donat
 *
 * @author Kacper Donat <kacper@kadet.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Controller\Api\v1;

use App\Controller\Controller;
use App\Dto\CollectionResult;
use App\Dto\Departure;
use App\Dto\Stop;
use App\Dto\Track;
use App\Filter\Binding\Http\EmbedParameterBinding;
use App\Filter\Binding\Http\FieldFilterParameterBinding;
use App\Filter\Binding\Http\IdConstraintParameterBinding;
use App\Filter\Binding\Http\LimitParameterBinding;
use App\Filter\Binding\Http\ParameterBinding;
use App\Filter\Binding\Http\ParameterBindingGroup;
use App\Filter\Binding\Http\ParameterBindingProvider;
use App\Filter\Binding\Http\RelatedFilterParameterBinding;
use App\Filter\Requirement\Embed;
use App\Filter\Requirement\FieldFilter;
use App\Filter\Requirement\FieldFilterOperator;
use App\Filter\Requirement\IdConstraint;
use App\Filter\Requirement\RelatedFilter;
use App\Filter\Requirement\Requirement;
use App\Provider\DepartureRepository;
use App\Provider\StopRepository;
use App\Provider\TrackRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Stops")
 * @OA\Parameter(ref="#/components/parameters/provider")
 */
#[Route(path: '/{provider}/stops', name: 'stop_')]
class StopsController extends Controller
{
    /**
     * List stops.
     *
     * @OA\Response(
     *     response=200,
     *     description="List of stops matching given criteria.",
     *     @OA\MediaType(
     *          mediaType="application/vnd.cojedzie.collection+json",
     *          @OA\Schema(ref=@Model(type=CollectionResult::class))
     *     ),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Stop::class)))
     * )
     *
     * @psalm-param iterable<Requirement> $requirements
     */
    #[Route(path: '', methods: ['GET'], name: 'list', options: ['version' => '1.1'])]
    #[ParameterBindingProvider([__CLASS__, 'getParameterBinding'])]
    public function index(
        StopRepository $stopRepository,
        array $requirements
    ): Response {
        $stops = $stopRepository->all(...$requirements);

        return $this->apiResponseFactory->createCollectionResponse($stops);
    }

    /**
     * Get information about specific stop.
     *
     * @OA\Response(
     *     response=200,
     *     description="Stop details.",
     *     @OA\MediaType(
     *          mediaType="application/vnd.cojedzie.stop+json",
     *          @OA\Schema(ref=@Model(type=Stop::class))
     *     ),
     * )
     */
    #[Route(path: '/{stop}', name: 'details', methods: ['GET'], options: ['version' => '1.2'])]
    public function one(
        StopRepository $stopRepository,
        #[IdConstraintParameterBinding(parameter: 'stop', from: ['attributes'])]
        IdConstraint $id
    ): Response {
        $stop = $stopRepository->first(
            $id,
            new Embed("destinations")
        );

        return $this->apiResponseFactory->createResponse($stop);
    }

    /**
     * List tracks containing specific stop.
     *
     * @OA\Response(
     *     response=200,
     *     description="List of tracks",
     *     @OA\MediaType(
     *          mediaType="application/vnd.cojedzie.collection+json",
     *          @OA\Schema(ref=@Model(type=CollectionResult::class))
     *     ),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Track::class)))
     * )
     */
    #[Route(path: '/{stop}/tracks', name: 'tracks', methods: ['GET'], options: ['version' => '1.1'])]
    public function tracks(
        TrackRepository $trackRepository,
        #[RelatedFilterParameterBinding(parameter: 'stop', resource: Stop::class, from: ['attributes'])]
        RelatedFilter $stop
    ): Response {
        $stops = $trackRepository->stops($stop);

        return $this->apiResponseFactory->createCollectionResponse($stops);
    }

    /**
     * List departures for given stop.
     *
     * @OA\Response(
     *     description="List of departures valid at the time of the request",
     *     response=200,
     *     @OA\MediaType(
     *          mediaType="application/vnd.cojedzie.collection+json",
     *          @OA\Schema(ref=@Model(type=CollectionResult::class))
     *     ),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Departure::class)))
     * )
     */
    #[Route(path: '/{stop}/departures', name: 'departures', methods: ['GET'], options: ['version' => '1.2'])]
    #[LimitParameterBinding]
    public function departures(
        DepartureRepository $departureRepository,
        StopRepository $stopRepository,
        #[IdConstraintParameterBinding(parameter: 'stop', from: ["attributes"])]
        IdConstraint $stop,
        array $requirements
    ): Response {
        $stops = $stopRepository->all($stop);

        $departures = $departureRepository->current(
            $stops,
            ...$requirements
        );

        return $this->apiResponseFactory->createCollectionResponse($departures);
    }

    /**
     * @psalm-return ParameterBinding[]
     */
    public static function getParameterBinding(): ParameterBinding
    {
        return new ParameterBindingGroup(
            new IdConstraintParameterBinding(
                documentation: [
                    'description' => 'Stop unique identifier as provided by data provider.',
                ]
            ),
            new LimitParameterBinding(),
            new EmbedParameterBinding(['destinations']),
            new FieldFilterParameterBinding(
                parameter: 'name',
                field: 'name',
                defaultOperator: FieldFilterOperator::Contains,
                operators: FieldFilterParameterBinding::STRING_OPERATORS,
                options: [
                    FieldFilter::OPTION_CASE_SENSITIVE => false,
                ],
                documentation: [
                    'description' => 'Part of the stop name to search for.',
                    'schema'      => [
                        'type' => 'string',
                    ],
                ]
            ),
        );
    }
}
