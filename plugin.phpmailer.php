<?php
/**
 * @file plugin.phpmailer.php
 * @brief Contiene la classe plugin_phpmailer
 * 
 * @copyright 2005 Otto srl (http://www.opensource.org/licenses/mit-license.php) The MIT License
 * @author marco guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */

/**
 * @brief Invio email con la libreria PHPMailer (http://phpmailer.worxware.com/)
 * 
 * Il sito ufficiale della libreria PHPMailer è http://phpmailer.worxware.com/. \n
 * Attualmente si fa riferimento alla versione 5.2.1.
 * 
 * @copyright 2005 Otto srl (http://www.opensource.org/licenses/mit-license.php) The MIT License
 * @author marco guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 * 
 * Il metodo sendPHPMail() si occupa di inviare le email utilizzando la libreria PHPMailer. \n
 * Qualora non si voglia attivare l'invio tramite SMTP (opzione @a issmtp), le email verranno inviate direttamente con la funzione base di PHP.
 * 
 * 
 * INSTALLAZIONE
 * ========================================================================
 * - scaricare la libreria dal sito del progetto
 * - scompattare il file compresso della libreria nella directory lib e rinominare la directory senza il numero di versione, ad esempio:
 * @code
 * # mv PHPMailer_5.2.1 PHPMailer
 * @endcode
 * 
 * Il file PHPMailer/class.phpmailer.php deve essere caricato una sola volta nello script (poi si potranno inviare infinite email).
 * 
 * Note sulla costruzione delle email in formato HTML
 * ========================================================================
 * - Usare CSS inline per il testo e i link (ovvero senza servirsi di documenti .css esterni o caricati sul server): inserire gli stili CSS direttamente nel corpo dell'email (o del template).
 * Outlook non riconosce gli sfondi nelle tabelle perciò usate solo tinte unite tramite l’attributo bgcolor dei CSS.
 * - Usare solo Tabelle e non elementi DIV. Purtroppo Microsoft scansa accuratamente i DIV e le E-mail vanno costruite con le Tabelle.
 * - Usare inline anche gli attributi di stile delle tabelle, ovvero direttamente nel TAG TABLE (p.e. <TR style="...">".
 * - Usate le immagini JPEG (scordatevi la trasparenza dei PNG)
 * 
 * Esempio di utilizzo
 * ========================================================================
 * @code
 * public function testEmail(){
 *   
 *   require_once(PLUGIN_DIR.OS.'plugin.phpmailer.php');
 *   $mailer = new plugin_phpmailer();
 *   $mailer->sendPHPMail("support@example.com", "test@example.com", "Prova invio da plugin", "Testo dell'email", 
 *   array(
 *     'debug'=>2, 
 *     'ishtml'=>false, 
 *     'smtp_server'=>'smtp.example.com', 
 *     'smtp_auth'=>true, 
 *     'smtp_user'=>"smtp@example.com", 
 *     'smtp_password'=>"password"
 *   ));
 *   exit();
 * }
 * @endcode
 */
class plugin_phpmailer {
	
	/**
	 * Costruttore
	 * 
	 * Include il file PHPMailer/class.phpmailer.php
	 * 
	 * @return void
	 */
	function __construct() {
		
		include_once(LIB_DIR.OS.'PHPMailer/class.phpmailer.php');
	}
	
	/**
	 * Parametri di alcuni Server SMTP
	 * 
	 * Riporta i parametri di alcuni server SMTP.
	 * 
	 * @param string $service nome del servizio (gmail)
	 * @return array
	 */
	private function setSMTPParams($service) {
		
		$params = array();
		if($service == 'gmail')
		{
			$params['smtp_secure'] = 'ssl';
			$params['smtp_server'] = "smtp.gmail.com";
			$params['smtp_port'] = 465;
			$params['smtp_auth'] = true;
		}
		return $params;
	}
	
