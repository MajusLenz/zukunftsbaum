# Zukunftsbaum

HSD-Projekt.

Falls ihr gerade diese README lest, wisst ihr wahrscheinlich bereits worum es geht. Wenn nicht: Wiedersehn.

* ACHTUNG: Niemals Passwörter, z.B. in configs commiten!!!




# Installationsanleitung:

- Installiert euch die neuste PHP Version 7.3.7 auf euren Rechner. Am leichtesten in dies über den "Windows Web Platform Installer". Für Windows 8 könnt ihr die Aneitung nutzen die ich auch benutzt habe: https://support.microsoft.com/de-de/help/2819022/how-to-install-and-configure-php-on-iis-8-in-windows-8. Für ein anderes Betriebssystem müsst ihr selber googlen.

- Installiert euch die neuste Composer Version auf euren Rechner.

- Installiert euch die neuste GIT Version auf euren Rechner.

- Installiert euch die neuste Symfony Version auf euren Rechner. (https://symfony.com/download)

- Install XXAMP for DB   ----> TODO Erklärung

- Klont dieses Git-Repository (https://github.com/MajusLenz/zukunftsbaum.git) auf euren Rechner in einen Pfad eurer Wahl. Z.B. "C:\uni\projekte\zukunftsbaum\code"

- Navigiert im Terminal in den Pfad. (cd "C:\uni\projekte\zukunftsbaum\code")

- Führt dort den Befehl "composer install" zum installieren der Dependencies aus.

- Führt dort den Befehl "symfony local:php:list" aus, um Symfony bekannte PHP-Versionen durch die neu installierte PHP-Version zu erweitern.

- Führt dann den Befehl "symfony server:start --no-tls" zum starten des lokalen Webservers aus. Es sollte die URL "127.0.0.1:8000" o.ä auf der Konsole ausgegeben werden.

- Öffnet die URL in eurem Browser. Sollte alles funktioniert haben, sollte die Symfony-default-page angezeigt werden. ("Welcome to
Symfony 5.0.1 ...")
