<?php

namespace App\Domain\User;

use App\Domain\Uuid\UuidGeneratorInterface;
use App\Exceptions\DuplicatedDataException;
use App\Exceptions\InvalidUserObjectException;
use App\Infra\Uuid\UuidGenerator;
use Exception;

class User
{
    private string $id;
    private string $name;
    private string $email;
    private string $cpf;
    private string $dateCreation;
    private string $dateEdition;
    private UserDataValidatorInterface $dataValidator;
    private UuidGeneratorInterface $uuidGenerator;
    private UserPersistenceInterface $persistence;

    public function __construct(UserPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function setDataValidator(UserDataValidatorInterface $dataValidator): User
    {
        $this->dataValidator = $dataValidator;

        return $this;
    }

    public function getDataValidator(): UserDataValidatorInterface
    {
        return $this->dataValidator;
    }

    public function setUuidGenerator(UuidGeneratorInterface $uuidGenerator): User
    {
        $this->uuidGenerator = $uuidGenerator;

        return $this;
    }

    public function setId(string $id): User
    {
        $this->getDataValidator()->validateId($id);

        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): User
    {
        $this->getDataValidator()->validateName($name);

        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEmail(string $email): User
    {
        $this->getDataValidator()->validateEmail($email);

        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setCpf(string $cpf): User
    {
        $this->getDataValidator()->validateCpf($cpf);

        $this->cpf = $cpf;

        return $this;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function setDateCreation(string $dateCreation): User
    {
        $this->getDataValidator()->validateDateCreation($dateCreation);

        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function setDateEdition(string $dateEdition): User
    {
        $this->getDataValidator()->validateDateEdition($dateEdition);

        $this->dateEdition = $dateEdition;

        return $this;
    }

    public function getDateEdition(): string
    {
        return $this->dateEdition;
    }

    public function generateId(): User
    {
        $this->id = $this->uuidGenerator->generate();

        return $this;
    }

    /**
     * @throws InvalidUserObjectException
     */
    public function createFromBatch(array $users): void
    {
        $this->checkUsers($users);

        foreach ($users as $user) {

            $this->persistence->create($user);
        }
    }

    /**
     * @throws InvalidUserObjectException
     */
    private function checkUsers(array $users): void
    {
        foreach($users as $user) {

            if ($user::class !== $this::class) {

                throw new InvalidUserObjectException('The users array must have only users');
            }
        }
    }

    /**
     * @throws DuplicatedDataException
     */
    public function verifyAlreadyCreatedCpf(): void
    {
        if ($this->persistence->isCpfAlreadyCreated($this)) {

            throw new DuplicatedDataException('CPF already created');
        }
    }

    /**
     * @throws DuplicatedDataException
     */
    public function verifyAlreadyCreatedEmail(): void
    {
        if ($this->persistence->isEmailAlreadyCreated($this)) {

            throw new DuplicatedDataException('Email already created');
        }
    }

    public function findAll(): array
    {
        return $this->persistence->findAll($this);
    }

    public function setItemsUser(UuidGenerator $uuidGenerator, string $name, string $cpf, string $email): void
    {
        $this->setDataValidator(new UserDataValidator())
            ->setUuidGenerator($uuidGenerator)
            ->setName(utf8_decode($name))
            ->setCpf($cpf)
            ->setEmail($email)
            ->generateId()
            ->setDateCreation(date('Y-m-d H:i:s'));
    }

    public function validateAndGetUsers(): array
    {
        $this->setDataValidator(new UserDataValidator());

        return $this->findAll();
    }

    /**
     * @throws Exception
     */
    public function findById(string $uuid): array
    {
        $this->setDataValidator(new UserDataValidator());

        $dataValidator = $this->getDataValidator();

        $dataValidator->validateUuid($uuid);

        $user = $this->persistence->findById($uuid);

        $dataValidator->validateUserExists($user);

        return $this->buildUserResponse($user);
    }

    public function deleteById(string $uuid): array
    {
        $this->setDataValidator(new UserDataValidator());

        $dataValidator = $this->getDataValidator();

        $dataValidator->validateUuid($uuid);

        $user = $this->persistence->findById($uuid);

        $dataValidator->validateUserExists($user);

        $this->persistence->deleteById($user);

        return $this->buildUserResponse($user);
    }

    /**
     * @throws Exception
     */
    public function updateById(string $uuid, array $data): array
    {
        $this->setDataValidator(new UserDataValidator());

        $dataValidator = $this->getDataValidator();

        $dataValidator->validateUuid($uuid);

        $user = $this->persistence->findById($uuid);

        $dataValidator->validateUserExists($user);

        $dataValidated = $this->validateData($data);

        $this->persistence->updateById($user, $dataValidated);

        $user = $this->persistence->findById($uuid);

        return $this->buildUserResponse($user);
    }

    /**
     * @throws Exception
     */
    public function validateData(array $data): array
    {
        $newItems  = [];

        $dataValidator = $this->getDataValidator();

        $dataValidator->validateRequestToUpdate($data);

        if (isset($data['name'])) {

            $dataValidator->validateName($data['name']);

            $newItems['name'] = $data['name'];
        }

        if (isset($data['email'])) {

            $dataValidator->validateEmail($data['email']);

            $newItems['email'] = $data['email'];
        }

        if (isset($data['cpf'])) {

            $dataValidator->validateCpf($data['cpf']);

            $newItems['cpf'] = $data['cpf'];
        }

        return $newItems;
    }

    public function buildUserResponse(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'cpf' => $user->getCpf(),
            'email' => $user->getEmail(),
            'dateCreation' => $user->getDateCreation(),
        ];
    }
}
