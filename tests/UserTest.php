<?php

namespace VdPoel\Concur\Test;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;

class UserTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->concur->authentication->login();
    }

    /**
     * @param array $users
     * @return array
     */
    protected function filterUsers (array $users): array
    {
        return array_filter($users, function ($item) {
            return preg_match('|^test\..*@example\.com$|', data_get($item, 'LoginID')) === 1;
        });
    }

    protected function toggleActivation (bool $active = true)
    {
        $this->markTestSkipped('');

        $data = $this->concur->user->get([
            'active' => $active
        ]);

        $original = data_get($data, 'Items', []);

        $testAccounts = $this->filterUsers($original);

        $this->assertLessThanOrEqual(count($original), count($testAccounts));

        $this->concur->user->bulkUpdateActivation($testAccounts, 'N');

        $data = $this->concur->user->get([
            'active' => !$active
        ]);

        $latest = data_get($data, 'Items', []);

        $updatedAccounts = $this->filterUsers($latest);

        $this->assertEquals(count($testAccounts), count($updatedAccounts));
    }

    /**
     * @test
     * @group user
     */
    public function it_retrieves_a_list_of_users(): void
    {
        $data = $this->concur->user->get([
            'limit' => 5
        ]);


        $items = data_get($data, 'Items', []);

        $this->assertNotEmpty($items);
        $this->assertCount(5, $items);
    }

    /** @test */
    public function it_finds_a_user_by_email_address(): void
    {
        $email = 'test.772b12698f599527640af196c55a1b39@example.com';

        $data = $this->concur->user->get(compact('email'));

        $items = data_get($data, 'Items', []);

        $this->assertNotEmpty($items);
        $this->assertCount(1, $items);
        $this->assertSame($email, $items[0]['LoginID']);
    }

    /** @test */
    public function it_finds_all_users_matching_last_name(): void
    {
        $data = $this->concur->user->get(['last_name' => 'User']);

        $items = data_get($data, 'Items', []);

        $this->assertNotEmpty($items);
        $this->assertCount(count($items), array_filter($items, function ($item) {
            return data_get($item, 'LastName') === 'User';
        }));
    }

    /** @test */
    public function it_bulk_deactivates_user_accounts(): void
    {
        $this->toggleActivation();
    }

    /** @test */
    public function it_bulk_reactivates_user_accounts(): void
    {
        $this->toggleActivation(false);
    }
}
