<?php
/**
 * Invio email con la libreria PHPMailer (http://phpmailer.worxware.com/).
 * Attualmente si fa riferimento alla versione 5.2.1
 * 
 * ========================
 * INSTALLAZIONE
 * ========================
 * 1. scaricare la libreria dal sito del progetto
 * 2. scompattare il file compresso nella directory lib e rinominare la directory senza il numero di versione, ad esempio: # mv PHPMailer_5.2.1 PHPMailer
 * 
 * Il file PHPMailer/class.phpmailer.php deve essere caricato una sola volta nello script (poi si potranno inviare infinite email)
 * 
 * ========================
 * Note sulla costruzione delle email in formato HTML
 * ========================
 * 1. Usare CSS inline per il testo e i link (ovvero senza servirsi di documenti .css esterni o caricati sul server): inserire gli stili CSS direttamente nel corpo dell'email (o del template).
 * Outlook non riconosce gli sfondi nelle tabelle perciò usate solo tinte unite tramite l’attributo bgcolor dei CSS.
 * 2. Usare solo Tabelle e non elementi DIV. Purtroppo Microsoft scansa accuratamente i DIV e le E-mail vanno costruite con le Tabelle.
 * 3. Usare inline anche gli attributi di stile delle tabelle, ovvero direttamente nel TAG TABLE (p.e. <TR style="...">".
 * 4. Usate le immagini JPEG (scordatevi la trasparenza dei PNG)
 * 
 * ========================
 * Esempio di utilizzo
 * ========================
 * 
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
 */

class plugin_phpmailer {
	
	function __construct() {
		
		include_once(LIB_DIR.OS.'PHPMailer/class.phpmailer.php');
	}
	
