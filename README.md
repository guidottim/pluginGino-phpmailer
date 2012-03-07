pluginGino-phpmailer
====================

Plugin per [gino CMS](https://github.com/otto-torino/gino) per l'invio di email con la libreria PHPMailer (http://phpmailer.worxware.com/).   
Attualmente si fa riferimento alla versione 5.2.1.

Installazione
-------------

* scaricare la libreria dal sito del progetto
* scompattare il file compresso nella directory lib e rinominare la directory senza il numero di versione, ad esempio:

	# mv PHPMailer_5.2.1 PHPMailer

Il file PHPMailer/class.phpmailer.php deve essere caricato una sola volta nello script (poi si potranno inviare infinite email).

Utilizzo
--------

Per attivare la classe occorre includerla prima di richiamarla:

	require_once(PLUGIN_DIR.OS.'/plugin/plugin.phpmailer.php');

plugin.phpmailer.php
--------------------

La classe plugin_phpmailer contiene i metodi che permettono di interfacciarsi con la libreria PHPMailer e che gestiscono l'invio delle email.

**Metodi pubblici**

void **__construct**()

@include PHPMailer/class.phpmailer.php

boolean **sendPHPMail**(string $**email_dest**, string $**email_mitt**, string $**oggetto**, string $**contenuto**, array $**options**=array())

@param email_dest		indirizzo email del destinatario   
@param email_mitt		indirizzo email del mittente   
@param oggetto			oggetto dell'email   
@param contenuto		contenuto dell'email   
@param options   

// METHODS, RECIPIENTS

	* nome_mitt [string]
	* email_reply [string]
	* nome_reply [string]
	* cc [string]: aggiungere uno o più destinatari come Copia Conoscenza (separati da virgola)
	* ccn [string]: aggiungere uno o più destinatari come Copia Conoscenza Nascosta (comoda in un sistema di Newsletter se inserita all'interno di un ciclo for)
	* notification [string]: indirizzo per la conferma di lettura (se richiesta)

// PROPERTIES FOR SMTP

	* smtp_server [string]: indirizzo SMTP (se è attivo l'invio tramite SMTP)
	* smtp_port [integer]: (default 25)
	* smtp_auth [boolean]: autenticazione SMTP (default false)
	* smtp_user [string]: account per l'autenticazione SMTP (deve essere una casella attiva e funzionante sul server, altrimenti potrà essere considerata SPAM)
	* smtp_password [string]: password dell'account SMTP

// METHODS, VARIABLES

	* exception [boolean]: abilita le eccezioni esterne (default false)
	* debug [integer]: informazioni per il DEBUG: 1=solo errori, 2=tutti i messaggi, 0=niente (default)
	* issmtp [boolean]: attiva l'invio tramite SMTP (default true)
	* ishtml [boolean]: dichiaro che è una email html (default true)
	* charset [integer]: set di caratteri (default UTF-8)
	* priority [integer]: (default 3)

// METHODS, MESSAGE CREATION, ATTACHMENTS

	* template [string]: percorso al file di template (formato HTML)
	* allegati [string]: nome degli allegati (nel template)
	* attachment [array]: allegati nella forma 'path_to_file'=>'file_name'

Esempio
-------

	public function testEmail(){
		
		require_once(PLUGIN_DIR.OS.'/plugin/plugin.phpmailer.php');
		$mailer = new plugin_phpmailer();
		$mailer->sendPHPMail("support@example.com", "test@example.com", "Prova invio da plugin", "Testo dell'email", 
		array(
			'debug'=>2, 
			'ishtml'=>false, 
			'smtp_server'=>'smtp.example.com', 
			'smtp_auth'=>true, 
			'smtp_user'=>"smtp@example.com", 
			'smtp_password'=>"password"
		));
		exit();
	}

Note sulla costruzione delle email in formato HTML
--------------------------------------------------

* Usare CSS inline per il testo e i link (ovvero senza servirsi di documenti .css esterni o caricati sul server): inserire gli stili CSS direttamente nel corpo dell'email (o del template).   
Outlook non riconosce gli sfondi nelle tabelle perciò usate solo tinte unite tramite l’attributo bgcolor dei CSS.
* Usare solo Tabelle e non elementi DIV. Purtroppo Microsoft scansa accuratamente i DIV e le E-mail vanno costruite con le Tabelle.
* Usare inline anche gli attributi di stile delle tabelle, ovvero direttamente nel TAG TABLE (p.e. <TR style="...">".
* Usare le immagini JPEG (scordarsi la trasparenza dei PNG)

Licenza
-------

MIT License

Links
-----------------

Si prega di segnalare bug, errori e consigli alla pagina del progetto su github: http://github.com/guidottim/pluginGino-phpmailer