# Fryzjer (Hairdresser Reservation System)

A complete visit booking system with a **PHP** backend API, an **Angular** frontend, and a **MySQL** database. The entire setup is containerized using **Docker** for easy, cross-platform deployment.

---

## Installation & Setup

### Docker (Recommended for all platforms)
Docker ensures the application runs identically on Windows, Linux, and macOS without needing local installations of PHP, Apache, Node.js, Angular CLI, or MySQL.

#### 1. Prerequisites
* **Windows**: Install [Docker Desktop](https://www.docker.com/products/docker-desktop/) and [Git](https://git-scm.com/), and ensure Docker Desktop is running.
* **Linux / macOS**: Install `docker` and `docker-compose`, and ensure the service is running.

#### 2. Get the Code
Clone this backend repository (which contains the orchestrating Docker Compose configuration):
```bash
git clone https://github.com/60rtoip/Fryzjer_backend.git
cd Fryzjer_backend
```

#### 3. Environment & SMTP Configuration
Open `config.php` and adjust configuration settings if needed.

* **Database Connection**: By default, the connection is preconfigured to link seamlessly to the containerized database:
  ```php
  $host     = "db";
  $dbname   = "fryzjer";
  $username = "fryzjer";
  $password = "fryzjer_password";
  ```
* **Application URL**: Used for email verification and password reset links. In development, keep this as:
  ```php
  $app_url = "http://localhost";
  ```
* **SMTP Settings**: To enable automatic registration verification and password reset emails, fill in your SMTP details:
  ```php
  $smtp_host = "smtp.gmail.com";
  $smtp_port = 587;
  $smtp_user = "your_email@gmail.com";
  $smtp_pass = "your_app_password";
  ```

#### 4. Launching the Project
Run the following commands inside the root of this repository to build and start the database, backend, and frontend containers.

* **Linux / macOS / Git Bash**:
  ```bash
  docker compose up --build -d
  ```

* **Windows (PowerShell)**:
  *(Note: Bypasses Docker BuildKit path evaluation quirks for remote Git contexts on Windows)*
  ```powershell
  $env:DOCKER_BUILDKIT=0; $env:COMPOSE_DOCKER_CLI_BUILD=0; docker compose up --build -d
  ```

* **Windows (CMD)**:
  ```cmd
  set DOCKER_BUILDKIT=0 && set COMPOSE_DOCKER_CLI_BUILD=0 && docker compose up --build -d
  ```

Once the build finishes, the application will be accessible at:
* **Frontend UI**: [http://localhost](http://localhost)
* **Backend API**: [http://localhost/api/](http://localhost/api/)
* **Database**: Automated schema initialization runs on the first start using the included `if0_39855735_fryzjer.sql` dump.

---

## Services & Architecture

* **Database (`db`)**: Running MySQL 8.0, initialized with the database structure.
* **Backend (`backend`)**: Running PHP 8.2 + Apache, serving API requests.
* **Frontend (`frontend`)**: Running Angular, built and served via Nginx. It is pulled dynamically from the remote frontend repository.

To view container status or logs:
```bash
docker compose ps
docker compose logs -f
```

To stop the containers:
```bash
docker compose down
```
