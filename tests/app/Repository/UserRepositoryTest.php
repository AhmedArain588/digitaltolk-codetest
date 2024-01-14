<?php

namespace tests\app\Repositories;

use DTApi\Repository\UserRepository;
use DTApi\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use tests\TestCase;
use DTApi\Models\UsersBlacklist;

class UserRepositoryTest extends TestCase
{

    public function testCreateOrUpdate()
    {
        // Assuming you have a factory for the User model
        $user = factory(User::class)->create();

        // Create an instance of the UserRepository
        $userRepository = new UserRepository(new User);

        // Mock the request data
        $requestData = [
            'role' => 'translator', // or 'customer' based on your scenario
            'name' => 'John Doe',
            'company_id' => 1,
            'department_id' => 2,
            'email' => 'john.doe@example.com',
            'dob_or_orgid' => '1980-01-01', // assuming it's a date of birth or organization ID
            'phone' => '1234567890',
            'mobile' => '9876543210',
            'password' => 'secure_password',
            'consumer_type' => 'paid',
            'customer_type' => 'premium',
            'username' => 'johndoe123',
            'post_code' => '12345',
            'address' => '123 Main Street',
            'city' => 'Anytown',
            'town' => 'Sometown',
            'country' => 'CountryX',
            'additional_info' => 'Some additional info',
            'cost_place' => 'Some cost place',
            'fee' => '100.00',
            'time_to_charge' => '2 hours',
            'time_to_pay' => '30 days',
            'charge_ob' => 'Some charge ob',
            'customer_id' => 'C12345',
            'charge_km' => '5.00',
            'maximum_km' => '100',
            'new_towns' => 'NewTownX',
            'user_towns_projects' => [1, 2, 3], // assuming town IDs
            'status' => '1',
            'translator_ex' => [4, 5], // assuming translator IDs
            'translator_type' => 'Some translator type',
            'worked_for' => 'yes',
            'organization_number' => 'Org123',
            'gender' => 'Male',
            'translator_level' => 'Advanced',
            'user_language' => [1, 2, 3], // assuming language IDs
        ];

        // Call the createOrUpdate method
        $result = $userRepository->createOrUpdate($user->id, $requestData);

        // Assert that the result is not false (indicating success)
        $this->assertNotFalse($result);

        // Verify the user's updated attributes
        $this->assertEquals($requestData['role'], $result->user_type);
        $this->assertEquals($requestData['name'], $result->name);
        $this->assertEquals($requestData['company_id'], $result->company_id);
        $this->assertEquals($requestData['department_id'], $result->department_id);
        $this->assertEquals($requestData['email'], $result->email);
        $this->assertEquals($requestData['dob_or_orgid'], $result->dob_or_orgid);
        $this->assertEquals($requestData['phone'], $result->phone);
        $this->assertEquals($requestData['mobile'], $result->mobile);
        $this->assertEquals($requestData['password'], $result->password); // Add this line if you want to assert the password
        $this->assertEquals($requestData['consumer_type'], $result->userMeta->consumer_type);
        $this->assertEquals($requestData['customer_type'], $result->userMeta->customer_type);
        $this->assertEquals($requestData['username'], $result->userMeta->username);
        $this->assertEquals($requestData['post_code'], $result->userMeta->post_code);
        $this->assertEquals($requestData['address'], $result->userMeta->address);
        $this->assertEquals($requestData['city'], $result->userMeta->city);
        $this->assertEquals($requestData['town'], $result->userMeta->town);
        $this->assertEquals($requestData['country'], $result->userMeta->country);
        $this->assertEquals($requestData['additional_info'], $result->userMeta->additional_info);
        $this->assertEquals($requestData['cost_place'], $result->userMeta->cost_place);
        $this->assertEquals($requestData['fee'], $result->userMeta->fee);
        $this->assertEquals($requestData['time_to_charge'], $result->userMeta->time_to_charge);
        $this->assertEquals($requestData['time_to_pay'], $result->userMeta->time_to_pay);
        $this->assertEquals($requestData['charge_ob'], $result->userMeta->charge_ob);
        $this->assertEquals($requestData['customer_id'], $result->userMeta->customer_id);
        $this->assertEquals($requestData['charge_km'], $result->userMeta->charge_km);
        $this->assertEquals($requestData['maximum_km'], $result->userMeta->maximum_km);
        $this->assertEquals($requestData['new_towns'], $result->userTowns->townname);
        $this->assertEquals($requestData['user_towns_projects'], $result->userTowns->pluck('town_id')->toArray());
        $this->assertEquals($requestData['status'], $result->status);
        $this->assertEquals($requestData['translator_ex'], UsersBlacklist::where('user_id', $result->id)->pluck('translator_id')->toArray());
        $this->assertEquals($requestData['translator_type'], $result->userMeta->translator_type);
        $this->assertEquals($requestData['worked_for'], $result->userMeta->worked_for);
        $this->assertEquals($requestData['organization_number'], $result->userMeta->organization_number);
        $this->assertEquals($requestData['gender'], $result->userMeta->gender);
        $this->assertEquals($requestData['translator_level'], $result->userMeta->translator_level);
        $this->assertEquals($requestData['user_language'], $result->userLanguages->pluck('lang_id')->toArray());
        

        // Clean up the test data
        $user->delete();
    }
}

// php artisan test
// with the upper command test will run
