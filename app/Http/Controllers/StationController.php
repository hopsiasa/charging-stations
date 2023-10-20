<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class StationController extends Controller
{
    /**
     * Get stations list
     * @OA\Get (
     *     path="/api/stations",
     *     tags={"Station"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="stations",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Upton Group"
     *                     ),
     *                     @OA\Property(
     *                         property="latitude",
     *                         type="decimal",
     *                         example="57.07853"
     *                     ),
     *                     @OA\Property(
     *                         property="longitude",
     *                         type="decimal",
     *                         example="172.37435"
     *                     ),
     *                     @OA\Property(
     *                         property="address",
     *                         type="text",
     *                         example="8516 Dayne Vista Suite 240 West Oran, ND 22379"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     ),
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 25);
        $stations = Station::paginate($perPage);

        $data = [
            'data' => StationResource::collection($stations),
            'pagination' => [
                'total' => $stations->total(),
                'per_page' => $stations->perPage(),
                'current_page' => $stations->currentPage(),
                'last_page' => $stations->lastPage(),
            ],
        ];

        return response()->json($data);
    }

    /**
     * Get station details
     * @OA\Get (
     *     path="/api/stations/{id}",
     *     tags={"Station"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Upton Group"),
     *              @OA\Property(property="latitude", type="number", example="22.123123"),
     *              @OA\Property(property="longitude", type="number", example="132.341233"),
     *              @OA\Property(property="address", type="number", example="8516 Dayne Vista Suite 240 West Oran, ND 22379"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *         )
     *     )
     * )
     */
    public function show(Station $station): StationResource
    {
        return new StationResource($station);
    }

    /**
     * Create station
     * @OA\Post (
     *     path="/api/stations",
     *     tags={"Station"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="latitude",
     *                          type="decimal"
     *                      ),
     *                      @OA\Property(
     *                          property="longitude",
     *                          type="decimal"
     *                      ),
     *                      @OA\Property(
     *                          property="company_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Russel, Daniel and Paucek",
     *                     "latitude": "12.981237",
     *                     "longitude": "121.981237",
     *                     "company_id": 5,
     *                     "address": "8516 Dayne Vista Suite 240 West Oran, ND 22379",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *               @OA\Property(property="name", type="string", example="Upton Group"),
     *               @OA\Property(property="latitude", type="number", example="22.123123"),
     *               @OA\Property(property="longitude", type="number", example="132.341233"),
     *               @OA\Property(property="address", type="number", example="8516 Dayne Vista Suite 240 West Oran, ND 22379"),
     *               @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *               @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="fail"),
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'company_id' => 'required|integer|exists:companies,id',
                'address' => 'required|string|max:500',
            ]);

            $station = Station::create($validatedData);
            return new StationResource($station);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the station.'], 422);
        }
    }

    /**
     * Update station
     * @OA\Put (
     *     path="/api/stations",
     *     tags={"Station"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="latitude",
     *                          type="decimal"
     *                      ),
     *                      @OA\Property(
     *                          property="longitude",
     *                          type="decimal"
     *                      ),
     *                      @OA\Property(
     *                          property="company_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Russel, Daniel and Paucek",
     *                     "latitude": "12.981237",
     *                     "longitude": "121.981237",
     *                     "company_id": 5,
     *                     "address": "8516 Dayne Vista Suite 240 West Oran, ND 22379",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *               @OA\Property(property="name", type="string", example="Upton Group"),
     *               @OA\Property(property="latitude", type="number", example="22.123123"),
     *               @OA\Property(property="longitude", type="number", example="132.341233"),
     *               @OA\Property(property="address", type="number", example="8516 Dayne Vista Suite 240 West Oran, ND 22379"),
     *               @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *               @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      ),
     * )
     */
    public function update(Request $request, Station $station)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'latitude' => 'numeric|between:-90,90',
                'longitude' => 'numeric|between:-180,180',
                'company_id' => 'integer|exists:companies,id',
                'address' => 'string|max:500',
            ]);

            $station->update($validatedData);
            return new StationResource($station);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the station.'], 422);
        }
    }

    /**
     * Delete station
     * @OA\Delete (
     *     path="/api/stations/{id}",
     *     tags={"Station"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Station deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $station = Station::find($id);

        if (!$station) {
            return response()->json(['error' => 'Station not found'], 404);
        }

        if ($station->delete()) {
            return response()->json(['message' => 'Station deleted successfully'], 200);
        }
    }

    /**
     * Get stations within radius
     * @OA\Get (
     *     path="/api/stations/search",
     *     tags={"Station"},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         description="Latitude",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example="27.90461"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lon",
     *         in="query",
     *         description="Longitude",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example="59.83534"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Search Radius (in kilometers)",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example="50.0"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="stations",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="location",
     *                         type="object",
     *                         @OA\Property(
     *                             property="latitude",
     *                             type="string",
     *                             example="27.90461"
     *                         ),
     *                         @OA\Property(
     *                             property="longitude",
     *                             type="string",
     *                             example="59.83534"
     *                         ),
     *                     ),
     *                     @OA\Property(
     *                         property="companies",
     *                         type="object",
     *                         @OA\Property(
     *                             property="5",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="company_id",
     *                                 type="number",
     *                                 example=5
     *                             ),
     *                             @OA\Property(
     *                                 property="stations_in_company",
     *                                 type="array",
     *                                 @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(
     *                                         property="id",
     *                                         type="number",
     *                                         example=79
     *                                     ),
     *                                     @OA\Property(
     *                                         property="name",
     *                                         type="string",
     *                                         example="Kohler-Buckridge"
     *                                     ),
     *                                     @OA\Property(
     *                                         property="company_id",
     *                                         type="number",
     *                                         example=5
     *                                     ),
     *                                     @OA\Property(
     *                                         property="address",
     *                                         type="string",
     *                                         example="38369 Imani Circle\nCarlosstad, UT 85281-5939"
     *                                     ),
     *                                     @OA\Property(
     *                                         property="distance",
     *                                         type="number",
     *                                         example=1378.317035354452
     *                                     ),
     *                                 )
     *                             ),
     *                         ),
     *                     ),
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $latitude = $request->input('lat');
        $longitude = $request->input('lon');
        $radius = $request->input('radius');

        $stations = Station::select(
            'id',
            'name',
            'latitude',
            'longitude',
            'company_id',
            'address'
        )
            ->selectRaw('( 6371
            * acos( cos( radians(?) )
            * cos( radians( latitude ) )
            * cos( radians( longitude ) - radians(?) )
            + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();

        // Group stations by coordinates
        $groupedStations = [];
        foreach ($stations as $station) {
            $locationKey = $station->latitude.'-'.$station->longitude;
            $companyKey = $station->company_id;

            if (!array_key_exists($locationKey, $groupedStations)) {
                $groupedStations[$locationKey] = [
                    'location' => [
                        'latitude' => $station->latitude,
                        'longitude' => $station->longitude,
                    ],
                    'companies' => [],
                ];
            }

            if (!array_key_exists($companyKey, $groupedStations[$locationKey]['companies'])) {
                $groupedStations[$locationKey]['companies'][$companyKey] = [
                    'company_id' => $station->company_id,
                    'stations_in_company' => [],
                ];
            }

            $groupedStations[$locationKey]['companies'][$companyKey]['stations_in_company'][] = [
                'id' => $station->id,
                'name' => $station->name,
                'company_id' => $station->company_id,
                'address' => $station->address,
                'distance' => $station->distance,
            ];
        }

        // Convert the grouped stations to a simple array
        $groupedStations = array_values($groupedStations);

        return response()->json(['stations' => $groupedStations]);
    }
}
