<?php

namespace Tests\Http\User;

use App\Models\User;
use Tests\TestCase;
use Faker;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserHttpTest extends TestCase
{
    use DatabaseMigrations;

    private const VALID_CPF = '48472338088';

    private Faker\Generator $faker;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker\Factory::create();

        $this->user = User::create([
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'cpf' => self::VALID_CPF
        ]);
    }

    public function testShouldCorrectlyReturnAllUsersThatAreNotDeleted(): void
    {
        $response = $this
            ->call('GET', '/users');

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'cpf' => $this->user->cpf
            ]
        ]);
    }

    public function testShouldCreateUserFromSpreadsheet(): void
    {
        $file = new \Illuminate\Http\UploadedFile(
            base_path('tests/fixtures/users.csv'),
            'users.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->call('POST', '/user/spreadsheet', [], [], ['file' => $file]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['created_users', 'date_time']);
    }

    public function testShouldReturnUserById(): void
    {
        $response = $this->call('GET', '/user/' . $this->user->uuid);

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            'id' => $this->user->uuid,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'cpf' => $this->user->cpf
        ]);
    }

    public function testShouldDeleteUserById(): void
    {
        $response = $this->call('DELETE', '/user/' . $this->user->uuid);

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson(['message' => 'User deleted successfully']);
    }

    public function testShouldUpdateUserById(): void
    {
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'cpf' => '12345678901'
        ];

        $response = $this->call('PUT', '/user/' . $this->user->uuid, $updatedData);

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            'id' => $this->user->uuid,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
            'cpf' => $updatedData['cpf']
        ]);
    }

    public function testShouldGenerateUserSpreadsheet(): void
    {
        $response = $this->call('GET', '/user/spreadsheet');

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJsonStructure(['csv']);
    }
}