	/**
	 * @param string $email_dest	indirizzo email del destinatario
	 * @param string $email_mitt	indirizzo email del mittente
	 * @param string $oggetto		oggetto dell'email
	 * @param string $contenuto		contenuto dell'email
	 * @param array $options		opzioni
	 * 		// METHODS, RECIPIENTS
	 * 		nome_mitt		string
	 * 		email_reply		string
	 * 		nome_reply		string
	 * 		cc				string		aggiungere uno o più destinatari come Copia Conoscenza (separati da virgola)
	 * 		ccn				string		aggiungere uno o più destinatari come Copia Conoscenza Nascosta (comoda in un sistema di Newsletter se inserita all'interno di un ciclo for)
	 * 		notification 	string		indirizzo per la conferma di lettura (se richiesta)
	 *		// PROPERTIES FOR SMTP
	 * 		smtp_server		string		indirizzo SMTP (se è attivo l'invio tramite SMTP)
	 * 		smtp_port		integer		(default 25)
	 * 		smtp_auth		boolean		autenticazione SMTP (default false)
	 * 		smtp_user		string		account per l'autenticazione SMTP (deve essere una casella attiva e funzionante sul server, altrimenti potrà essere considerata SPAM)
	 * 		smtp_password	string		password dell'account SMTP
	 * 		// METHODS, VARIABLES
	 * 		exception		boolean		abilita le eccezioni esterne (default false)
	 * 		debug			integer		informazioni per il DEBUG: 1=solo errori, 2=tutti i messaggi, 0=niente (default)
	 * 		issmtp			boolean		attiva l'invio tramite SMTP (default true)
	 * 		ishtml			boolean		dichiaro che è una email html (default true)
	 * 		charset			integer		set di caratteri (default UTF-8)
	 * 		priority		integer		(default 3)
	 *		// METHODS, MESSAGE CREATION, ATTACHMENTS
	 * 		template		string		percorso al file di template (formato HTML)
	 * 		allegati		string		nome degli allegati (nel template)
	 * 		attachment		array		allegati nella forma 'path_to_file'=>'file_name'
	 */
	public function sendPHPMail($email_dest, $email_mitt, $oggetto, $contenuto, $options=array()) {

		$nome_mitt = array_key_exists('nome_mitt', $options) ? $options['nome_mitt'] : '';
		$email_reply = array_key_exists('email_reply', $options) ? $options['email_reply'] : '';
		$nome_reply = array_key_exists('nome_reply', $options) ? $options['nome_reply'] : '';
		$cc = array_key_exists('cc', $options) ? $options['cc'] : '';
		$ccn = array_key_exists('ccn', $options) ? $options['ccn'] : '';
		$notification = array_key_exists('notification', $options) ? $options['notification'] : '';
		
		$template = array_key_exists('template', $options) ? $options['template'] : '';
		$allegati = array_key_exists('allegati', $options) ? $options['allegati'] : '';
		$attachment = (array_key_exists('attachment', $options) && is_array($options['attachment'])) ? $options['attachment'] : array();
		
		// Server SMTP
		$smtp_server = array_key_exists('smtp_server', $options) ? $options['smtp_server'] : '';
		$smtp_port = array_key_exists('smtp_port', $options) ? $options['smtp_port'] : 25;
		$smtp_auth = array_key_exists('smtp_auth', $options) ? $options['smtp_auth'] : false;
		$smtp_user = array_key_exists('smtp_user', $options) ? $options['smtp_user'] : '';
		$smtp_password = array_key_exists('smtp_password', $options) ? $options['smtp_password'] : '';
		
		// Impostazioni Email
		$exception = array_key_exists('exception', $options) ? $options['exception'] : false;
		$debug = array_key_exists('debug', $options) ? $options['debug'] : 0;
		$issmtp = array_key_exists('issmtp', $options) ? $options['issmtp'] : true;
		$ishtml = array_key_exists('ishtml', $options) ? $options['ishtml'] : true;
		$charset = array_key_exists('charset', $options) ? $options['charset'] : 'UTF-8';
		$priority = array_key_exists('priority', $options) ? $options['priority'] : 3;
		
		$mail = new PHPMailer($exception);
		$mail->IsSMTP($issmtp);
		$mail->CharSet = $charset;
		
		if($issmtp)
		{
			$mail->Host = $smtp_server;
			$mail->Port = $smtp_port;
			$mail->SMTPDebug = $debug;
			if($smtp_auth)
			{
				$mail->SMTPAuth = $smtp_auth;
				$mail->Username = $smtp_user;
				$mail->Password = $smtp_password;
			}
		}
		if($email_reply) $mail->AddReplyTo($email_reply, $nome_reply);
		
		$mail->Priority = $priority;
		$mail->AddAddress($email_dest);
		$mail->SetFrom($email_mitt, $nome_mitt);
		
		// Varianti
		if($cc) $mail->AddCC($cc);		
		if($ccn) $mail->AddBCC($ccn);
		if($notification) $mail->ConfirmReadingTo = $notification;
		
		// Corpo Email
		
		if(is_file($template))
		{
			$file = fopen($template, 'r');
			if($file !== NULL) {
				$contents = fread($file, filesize($template));
				$contents = eregi_replace("var_mail_messaggio", $contenuto, $contents);
				$contents = eregi_replace("var_mail_allegati", $allegati, $contents);
				$contents = eregi_replace("var_mail_data", date("d/m/Y",time()), $contents);
				$contents = eregi_replace("var_mail_ora", date("H:i",time()), $contents);
				
				//$contents = eregi_replace("var_mail_Htitle", '<img src="cid:Htitle" width="650" height="40" alt="header" />', $contents);//inserisco l'immagine in alto
				//$contents = eregi_replace("var_mail_bottom", '<img src="cid:bottom" width="650" height="10" alt="bottom" />', $contents);//inserisco l'immagine in basso
				
				//Messaggio alternativo nel caso in cui il destinatario non possa vedere il formato HTML
				$Alternative = "$contenuto\n\n";
			}
			fclose($file);
		}
		else
		{
			$contents = "
			<table align=\"center\" width=\"600\" border=\"0\" bgcolor=\"FFFFFF\">
				<tr><td><div style=\"text-align:left; padding:8px 4px; background-color:#D5D5CF; font-size:10px;\">$contenuto</div>
				</td></tr>
			</table>";
			$Alternative = "$contenuto\n\n";
		}
		
		// Invio Email
		
		$mail->Subject = $oggetto;
		
		if($ishtml)
		{
			$mail->Body = $contents;		// inserisco il contenuto HTML nel Body
			$mail->IsHTML($ishtml);
			$mail->AltBody = $Alternative;	// includo il messaggio in formato testo
			
			/*
			// aggiungere le immagini inline: filename, cid, name
			if(is_file("header.jpg"))
				$mail->AddEmbeddedImage('header.jpg', 'Htitle', 'header.jpg');
			if(is_file("bottom.jpg"))
				$mail->AddEmbeddedImage('bottom.jpg', 'bottom', 'bottom.jpg');
			*/
		}
		else
		{
			$mail->Body = $Alternative;
			$mail->isHTML($ishtml);
		}
		
		// Aggiungere allegati
		if(sizeof($attachment) > 0)
		{
			foreach($attachment AS $key=>$value)
			{
				$mail->AddAttachment($key, $value);
			}
		}

		if($mail->Send()) return true; else return false;
	}
}

?>
