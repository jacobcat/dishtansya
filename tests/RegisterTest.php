<?php


use App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;       // use database transactions so inserts are not committed to the database

    /**
     * Positive test: Successful registration
     */
    public function testSuccessfulRegistration()
    {
        // first, ensure that the user name does not exist
        $this->notSeeInDatabase('users', ['email' => 'backend@multisyscorp.com']);

        // then, call the Register API
        // and check the following:
        // - Status code is 201
        // - Status message is "User successfully registered"
        // - The correct user and password is created in the database
        $this->json('POST', '/register', ['email' => 'backend@multisyscorp.com', 'password' => 'test123'])
            ->seeStatusCode(201)
            ->seeJson(['message' => "User successfully registered"])
            ->seeInDatabase('users', ['email' => 'backend@multisyscorp.com']);
    }

    /**
     * Negative test: Unsuccessful registration due to e-mail is already taken
     */
    public function testRegisterExistingEmail()
    {
        // Create a test user based on the User Factory
        $user = User::factory()->create(['password' => 'test123']);

        // then call the Register API using the same existing user
        // and check the following:
        // - Status code is 400
        // - Status message is "Email already taken"
        $this->json('POST', '/register', ['email' => $user->email, 'password' => 'test123'])
            ->seeStatusCode(400)
            ->seeJson(['message' => "Email already taken"]);
    }
}