	/**
	 * Invio email con la funzione base di PHP
	 * 
	 * @see mail()
	 * @param string $to indirizzo di spedizione; alcuni esempi: 
	 *   - user@example.com
	 *   - user@example.com, anotheruser@example.com
	 *   - User <user@example.com>
	 *   - User <user@example.com>, Another User <anotheruser@example.com>
	 * @param string $subject oggetto dell'email da inviare
	 * @param string $message messaggio da inviare (ogni linea deve essere separata con un LF (\\n). Le linee non devono essere più larghe di 70 caratteri)
	 * @param array $options
	 *   array associativo di opzioni
	 *   - @b email_mitt (string): 
	 *   - @b nome_mitt (string): 
	 *   - @b email_reply (string): 
	 *   - @b nome_reply (string): 
	 *   - @b cc (string): aggiungere uno o più destinatari come Copia Conoscenza (separati da virgola); ad esempio: dest <mail@dest.it>,dest2 <mail@dest2.it>
	 *   - @b mailer (boolean): mostrare il mailer (versione di PHP)
	 * @return boolean
	 * 
	 * @b additional_headers: stringa che viene inserita alla fine dell'header dell'email (tipicamente utilizzata per aggiungere extra headers (From, Cc, and Bcc). \n
	 * Header extra multipli dovranno essere separati con un CRLF (\\r\\n). \n
	 * @b additional_parameters: parametro che può essere utilizzato per passare flags addizionali come opzioni di linea di comando al programma configurato per inviare le email, come definito nell'impostazione 'sendmail_path'.
	 */
	private function sendBaseMail($to, $subject, $message, $options=array()) {
		
		$email_mitt = array_key_exists('email_mitt', $options) ? $options['email_mitt'] : null;
		$nome_mitt = array_key_exists('nome_mitt', $options) ? $options['nome_mitt'] : null;
		$email_reply = array_key_exists('email_reply', $options) ? $options['email_reply'] : null;
		$nome_reply = array_key_exists('nome_reply', $options) ? $options['nome_reply'] : null;
		$cc = array_key_exists('cc', $options) ? $options['cc'] : null;
		$mailer = array_key_exists('mailer', $options) ? $options['mailer'] : false;
		
		$additional_headers = null;
		$additional_parameters = null;
		
		if($email_mitt || $email_reply || $mailer)
		{
			$additional_headers = '';
			
			if($email_mitt)
			{
				$additional_headers .= "From:";
				if($nome_mitt) $additional_headers .= $nome_mitt." <";
				$additional_headers .= $email_mitt;
				if($nome_mitt) $additional_headers .= ">";
				
				if($email_reply || $mailer)
					$additional_headers .= "\r\n";
			}
			if($email_reply)
			{
				$additional_headers .= "Reply-To:";
				if($nome_reply) $additional_headers .= $nome_reply." <";
				$additional_headers .= $email_reply;
				if($nome_reply) $additional_headers .= ">";
				
				if($mailer)
					$additional_headers .= "\r\n";
			}
			if($mailer)
			{
				$additional_headers .= "X-Mailer: PHP/" . phpversion();
			}
		}
		
		if($cc)
		{
			if($to)
				$to = $to.", ".$cc;
			else 
				$to = $cc;
		}
		
		$send = mail($to, $subject, $message, $additional_headers, $additional_parameters);
		
		return $send;
	}
	
