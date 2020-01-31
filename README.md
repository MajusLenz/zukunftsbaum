# Zukunftsbaum

HSD-Projekt.

Falls ihr gerade diese README lest, wisst ihr wahrscheinlich bereits worum es geht. Wenn nicht: Wiedersehn.

* ACHTUNG: Niemals Passwörter, z.B. in configs commiten!!!


# Web-Server Installationsanleitung:

- Optional: Installiert euch für eure IDE am besten ein Symfony Plugin. Ich nutze PHP Storm von JetBrains, da geht es über File -> Settings -> Plugins -> "Symfony Support".

- Installiert euch die PHP Version 7.3.7 auf euren Rechner. Am leichtesten geht dies m.M.n über den "Windows Web Platform Installer". Für Windows 8 könnt ihr die Aneitung nutzen die ich auch benutzt habe: https://support.microsoft.com/de-de/help/2819022/how-to-install-and-configure-php-on-iis-8-in-windows-8. Für ein anderes Betriebssystem müsst ihr selber googlen. (Der Link klappt wahrscheinlich auch für Windows 10. Einfach mal durchlesen und ausprobieren)

- Installiert euch die neuste Composer Version auf euren Rechner.

- Installiert euch die neuste GIT Version auf euren Rechner.

- Installiert euch die neuste Symfony Version (5.0.1) auf euren Rechner. (https://symfony.com/download)

- Klont dieses Git-Repository (https://github.com/MajusLenz/zukunftsbaum.git) auf euren Rechner in einen Pfad eurer Wahl. Z.B. "C:\uni\projekte\zukunftsbaum\code"

- Navigiert im Terminal in besagten Pfad. (in windows über den Befehl: cd "C:\uni\projekte\zukunftsbaum\code")

- Führt dort den Befehl "composer install" zum installieren der Dependencies aus.

- Führt dort den Befehl "symfony local:php:list" aus, um Symfonys bekannte PHP-Versionen durch die neu installierte PHP-Version zu erweitern.

- Führt dann den Befehl "symfony server:start --no-tls" zum starten des lokalen Webservers aus. Es sollte die URL "127.0.0.1:8000" o.ä auf der Konsole ausgegeben werden.

- Öffnet die URL in eurem Browser mit dem Zusatz "/testweb". (Also: "127.0.0.1:8000/testweb")
Wenn alles funktioniert hat, sollte die Nachricht "Webserver funktioniert!" angezeigt werden.
Beenden könnt ihr den Serverprozess mit Strg + C.


# Datenbank-Server Installationsanleitung

- Installiert euch ein Tool eures Vertrauens um eine MySQL-Datenbank lokal zu emulieren. Ich nutze dazu XAMPP.

- Startet in dem Tool die Datenbank. Diese sollte nun auf Port 3306 einen Datenbankprozess laufen haben.

- Führt im Terminal im noch immer selben Pfad (also z.B: "C:\uni\projekte\zukunftsbaum\code") den Befehl "php bin/console doctrine:database:create" aus, um die DB zu erstellen.

- Führt dann den befehl "php bin/console doctrine:migrations:migrate" aus, um die DB zu um die Tabellen zu erstellen.

- Öffnet die Seite "127.0.0.1:8000/dbtest". Wenn alles funktioniert hat, sollte die Nachricht "DB-Server funktioniert!" erscheinen.

- Fertig!


Weitere nützliche Befehle: "php bin/console make:entity" , "php bin/console make:migration" , "php bin/console make:entity --regenerate --overwrite"


