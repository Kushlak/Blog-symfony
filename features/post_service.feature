Feature: Post Service

    Scenario: Creating a post successfully
        Given a new post with title "Test Post" and content "This is a test post content" by user "testuser"
        When I save the post
        Then the post should be saved successfully

    Scenario: Updating a post successfully
        Given an existing post with title "Old Post" and content "Old content"
        When I update the post with title "Updated Post" and content "Updated content"
        Then the post should be updated successfully

    Scenario: Fetching a post by ID
        Given an existing post with ID 1
        When I fetch the post by ID
        Then I should receive the post details

    Scenario: Fetching all posts
        Given there are existing posts
        When I fetch all posts
        Then I should receive the list of posts

    Scenario: Deleting a post successfully
        Given an existing post
        When I delete the post
        Then the post should be deleted successfully
