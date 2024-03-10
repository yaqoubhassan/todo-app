<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @author Yakubu Alhassan <yaqoubdramani@gmail.com>
 */
class TodoTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function authenticateUser()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    protected function getHeaders($token)
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    protected function fakeExternalApi($url, $responseBody, $statusCode)
    {
        Http::fake([$url => Http::response($responseBody, $statusCode)]);
    }

    protected function assertJsonResponse($response, $expectedStatus, $expectedData)
    {
        $response->assertStatus($expectedStatus);
        $response->assertJson($expectedData);
    }

    /**
     * @test
     */
    public function testListAllTodoItems()
    {
        $auth = $this->authenticateUser();
        $this->fakeExternalApi(config('json_faker.base_url') . 'todos', [
            [
                "id" => 1,
                "userId" => 1,
                "title" => "delectus aut autem",
                "completed" => false
            ],
            [
                "id" => 2,
                "userId" => 1,
                "title" => "quis ut nam facilis et officia qui",
                "completed" => false
            ]
        ], 200);

        $response = $this->withHeaders($this->getHeaders($auth['token']))->json('GET', route('todos.index'));
        $this->assertJsonResponse($response, 200, ['data' => [['id' => 1, 'userId' => 1, 'title' => 'delectus aut autem', 'completed' => false], ['id' => 2, 'userId' => 1, 'title' => 'quis ut nam facilis et officia qui', 'completed' => false]]]);
    }

    /**
     * @test
     */
    public function testCreateTodoItem()
    {
        $auth = $this->authenticateUser();
        $this->fakeExternalApi(config('json_faker.base_url') . 'todos', ['id' => 1, 'title' => 'Test Todo', 'userId' => $auth['user']->id, 'completed' => false], 201);

        $response = $this->withHeaders($this->getHeaders($auth['token']))->json('POST', route('todos.store'), ['title' => 'Test Todo', 'completed' => true]);
        $this->assertJsonResponse($response, 200, ['data' => ['id' => 1, 'title' => 'Test Todo', 'completed' => false]]);
    }

    /**
     * @test
     */
    public function testFetchSingleTodoItem()
    {
        $auth = $this->authenticateUser();
        $this->fakeExternalApi(config('json_faker.base_url') . 'todos/1', ['id' => 1, 'title' => 'Fake Todo', 'completed' => false, 'userId' => $auth['user']->id], 200);

        $response = $this->withHeaders($this->getHeaders($auth['token']))->json('GET', route('todos.show', ['todo' => 1]));
        $this->assertJsonResponse($response, 200, ['data' => ['id' => 1, 'title' => 'Fake Todo', 'completed' => false, 'userId' => $auth['user']->id]]);
    }

    /**
     * @test
     */
    public function testUpdateTodoItem()
    {
        $auth = $this->authenticateUser();
        $this->fakeExternalApi(config('json_faker.base_url') . 'todos/1', ['id' => 1, 'title' => 'Updated Todo', 'completed' => true], 200);

        $response = $this->withHeaders($this->getHeaders($auth['token']))->json('PUT', route('todos.update', ['todo' => 1]), ['title' => 'Updated Todo', 'completed' => true]);
        $this->assertJsonResponse($response, 200, ['data' => ['id' => 1, 'title' => 'Updated Todo', 'completed' => true]]);
    }

    /**
     * @test
     */
    public function testDeleteTodoItem()
    {
        $auth = $this->authenticateUser();
        $this->fakeExternalApi(config('json_faker.base_url') . 'todos/1', [], 200);

        $response = $this->withHeaders($this->getHeaders($auth['token']))->json('DELETE', route('todos.destroy', ['todo' => 1]));
        $this->assertJsonResponse($response, 200, ['message' => 'Data deleted successfully']);
        $this->assertNull(Cache::get('todo_data_1'));
    }
}
