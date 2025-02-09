Feature: Comment Service

    Scenario: Saving a comment successfully
        Given a new comment with content "Test comment"
        When I save the comment
        Then the comment should be saved successfully

    Scenario: Saving a comment fails due to validation errors
        Given a new comment with invalid content
        When I save the comment
        Then I should receive a validation error

    Scenario: Deleting a comment successfully
        Given an existing comment
        When I delete the comment
        Then the comment should be deleted successfully

    Scenario: Fetching a comment by ID
        Given an existing comment with ID 1
        When I fetch the comment by ID
        Then I should receive the comment details

    Scenario: Fetching comments by post
        Given an existing post with comments
        When I fetch comments by post ID
        Then I should receive the list of comments
