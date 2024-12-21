<?php

namespace App\Http\Controllers\User;

use App\Domain\File\Csv\CsvDataValidator;
use App\Domain\File\UserSpreadsheet\UserSpreadsheet;
use App\Domain\User\User;
use App\Exceptions\InvalidUserObjectException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserSpreadsheetException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\DateTime;
use App\Infra\Db\UserDb;
use App\Infra\File\Csv\Csv;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/user/spreadsheet",
     *     summary="Importação de usuário através de arquivo CSV",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="text/csv",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     default="Campo do tipo arquivo, com o nome 'file', que recebe o arquivo CSV com os dados dos usuários"
     *                 ),
     *           ),
     *        )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Created",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="created_users",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="date_time",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "created_users": 2,
     *                        "date_time": "2023-12-28 04:10:10"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Bad Request",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="bad_request",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "bad_request": "Spreadsheet error: line 2 | CPF already created",
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function spreadsheet(Request $request): JsonResponse
    {
        try {

            $this->validate($request, ['file' => 'required']);

            $uploadedFile = $request->file('file');

            $csv = new Csv();

            $csv->setItemsCSV($uploadedFile);

            $userSpreadsheet = new UserSpreadsheet();

            $userSpreadsheet->setItemsUserSpreadSheet($csv);

            $usersFromFile = $userSpreadsheet->buildUsersFromContent();

            $userInstance = new User(new UserDb());

            $userInstance->createFromBatch($usersFromFile);

            return $this->buildCreatedResponse([
                'created_users' => count($usersFromFile),
                'date_time' => DateTime::formatDateTime('now')
            ]);

        } catch (InvalidUserObjectException | ValidationException | UserSpreadsheetException $e) {

            return $this->buildBadRequestResponse($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Listagem de todos os usuários cadastrados",
     *     tags={"User"},
     *     @OA\Response(
     *          response="201",
     *          description="Created",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="id",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="cpf",
     *                         type="string",
     *                     ),
     *                     example={
     *                        {
     *                          "id": "a38a7ac8-9295-33c2-8c0b-5767c1449bc3",
     *                          "name": "Ronaldo de Assis Moreira",
     *                          "email": "ro.naldinho@email.com",
     *                          "cpf": "2023-12-28 04:10:10"
     *                        }
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function all(): JsonResponse
    {
        try {
            $userInstance = new User(new UserDb());

            $users = $userInstance->findAll();

            $response = [];
            foreach($users as $user) {
                $response[] = [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'cpf' => $user->getCpf()
                ];
            }

            return $this->buildSuccessResponse($response);

        } catch (Exception $e) {

            throw $e;
        }
    }

    /**
     * @OA\Get(
     *     path="/user/spreadsheet",
     *     summary="Geração dos dados dos usuários registrados em formato CSV",
     *     tags={"User"},
     *     @OA\Response(
     *          response="200",
     *          description="Created",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="csv",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "csv": "name,cpf,email\nRonaldo de Assis Moreira,16742019077,drnaoseioque@email.com\n"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function createSpreadsheet(): JsonResponse
    {
        $userInstance = new User(new UserDb());
        $allUsers = $userInstance->validateAndGetUsers();

        $csv = new Csv();
        $csv->setDataValidator(new CsvDataValidator());

        $userSpreadsheet = new UserSpreadsheet();
        $userSpreadsheet->setUsers($allUsers)
            ->setCsv($csv);

        $response = [
            'csv' => $csv->generateCsvContent($allUsers)
        ];

        return $this->buildSuccessResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/user/{uuid}",
     *     summary="Busca um usuário pelo UUID",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="uuid",
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
     *                         property="id",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="cpf",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "id": "a38a7ac8-9295-33c2-8c0b-5767c1449bc3",
     *                        "name": "Ronaldo de Assis Moreira",
     *                        "email": "ronaldinho@email.com",
     *                        "cpf": "94965217039"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="The user does not exist",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="error",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "bad_request": "The user does not exist"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function findById(string $uuid): JsonResponse
    {
        try {
            $userInstance = new User(new UserDb());
            $user = $userInstance->findById($uuid);

            return $this->buildSuccessResponse($user);

        } catch (UserNotFoundException|Exception $e) {

            return $this->buildBadRequestResponse($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/user/{uuid}",
     *     summary="Deleta um usuário pelo UUID",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="uuid",
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
     *                         property="message",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "message": "User deleted successfully"
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
     *                        "error": "The user does not exist"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function deleteById(string $uuid): JsonResponse
    {
        try {
            $userInstance = new User(new UserDb());

            $user = $userInstance->deleteById($uuid);

            return $this->buildSuccessResponse($user);

        } catch (Exception $e) {

            return $this->buildBadRequestResponse($e->getMessage());
        }
    }
}
