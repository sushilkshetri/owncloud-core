Feature: login

	@CreateRegularUsers
	Scenario: simple user login
		Given I am on login page
		When I login with an existing user and a correct password
		Then I should be redirected to a page with the title "Files - ownCloud"
		
	Scenario: admin login
		Given I am on login page
		When I login with username "admin" and password "admin"
		Then I should be redirected to a page with the title "Files - ownCloud"