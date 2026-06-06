Fryzjer Backend

Backend systemu rezerwacji wizyt (PHP + MySQL).

WYMAGANIA

- PHP
- MySQL
- Przeglądarka

-------------------------------------

URUCHOMIENIE

1. Skopiuj projekt

Skopiuj folder Fryzjer_backend w dowolne miejsce na komputerze.


2. Baza danych

Utwórz bazę danych:

CREATE DATABASE fryzjer;

Następnie zaimportuj plik database.sql (jeśli jest dołączony).


3. Konfiguracja

Otwórz plik config.php i ustaw dane dostępowe do bazy:

$host = "localhost";
$dbname = "fryzjer";
$username = "root";
$password = "";


4. Mail (opcjonalne)

W plikach:

auth/register.php  
password/password_reset_request.php  

ustaw dane SMTP:

$mail->Username = "twoj_email@gmail.com";  
$mail->Password = "APP_PASSWORD";  


5. Uruchomienie serwera PHP

Wejdź w terminal / cmd do folderu projektu:

cd Fryzjer_backend

Uruchom serwer:

php -S localhost:8000


6. Otwórz w przeglądarce

http://localhost:8000/index.php


-------------------------------------
