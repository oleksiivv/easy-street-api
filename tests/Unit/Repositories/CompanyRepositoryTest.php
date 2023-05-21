<?php

namespace Tests\Unit\Repositories;

use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testGet()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Create a new company
        $company = Company::factory()->create();

        // Retrieve the company by ID
        $retrievedCompany = $repository->get($company->id);

        // Assert that the retrieved company matches the created company
        $this->assertInstanceOf(Company::class, $retrievedCompany);
        $this->assertEquals($company->id, $retrievedCompany->id);
        $this->assertEquals($company->games->count(), $retrievedCompany->games->count());
        $this->assertEquals($company->games->first()->gamePage, $retrievedCompany->games->first()->gamePage);
    }

    public function testGetThrowsModelNotFoundException()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Try to retrieve a non-existent company
        $nonExistentCompanyId = 999;
        $this->expectException(ModelNotFoundException::class);

        // Call the get method
        $repository->get($nonExistentCompanyId);
    }

    public function testList()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Create some companies
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $company3 = Company::factory()->create();

        // Retrieve the companies list
        $companies = $repository->list();

        // Assert that the companies list is a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $companies);

        // Assert that the companies list contains the created companies
        $this->assertTrue($companies->contains('id', $company1->id));
        $this->assertTrue($companies->contains('id', $company2->id));
        $this->assertTrue($companies->contains('id', $company3->id));
    }

    public function testCompaniesByTeamMemberUserId()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Create a user
        $user = User::factory()->create();

        // Create some companies
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $company3 = Company::factory()->create();

        // Add the user as a team member to the companies
        $company1->team_members = [
            [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'id' => $user->id,
            ],
        ];
        $company1->save();

        $company2->team_members = [
            [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'id' => $user->id,
            ],
        ];
        $company2->save();

        // Retrieve the companies for the user
        $userCompanies = $repository->companiesByTeamMemberUserId($user->id);

        // Assert that the user companies list is a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $userCompanies);

        // Assert that the user companies list contains the expected companies
        $this->assertTrue($userCompanies->contains('id', $company1->id));
        $this->assertTrue($userCompanies->contains('id', $company2->id));
        $this->assertFalse($userCompanies->contains('id', $company3->id));
    }

    public function testCreate()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Create company data
        $data = [
            'name' => 'Test Company',
            'description' => 'A test company',
        ];

        // Create a new company
        $createdCompany = $repository->create($data);

        // Assert that the created company matches the provided data
        $this->assertInstanceOf(Company::class, $createdCompany);
        $this->assertEquals($data['name'], $createdCompany->name);
        $this->assertEquals($data['description'], $createdCompany->description);
    }

    public function testUpdate()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Create a new company
        $company = Company::factory()->create();

        // Update the company data
        $updatedData = [
            'name' => 'Updated Company Name',
            'description' => 'Updated company description',
        ];
        $updatedCompany = $repository->update($company->id, $updatedData);

        // Assert that the updated company matches the provided data
        $this->assertInstanceOf(Company::class, $updatedCompany);
        $this->assertEquals($company->id, $updatedCompany->id);
        $this->assertEquals($updatedData['name'], $updatedCompany->name);
        $this->assertEquals($updatedData['description'], $updatedCompany->description);
    }

    public function testUpdateThrowsModelNotFoundException()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CompanyRepository();

        // Try to update a non-existent company
        $nonExistentCompanyId = 999;
        $this->expectException(ModelNotFoundException::class);

        // Call the update method
        $repository->update($nonExistentCompanyId, []);
    }
}
