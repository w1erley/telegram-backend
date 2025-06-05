# Telegram backend

## Main Features
- Laravel 11 (Sail, PHP-FPM)
- REST API for frontend
- WebSocket via Laravel Reverb (integrated)
- Caching with Redis
- File storage in MinIO (S3 compatible)
- File uploads via tusd (webhook integration)
- Automatic .env generation via Ansible
- Feature testing with Laravel

## Setup

1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd ansible
   ```

2. **Create Jinja2 templates and variables:**
   - Copy all `.j2` templates to the `templates/` folder (see provided example files).
   - Copy `main.yaml` to the `vars/` folder (see example file).

3. **Generate .env files automatically:**
   ```bash
   ansible-playbook generate-envs.yml
   ```
4. **Move back to root folder:**
   ```bash
   cd ../
   ```
5. **Start all services (including dependencies):**
   ```bash
   docker-compose up -d --build
   ```

> **Note:** Database migrations and seeders will run automatically inside the Docker container.

## Usage

- API base URL: `http://localhost:8080/api/`
- WebSocket: `ws://localhost:6001`

## Testing

To run tests (from inside the container or using Sail):
```bash
./vendor/bin/sail artisan test
```