	/**
	 * Invio dell'email
	 * 
	 * @param string $email_dest indirizzo email del destinatario
	 * @param string $email_mitt indirizzo email del mittente
	 * @param string $oggetto oggetto dell'email
	 * @param string $contenuto contenuto dell'email
	 * @param array $options opzioni
	 *   array associativo di opzioni
	 *   - // METHODS, RECIPIENTS
	 *   - @b nome_mitt (string)
	 *   - @b email_reply (string)
	 *   - @b nome_reply (string)
	 *   - @b cc (string): aggiungere uno o più destinatari come Copia Conoscenza (separati da virgola)
	 *   - @b ccn (string): aggiungere uno o più destinatari come Copia Conoscenza Nascosta (comoda in un sistema di Newsletter se inserita all'interno di un ciclo for)
	 *   - @b notification (string): indirizzo per la conferma di lettura (se richiesta)
	 *   - // PROPERTIES FOR SMTP
	 *   - @b issmtp (boolean): attiva l'invio tramite SMTP (default true)
	 *   - @b smtp_service (string): nome del servizio del server SMTP
	 *   - @b smtp_secure (string): imposta il prefisso del server
	 *   - @b smtp_server (string): indirizzo SMTP (se è attivo l'invio tramite SMTP)
	 *   - @b smtp_port (integer): (default 25)
	 *   - @b smtp_auth (boolean): autenticazione SMTP (default false)
	 *   - @b smtp_user (string): account per l'autenticazione SMTP (deve essere una casella attiva e funzionante sul server, altrimenti potrà essere considerata SPAM)
	 *   - @b smtp_password (string): password dell'account SMTP
	 *   - // METHODS, VARIABLES
	 *   - @b exception (boolean): abilita le eccezioni esterne (default false)
	 *   - @b debug (integer): informazioni per il DEBUG: 1=solo errori, 2=tutti i messaggi, 0=niente (default)
	 *   - @b ishtml (boolean): dichiaro che è una email html (default true)
	 *   - @b charset (integer): set di caratteri (default UTF-8)
	 *   - @b priority (integer): (default 3)
	 *   - // METHODS, MESSAGE CREATION, ATTACHMENTS
	 *   - @b template (string): percorso al file di template (formato HTML)
	 *   - @b allegati (string): nome degli allegati (nel template)
	 *   - @b attachment (array): allegati nella forma 'path_to_file'=>'file_name'
	 * @return boolean
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
		$issmtp = array_key_exists('issmtp', $options) ? $options['issmtp'] : true;
		$smtp_service = array_key_exists('smtp_service', $options) ? $options['smtp_service'] : null;
		$smtp_secure = array_key_exists('smtp_secure', $options) ? $options['smtp_secure'] : null;
		$smtp_server = array_key_exists('smtp_server', $options) ? $options['smtp_server'] : '';
		$smtp_port = array_key_exists('smtp_port', $options) ? $options['smtp_port'] : 25;
		$smtp_auth = array_key_exists('smtp_auth', $options) ? $options['smtp_auth'] : false;
		$smtp_user = array_key_exists('smtp_user', $options) ? $options['smtp_user'] : '';
		$smtp_password = array_key_exists('smtp_password', $options) ? $options['smtp_password'] : '';
		
		// Impostazioni Email
		$exception = array_key_exists('exception', $options) ? $options['exception'] : false;
		$debug = array_key_exists('debug', $options) ? $options['debug'] : 0;
		$ishtml = array_key_exists('ishtml', $options) ? $options['ishtml'] : true;
		$charset = array_key_exists('charset', $options) ? $options['charset'] : 'UTF-8';
		$priority = array_key_exists('priority', $options) ? $options['priority'] : 3;
		
		if(!$issmtp)
			return $this->sendBaseMail($email_dest, $oggetto, $contenuto, 
				array(
					'email_mitt'=>$email_mitt, 
					'nome_mitt'=>$nome_mitt, 
					'email_reply'=>$email_mitt, 
					'nome_reply'=>$nome_mitt, 
					'cc'=>$cc
				));
		
		$mail = new PHPMailer($exception);
		$mail->IsSMTP($issmtp);
		$mail->CharSet = $charset;
		
		if($smtp_service)
		{
			$params = $this->setSMTPParams($smtp_service);
			
			if(array_key_exists('smtp_secure', $params)) $smtp_secure = $params['smtp_secure'];
			if(array_key_exists('smtp_server', $params)) $smtp_server = $params['smtp_server'];
			if(array_key_exists('smtp_port', $params)) $smtp_port = $params['smtp_port'];
			if(array_key_exists('smtp_auth', $params)) $smtp_auth = $params['smtp_auth'];
		}
		
		if($smtp_secure)
			$mail->SMTPSecure = $smtp_secure;
		$mail->Host = $smtp_server;
		$mail->Port = $smtp_port;
		$mail->SMTPDebug = $debug;
		if($smtp_auth)
		{
			$mail->SMTPAuth = $smtp_auth;
			$mail->Username = $smtp_user;
			$mail->Password = $smtp_password;
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
