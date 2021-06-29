<?php


use App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;       // use database transactions so inserts are not committed to the database

    /**
     * Positive test: User has logged in successfully
     */
    public function testSuccessfulLogin()
    {
        // create a user
        $user = User::factory()->create(['password' => app('hash')->make('test123')]);

        // then call the Login API using the same user
        // and check the following:
        // - Status code is 201
        // - Response body contains "access_token"
        $this->json('POST', '/login', ['email' => $user->email, 'password' => 'test123'])
            ->seeStatusCode(201);

        $res_array = (array)json_decode($this->response->content());

        $this->assertArrayHasKey('access_token', $res_array);
    }

    /**
     * Negative test: User has failed login
     */
    public function testFailedLogin() {
        // create a user
        $user = User::factory()->create(['password' => app('hash')->make('test123')]);

        // then call the Login API using the same user but different password
        // and check the following:
        // - Status code is 401
        // - Status message is "Invalid credentials"
        $this->json('POST', '/login', ['email' => $user->email, 'password' => 'XXX'])
            ->seeStatusCode(401)
            ->seeJson(['message' => 'Invalid credentials']);
    }
}
