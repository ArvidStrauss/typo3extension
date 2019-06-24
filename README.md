# typo3extension

Typo3Extension to administrate offers

**_ Diese Typo 3 Extension verbindet sich mit einer IBM INFORMIX - Daten bank _**
**_ Damit dies möglich ist, muss zunächst ein Treiber installiert werden _**

**_Installation PDO Informix Treiber für Debian/Linux Server_**

- [ ] Client SDK installieren
- [ ] PDO_Informix Treiber erstellen
- [ ] PDO_Informix Treiber aktivieren/konfigurieren

**_Client SDK installieren_**

Wechsle ins `/opt` Verzeichnis und erstelle einen neuen Ordner `IBM` und wechsle ebenfalls in diesen:

```bash
cd /opt
mkdir IBM
cd IBM
```

Lade nun die
**Client SDK Developer Edition für Linux x86_64, 64-Bit Version 4.10 FC9DE** über folgenden Link herunter und lege Sie in dem eben erstellten `IBM` Verzeichnis ab:

[Download-Link](https://www-01.ibm.com/marketing/iwm/iwm/web/pickUrxNew.do?source=ifxdl)

Entpacke nun die heruntergeladene Datei:

```bash
tar -xvf clientsdk.4.10.FC9DE.LINUX.tar
```

Starte nun die Installation des Client SDK:

```bash
./installclientsdk
```

Bei der anschließenden Menüführung die folgenden Auswahlen treffen:

- Enter drücken
- '1' eingeben und Enter drücken um Lizenzvereinbarung zu akzeptieren
- Das vorgegebene Installations-Verzeichnis beibehalten und mit Enter bestätigen
- Bei Abfrage der Features Enter drücken, um die voreingestellten Features zu installieren
- Bei Zusammenfassung erneut mit Enter bestätigen
- Am Ende der Installation ein letztes mal mit Enter drücken

> **_Fertig! Die Installation vom Client SDK ist nun abgeschlossen_**

- [x] Client SDK installieren
- [ ] PDO_Informix Treiber erstellen
- [ ] PDO_Informix Treiber aktivieren/konfigurieren

**_PDO_Informix Treiber erstellen_**

Wechsle erneut ins `/opt` Verzeichnis, erstelle ein neues Verzeichnis mit Namen `pdo` und wechsle in dieses.

```bash
cd /opt
mkdir pdo
cd pdo
```

Lade nun die aktuelle PDO_Informix Treiber Hilfsdateien mittels wget herunter.

```bash
wget http://pecl.php.net/get/PDO_INFORMIX-1.3.3.tgz
```

Entpacke das heruntergeladene Zip_File

```bash
tar zxf PDO_INFORMIX-1.3.3.tgz
```

Und wechsle in das nun entstandene Verzeichnis

```bash
cd PDO_INFORMIX-1.3.3
```

INFORMIXDIR Umgebungsvariable setzen:

```bash
export INFORMIXDIR=/opt/IBM/informix
```

Starte nun phpize. Achtung, es kann sein, dass es noch nicht installiert ist. In diesem Fall gehe zum nächsten Schritt.

```bash
phpize
```

Falls phpize noch nicht installiert ist, dann installiere es mit folgendem Befehl

```bash
sudo apt-get install php7.x-dev
```

Bestätige die Installation mit `Ja` und führe nun `phpize` erneut aus oder ignoriere diesen Schritt, falls es schon beim ersten mal funktioniert hat.

Konfiguriere die Treiber-Dateien

```bash
./configure
```

Falls Fehler-Meldung

> error: Cannot find php_pdo_driver.h

auftritt, dann nimm folgende Einstellungen vor.

Setze einen Soft-Link in die entsprechenden Ordner.

```bash
ln -s /usr/include/php/20170718/ext /usr/include/php/ext
```

Führe `./configure` nun erneut aus oder überspringe den Schritt, falls die Fehlermeldung nicht kam.

Erstelle nun den Treiber mit den folgenden 2 Befehlen.

```bash
make
make install
```

> **_Fertig! Die Erstellung des PDO_Informix Treibers ist nun abgeschlossen_**

- [x] Client SDK installieren
- [x] PDO_Informix Treiber erstellen
- [ ] PDO_Informix Treiber aktivieren/konfigurieren

**_PDO_Informix Treiber aktivieren_**
Wechsel in das Mods Verzeichnis und erstelle eine .ini Datei mit dem Namen unseres Treibers als Inhalt. Wechsle anschließend ins Php/Apache-Verzeichnis und erstelle dort einen Soft-Link zu dieser eben erstellten .ini Datei.

```bash
cd /etc/php/7.x/mods-available
echo "extension=pdo_informix.so" > pdo_informix.ini
cd /etc/php/7.x/apache2/conf.d
ln -s /etc/php/7.x/mods-available/pdo_informix.ini 20-pdo_informix.ini
```

Wechsle nun ins Haupt-Apache Verzeichnis

```bash
cd /etc/apache2
```

Ergänze die dort vorhandene Datei namens `envvars` mit folgenden 4 Zeilen:

> export INFORMIXDIR=/opt/IBM/informix

> export LD_LIBRARY_PATH=/opt/IBM/informix/lib:/opt/IBM/informix/lib/esql:/opt/IBM/informix/lib/client:/opt/IBM/informix/lib/cli:\$LD_LIBRARY_PATH

> export APACHE_EXTENDED_STATUS="off"

> export DB_LOCALE=de_DE.8859-1 export CLIENT_LOCALE=de_DE.8859-1

Stoppe nun den Apache-Server

```bash
apachectl stop
```

Und starte ihn neu

```bash
apachectl start
```

> **_Fertig! Die Installation vom PDO_Informix Treiber ist nun abgeschlossen._**

- [x] Client SDK installieren
- [x] PDO_Informix Treiber erstellen
- [x] PDO_Informix Treiber aktivieren/konfigurieren

> **_Du solltest dich nun problemlos mit der Datenbank und einem geeigneten PDO-Connection String verbinden können_**
