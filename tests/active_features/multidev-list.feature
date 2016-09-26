Feature: Listing a site's environments
  In order to administer my site
  As a user
  I need to be able to list all of its environments

  Background: I am authenticated and have a site named [[test_site_name]]
    Given I am authenticated
    And a site named "[[test_site_name]]"

  @vcr site_environments
  Scenario: Listing all multidev environments belonging to a site
    When I run "terminus multidev:list [[test_site_name]] --format=json"
    Then I should get: ""
