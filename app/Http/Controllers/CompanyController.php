<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CompanyController extends Controller
{
    /**
     * Get List Company
     * @OA\Get (
     *     path="/api/companies",
     *     tags={"Company"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="companies",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="parent_company_id",
     *                         type="number",
     *                         example=2
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Russel, Daniel and Paucek"
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
        $companies = Company::paginate($perPage);

        $data = [
            'data' => CompanyResource::collection($companies),
            'pagination' => [
                'total' => $companies->total(),
                'per_page' => $companies->perPage(),
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
            ],
        ];

        return response()->json($data);
    }

    /**
     * Get Detail Company
     * @OA\Get (
     *     path="/api/companies/{id}",
     *     tags={"Company"},
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
     *              @OA\Property(property="parent_company_id", type="number", example=5),
     *              @OA\Property(property="name", type="string", example="Russel, Daniel and Paucek"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *         )
     *     )
     * )
     */
    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company);
    }

    /**
     * Create Company
     * @OA\Post (
     *     path="/api/companies",
     *     tags={"Company"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="parent_company_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "parent_company_id":5,
     *                     "name":"Russel, Daniel and Paucek"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="parent_company_id", type="number", example=5),
     *              @OA\Property(property="name", type="string", example="Russel, Daniel and Paucek"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
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
                'parent_company_id' => 'required|integer',
                'name' => 'required|string|max:255',
            ]);

            $company = Company::create($validatedData);
            return new CompanyResource($company);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the company.'], 422);
        }
    }

    /**
     * Update Company
     * @OA\Put (
     *     path="/api/company/{id}",
     *     tags={"Company"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="parent_company_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "parent_company_id":5,
     *                     "name":"Russel, Daniel and Paucekt"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="parent_company_id", type="number", example=5),
     *              @OA\Property(property="name", type="string", example="Russel, Daniel and Paucek"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */
    public function update(Request $request, Company $company)
    {
        try {
            $validatedData = $request->validate([
                'parent_company_id' => 'required|integer|exists:companies,id',
                'name' => 'required|string|max:255',
            ]);

            $company->update($validatedData);
            return new CompanyResource($company);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the company.'], 422);
        }
    }

    /**
     * Delete Company
     * @OA\Delete (
     *     path="/api/companies/{id}",
     *     tags={"Company"},
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
     *             @OA\Property(property="msg", type="string", example="Company deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        if ($company->delete()) {
            return response()->json(['message' => 'Company deleted successfully'], 200);
        }
    }
}
