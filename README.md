## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

- Check the [API documentation on Postman](https://www.postman.com/mission-candidate-75963434/workspace/my-workspace/collection/41861286-32264319-b417-4cbc-9618-51c79f51638e?action=share&creator=41861286) for more details.
