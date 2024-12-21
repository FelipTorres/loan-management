<?php

namespace App\Http\Controllers\Eligibility;

use App\Domain\User\User as UserDomain;
use App\Exceptions\UserNotFoundException;
use App\Infra\Db\EmployeeDB;
use App\Http\Controllers\Controller;
use App\Infra\Db\UserDb;
use App\Domain\Employee\Employee as EmployeeDomain;
use Exception;
use Illuminate\Http\JsonResponse;

class EligibilityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/eligibility/{userUuid}",
     *     summary="Verifica a elegibilidade de um usuário para crédito consignado",
     *     tags={"Eligibility"},
     *     @OA\Parameter(
     *         name="userUuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="OK",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="eligible",
     *                         type="boolean",
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "eligible": true,
     *                        "message": "Employee is eligible for consigned credit."
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="error",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "error": "User not found"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function check($userUuid): JsonResponse
    {
        $userInstance = new UserDomain(new UserDb());

        $user = $userInstance->findById($userUuid);

        $employeeInstance = new EmployeeDomain(new EmployeeDB());

        $employee = $employeeInstance->findByUserId($user['id']);

        $checkConsignedCredit = $employee->checkIfEmployeeGetConsignedCredit();

        return $this->buildSuccessResponse($checkConsignedCredit);
    }
}
