<?php
/**
 * @mainpage Applicazione per l'invio delle email con la libreria PHPMailer
 * 
 * PHPMailer gestisce l'invio di email in formato html e con autenticazione smtp. \n
 * Il sito ufficiale della libreria PHPMailer è http://phpmailer.worxware.com/.
 * 
 * INSTALLAZIONE
 * ---------------
 * 1. Scaricare la libreria dal repository github https://github.com/Synchro/PHPMailer
 * 2. Decomprimere il file del progetto nella directory lib di gino e rinominare la directory @a PHPMailer, ad esempio:
 * @code
 * # mv PHPMailer_master PHPMailer
 * @endcode
 * 
 * Il file PHPMailer/class.phpmailer.php deve essere caricato una sola volta nello script (poi si potranno inviare infinite email).
 * 
 * UTILIZZO
 * ---------------
 * 1. Includere nel metodo che si occupa dell'invio delle email il file plugin.phpmailer.php
 * @code
 * require_once(PLUGIN_DIR.OS.'plugin.phpmailer.php');
 * @endcode
 * 2. Utilizzare il metodo sendMail() per inviare le email; questo metodo si interfaccia alla libreria PHPMailer. \n
 * La scelta dell'utilizzo della libreria PHPMailer o della funzione base di php (mail) per l'invio di una email avviene tramite l'opzione @a php_mail: 
 *   - php_mail true, funzione mail (default)
 *   - php_mail false, libreria PHPMailer
 * 
 * Optando per la libreria PHPMailer è inoltre possibile scegliere la tipologia di mailer; di default il valore è @a mail.
 * 
 * ###ESEMPIO
 * Esempio di metodo per l'invio di una email con la tipologia di mailer @a smtp
 * @code
 * public function testEmail(){
 *   require_once(PLUGIN_DIR.OS.'plugin.phpmailer.php');
 *   $mailer = new \Gino\Plugin\plugin_phpmailer();
 *   $send = $mailer->sendMail("support@test.com", "info@test.com", "Prova invio da plugin", "Testo dell'email", 
 *   array(
 *     'php_mail'=>false, 
 *     'mailer'=>'smtp', 
 *     'debug'=>2, 
 *     'reply_email'=>'reply@test.com', 
 *     'reply_name'=>'Test Email', 
 *     'ishtml'=>false, 
 *     'smtp_server'=>'smtp.example.com', 
 *     'smtp_auth'=>true, 
 *     'smtp_user'=>"smtp@test.com", 
 *     'smtp_password'=>"password"
 *   ));
 *   
 *   return $send;
 * }
 * @endcode
 * 
 * CONTENUTO DELL'EMAIL
 * ---------------
 * Una email può essere in formato html o testo. Per definire il tipo di formato impostare l'opzione @a ishtml nel metodo sendMail()
 * @code
 * array(
 *   ...
 *   'ishtml'=>[true|false], 
 * )
 * @endcode
 * 
 * ###ALLEGATI
 * Per allegare dei file all'email utilizzare l'opzione @a attachments nel metodo sendMail(). 
 * Ogni file viene definito come un array con le chiavi @a path e @a name
 * @code
 * array(
 *   ...
 *   'attachments'=>array(
 *     array(
 *       'path'=>CONTENT_DIR.OS.'attachment'.OS.'prova1.pdf', 
 *       'name'=>'pippo.pdf'
 *     ), 
 *     array(
 *       'path'=>CONTENT_DIR.OS.'attachment'.OS.'prova2.pdf', 
 *       'name'=>'pluto.pdf'
 *     )
 *   ), 
 * )
 * @endcode
 * 
 * ###FORMATO HTML
 * Utilizzando il formato html è possibile formattare il corpo dell'email con i tag html. Il contenuto può essere definito nel codice all'interno di una variabile oppure in una specifica vista. \n
 * Nel primo caso avremo ad esempio
 * @code
 * $contents = "
 * <table align=\"center\" width=\"600\" border=\"0\" bgcolor=\"FFFFFF\">
 *   <tr>
 *     <td><div style=\"text-align:left; padding:8px 4px; background-color:#F2F2F2; font-size:10px;\">Testo dell'email</div></td>
 *   </tr>
 * </table>";
 * @endcode
 * 
 * Nel caso della vista il contenuto viene definito nella classe controller attraverso la classe Gino.View; 
 * prendendo come riferimento per la costruzione delle email html il template mailing.php (presente nel progetto) abbiamo ad esempio:
 * @code
 * $view = new \Gino\View($this->_view_dir);
 * $view->setViewTpl('mailing');
 * 
 * $root_absolute_url = $this->_registry->request->root_absolute_url;
 * $dict = array(
 *   'image' => $root_absolute_url.'app/blog/img/image.gif',
 *   'title' => _("Mailing"), 
 *   'subtitle' => null, 
 *   'items' => $items,
 * );
 * $contents = $view->render($dict);
 * @endcode
 * 
 * ###CREARE UN MESSAGGIO DA UNA STRINGA HTML
 * Per creare un messaggio in modo automatico a partire da una stringa html occorre attivare l'opzione @a automatic_html nel metodo sendMail()
 * @code
 * array(
 *   ...
 *   'automatic_html'=>true, 
 * )
 * @endcode
 * 
 * Con questa opzione si richiama PHPMailer::MsgHTML() che effettua in modo automatico le modifiche per le immagini inline e i background, 
 * e crea una versione di solo testo convertendo il codice html. 
 * PHPMailer::MsgHTML() sovrascrive qualsiasi valore esistente in PHPMailer::$Body e PHPMailer::$AltBody. \n
 * Il contenuto dell'email è quello passato nel parametro @a $contents di sendMail(), come ad esempio
 * @code
 * $contents = file_get_contents('contents.html');
 * @endcode
 * 
 * ###IMMAGINI INLINE
 * Se si vuole costruire una email con delle immagini incorporate nel testo è necessario allegare le immagini e collegarle con il tag 
 * @code
 * <img src="cid:CID" />
 * @endcode
 * dove CID è il content ID dell'allegato, ovvero il riferimento dell'immagine. \n
 * Le immagini devono essere allegate con l'opzione @a embedded_images nel metodo sendMail(). Ogni file viene definito come un array con le chiavi @a path, @a cid e @a name
 * @code
 * array(
 *   ...
 *   'embedded_images'=>array(
 *     array(
 *       'path'=>SITE_ROOT.OS.'app/blog/img/image.gif', 
 *       'cid'=>'img1'
 *     )
 *   ), 
 * )
 * @endcode
 * 
 * ###NOTE SULLA COSTRUZIONE DELLE EMAIL IN FORMATO HTML
 * 1. Usare CSS inline per il testo e i link (ovvero senza servirsi di file css esterni o caricati sul server): inserire gli stili CSS direttamente nel corpo dell'email. 
 * Outlook non riconosce gli sfondi nelle tabelle perciò usate solo tinte unite tramite l’attributo bgcolor dei CSS. \n
 * 2. Usare solo Tabelle e non elementi DIV. Purtroppo Microsoft scansa accuratamente i DIV e le E-mail vanno costruite con le Tabelle. \n
 * 3. Usare inline anche gli attributi di stile delle tabelle, ovvero direttamente nel TAG TABLE (p.e. <TR style=\"...\">). \n
 * 4. Usare le immagini jpeg (scordarsi la trasparenza dei png)
 * 
 * ECCEZIONI
 * ---------------
 * Per attivare le eccezioni impostare l'opzione @a exception nel metodo sendMail()
 * @code
 * array(
 *   ...
 *   'exception'=>true, 
 * )
 * @endcode
 * 
 * Esempio di eccezioni
 * @code
 * $mail = new PHPMailer(true);
 * $mail->IsSMTP();
 * try {
 *   $mail->Host       = "mail.yourdomain.com";
 *   $mail->SMTPDebug  = 2;
 *   ...
 *   $mail->Send();
 *   echo "Message Sent OK<p></p>\n";
 * } catch (phpmailerException $e) {
 *   echo $e->errorMessage(); //Pretty error messages from PHPMailer
 * } catch (Exception $e) {
 *   echo $e->getMessage(); //Boring error messages from anything else!
 * }
 * @endcode
 */

/**
 * @file plugin.phpmailer.php
 * @brief Contiene la classe plugin_phpmailer
 * 
 * @copyright 2013-2015 Otto srl (http://www.opensource.org/licenses/mit-license.php) The MIT License
 * @author marco guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */

/**
 * @namespace Gino.Plugin
 * @description Namespace che comprende classi di tipo plugin
 */
namespace Gino\Plugin;

require_once(LIB_DIR.OS.'PHPMailer/class.phpmailer.php');
require_once(LIB_DIR.OS.'PHPMailer/class.pop3.php');
require_once(LIB_DIR.OS.'PHPMailer/class.smtp.php');

/**
 * @brief Interfaccia alla classe PHPMailer
 * 
 * @copyright 2013-2015 Otto srl (http://www.opensource.org/licenses/mit-license.php) The MIT License
 * @author marco guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */
class plugin_phpmailer {
	
	/**
     * percorso alla cartella contenente le view specifiche del modulo
     */
    private $_view_folder;
    
	/**
     * percorso alla cartella che contine le view di sistema
     */
    private $_dft_view_folder;
    
	/**
	 * Costruttore
	 * 
	 * @return void
	 */
	function __construct() {
		
		$this->_dft_view_folder = VIEWS_DIR;
	}
	
	/**
	 * Parametri (non standard) di alcuni server smtp
	 * 
	 * @param string $name nome del servizio smtp
	 *   - @a gmail
	 * @return array
	 */
	private function setSmtpParams($name) {
		
		$params = array();
		if($name == 'gmail')
		{
			$params['smtp_auth'] = true;
			$params['smtp_secure'] = 'tls';
			$params['smtp_server'] = "smtp.gmail.com";
			$params['smtp_port'] = 587;
		}
		return $params;
	}
	
	/**
	 * Invio email con la funzione mail di php
	 * 
	 * @see mail()
	 * @param string $to indirizzo del destinatario; alcuni esempi: 
	 *   - user@test.com
	 *   - user@test.com, anotheruser@test.com
	 *   - User <user@test.com>
	 *   - User <user@test.com>, Another User <anotheruser@test.com>
	 * @param string $subject oggetto dell'email da inviare
	 * @param string $message messaggio da inviare (ogni linea deve essere separata con un LF (\\n). Le linee non devono essere più larghe di 70 caratteri)
	 * @param array $options
	 *   array associativo di opzioni
	 *   - @b sender_email (string): 
	 *   - @b sender_name (string): 
	 *   - @b reply_email (string): 
	 *   - @b reply_name (string): 
	 *   - @b cc (string): aggiungere uno o più destinatari come Copia Conoscenza (separati da virgola e senza spazi); ad esempio:
	 *     - dest <mail@dest.it>,dest2 <mail@dest2.it>
	 *     - mail@dest.it,mail@dest2.it
	 *   - @b view_mailer (boolean): visualizza il mailer, ovvero la versione di php (default @a false)
	 *   - @b charset (string): set di caratteri (default @a UTF-8)
	 *   - @b crlf (string): default "\r\n"
	 * @return boolean
	 * 
	 * @b additional_headers: stringa che viene inserita alla fine dell'header dell'email (tipicamente utilizzata per aggiungere extra headers (From, Cc, and Bcc). \n
	 * Header extra multipli dovranno essere separati con un CRLF (\\r\\n). \n
	 * @b additional_parameters: parametro che può essere utilizzato per passare flags addizionali come opzioni di linea di comando al programma configurato per inviare le email, come definito nell'impostazione 'sendmail_path'.
	 */
	private function sendPhpMail($to, $subject, $message, $options=array()) {
		
		$sender_email = array_key_exists('sender_email', $options) ? $options['sender_email'] : null;
		$sender_name = array_key_exists('sender_name', $options) ? $options['sender_name'] : null;
		$reply_email = array_key_exists('reply_email', $options) ? $options['reply_email'] : null;
		$reply_name = array_key_exists('reply_name', $options) ? $options['reply_name'] : null;
		$cc = array_key_exists('cc', $options) ? $options['cc'] : null;
		$view_mailer = array_key_exists('view_mailer', $options) ? $options['view_mailer'] : false;
		$charset = array_key_exists('charset', $options) ? $options['charset'] : 'UTF-8';
		$crlf = array_key_exists('crlf', $options) && $options['crlf'] ? $options['crlf'] : "\r\n";
		
		$additional_headers = null;
		$additional_parameters = null;
		
		if($sender_email || $reply_email || $view_mailer || $cc)
		{
			$additional_headers = '';
			
			if($sender_email)
			{
				$additional_headers .= "From:";
				if($sender_name) $additional_headers .= $sender_name." <";
				$additional_headers .= $sender_email;
				if($sender_name) $additional_headers .= ">";
				
				if($reply_email || $view_mailer || $cc)
					$additional_headers .= $crlf;
			}
			if($reply_email)
			{
				$additional_headers .= "Reply-To:";
				if($reply_name) $additional_headers .= $reply_name." <";
				$additional_headers .= $reply_email;
				if($reply_name) $additional_headers .= ">";
				
				if($view_mailer || $cc)
					$additional_headers .= $crlf;
			}
			if($view_mailer)
			{
				$additional_headers .= "X-Mailer: PHP/" . phpversion();
				if($cc)
					$additional_headers .= $crlf;
			}
			if($cc)
			{
				$additional_headers .= "Cc:".$cc;
			}
		}
		
		if($charset == 'UTF-8')
		{
			$header_charset = 'MIME-Version: 1.0'."\r\n".'Content-type: text/plain; charset=UTF-8'.$crlf;
			$subject = "=?UTF-8?B?".base64_encode($subject).'?=';
			$additional_headers = $header_charset.$additional_headers;
		}
		
		$send = \mail($to, $subject, $message, $additional_headers, $additional_parameters);
		
		return $send;
	}
	
	/**
	 * Invio email
	 * 
	 * @see sendPhpMail()
	 * @see setSmtpParams()
	 * @param string $recipient_email indirizzo email del destinatario
	 * @param string $sender_email indirizzo email del mittente
	 * @param string $subject oggetto dell'email
	 * @param string $contents contenuto dell'email
	 * @param array $options opzioni
	 *   array associativo di opzioni
	 *   - @b php_mail (boolean): indica se utilizzare il metodo sendPhpMail() e il metodo mail del core di php per l'invio di una email (default @a true)
	 *   - @b mailer (string): tipologia di mailer utilizzato per l'invio
	 *     - @a mail (default)
	 *     - @a sendmail
	 *     - @a qmail
	 *     - @a smtp
	 *   - // METHODS, RECIPIENTS
	 *   - @b sender_name (string)
	 *   - @b reply_email (string)
	 *   - @b reply_name (string)
	 *   - @b cc (string): aggiungere uno o più destinatari come Copia Conoscenza (separati da virgola)
	 *   - @b ccn (string): aggiungere uno o più destinatari come Copia Conoscenza Nascosta (utile in un sistema di Newsletter se inserita all'interno di un ciclo)
	 *   - @b notification (string): indirizzo per la conferma di lettura (se richiesta)
	 *   - // PROPERTIES FOR SMTP
	 *   - @b smtp_name (string): nome del servizio del server smtp (@see setSmtpParams)
	 *   - @b smtp_secure (string): imposta il prefisso del server (ssl/tls)
	 *   - @b smtp_server (string): indirizzo smtp
	 *   - @b smtp_port (integer): numero della porta del servizio smtp (default 25)
	 *   - @b smtp_auth (boolean): autenticazione smtp (default false)
	 *   - @b smtp_auth_type (string): tipologia di autenticazione; LOGIN (default), PLAIN, NTLM, CRAM-MD5
	 *   - @b smtp_user (string): account per l'autenticazione SMTP (deve essere una casella attiva e funzionante sul server, altrimenti potrà essere considerata SPAM)
	 *   - @b smtp_password (string): password dell'account SMTP
	 *   - // METHODS, VARIABLES
	 *   - @b exception (boolean): per generare le eccezioni esterne (throw exceptions); default @a false
	 *   - @b debug (integer): informazioni per il DEBUG:
	 *     - 0, No output (default)
	 *     - 1, Commands
	 *     - 2, Data and commands
	 *     - 3, As 2 plus connection status
	 *     - 4, Low-level data output
	 *   - @b ishtml (boolean): dichiara che è una email html (default @a true)
	 *   - @b charset (string): set di caratteri (default @a utf-8)
	 *   - @b priority (integer): priorità (default @a 3)
	 *   - @b view_mailer (boolean): visualizza il mailer nell'invio di una email con il metodo sendPhpMail (default @a false)
	 *   - // ATTACHMENTS
	 *   - @b attachments (array): elenco degli allegati dove ogni file è un array con le chiavi
	 *     - @a path: percorso dell'allegato
	 *     - @a name: se definito sovrascrive il nome dell'allegato (non necessario)
	 *   - @b embedded_images (array): immagini inline (ovvero incorporate nel testo) dove ogni immagine è un array con le chiavi
	 *     - @a path: percorso dell'allegato
	 *     - @a cid: content id dell'allegato, ovvero il riferimento per collagarlo al tag IMG
	 *     - @a name: se definito sovrascrive il nome dell'allegato (non necessario)
	 *   - // OTHERS
	 *   - @b automatic_html (boolean): crea un messaggio in modo automatico a partire da una stringa html (default @a false); @see PHPMailer::msgHTML()
	 *   - @b alternative_text (string): messaggio in formato testo (nel caso in cui il destinatario non possa vedere il formato html)
	 *   - @b crlf (string): default "\r\n"
	 *   - @b newline (string): default "\r\n"
	 *   - @b word_wrap (integer): numero di caratteri di una riga (default 50)
	 * @return boolean
	 */
	public function sendMail($recipient_email, $sender_email, $subject, $contents, $options=array()) {

		$php_mail = array_key_exists('php_mail', $options) ? $options['php_mail'] : true;
		$mailer = array_key_exists('mailer', $options) ? $options['mailer'] : 'mail';
		
		// Intestazioni
		$sender_name = array_key_exists('sender_name', $options) ? $options['sender_name'] : '';
		$reply_email = array_key_exists('reply_email', $options) ? $options['reply_email'] : '';
		$reply_name = array_key_exists('reply_name', $options) ? $options['reply_name'] : '';
		$cc = array_key_exists('cc', $options) ? $options['cc'] : '';
		$ccn = array_key_exists('ccn', $options) ? $options['ccn'] : '';
		$notification = array_key_exists('notification', $options) ? $options['notification'] : '';
		
		// Allegati
		$attachments = (array_key_exists('attachments', $options) && is_array($options['attachments'])) ? $options['attachments'] : array();
		$embedded_images = (array_key_exists('embedded_images', $options) && is_array($options['embedded_images'])) ? $options['embedded_images'] : array();
		
		// Server smtp
		$smtp_name = array_key_exists('smtp_name', $options) ? $options['smtp_name'] : null;
		$smtp_secure = array_key_exists('smtp_secure', $options) ? $options['smtp_secure'] : null;
		$smtp_server = array_key_exists('smtp_server', $options) ? $options['smtp_server'] : '';
		$smtp_port = array_key_exists('smtp_port', $options) ? $options['smtp_port'] : 25;
		$smtp_auth = array_key_exists('smtp_auth', $options) ? $options['smtp_auth'] : false;
		$smtp_auth_type = array_key_exists('smtp_auth_type', $options) ? $options['smtp_auth_type'] : null;
		$smtp_user = array_key_exists('smtp_user', $options) ? $options['smtp_user'] : '';
		$smtp_password = array_key_exists('smtp_password', $options) ? $options['smtp_password'] : '';
		
		// Impostazioni Email
		$exception = array_key_exists('exception', $options) ? $options['exception'] : false;
		$debug = array_key_exists('debug', $options) ? $options['debug'] : 0;
		$ishtml = array_key_exists('ishtml', $options) ? $options['ishtml'] : true;
		$charset = array_key_exists('charset', $options) ? $options['charset'] : 'UTF-8';
		$priority = array_key_exists('priority', $options) ? $options['priority'] : 3;
		$view_mailer = array_key_exists('view_mailer', $options) ? $options['view_mailer'] : false;
		
		// Altro
		$automatic_html = array_key_exists('automatic_html', $options) ? $options['automatic_html'] : false;
		$alternative_text = array_key_exists('alternative_text', $options) ? $options['alternative_text'] : null;
		$crlf = array_key_exists('crlf', $options) && $options['crlf'] ? $options['crlf'] : "\r\n";
		$newline = array_key_exists('newline', $options) && $options['newline'] ? $options['newline'] : "\r\n";
		$word_wrap = array_key_exists('word_wrap', $options) && $options['word_wrap'] ? $options['word_wrap'] : 50;
		
		if($php_mail)
		{
			return $this->sendPhpMail($recipient_email, $subject, $contents, 
				array(
					'sender_email'=>$sender_email, 
					'sender_name'=>$sender_name, 
					'reply_email'=>$reply_email, 
					'reply_name'=>$reply_name, 
					'cc'=>$cc, 
					'charset'=>$charset, 
					'view_mailer'=>$view_mailer
				)
			);
		}
		
		$mail = new \PHPMailer($exception);
		
		if($mailer == 'mail') {
			$mail->isMail();
		}
		elseif($mailer == 'sendmail') {
			$mail->isSendmail();
		}
		elseif($mailer == 'qmail') {
			$mail->isQmail();
		}
		elseif($mailer == 'smtp') {
			$mail->IsSMTP();
		}
		else {
			return false;
		}
		
		$mail->SMTPDebug = $debug;
		$mail->Debugoutput = 'html';
		$mail->Priority = $priority;
		$mail->CharSet = $charset;
		
		// Set addresses
		$mail->SetFrom($sender_email, $sender_name);
		$mail->AddAddress($recipient_email);
		if($reply_email) $mail->AddReplyTo($reply_email, $reply_name);
		
		if($cc) $mail->AddCC($cc);		
		if($ccn) $mail->AddBCC($ccn);
		if($notification) $mail->ConfirmReadingTo = $notification;
		// End
		
		if($smtp_name)
		{
			$params = $this->setSmtpParams($smtp_name);
			
			if(count($params))
			{
				if(array_key_exists('smtp_auth', $params)) $smtp_auth = $params['smtp_auth'];
				if(array_key_exists('smtp_secure', $params)) $smtp_secure = $params['smtp_secure'];
				if(array_key_exists('smtp_server', $params)) $smtp_server = $params['smtp_server'];
				if(array_key_exists('smtp_port', $params)) $smtp_port = $params['smtp_port'];
			}
		}
		
		if($smtp_secure) $mail->SMTPSecure = $smtp_secure;
		if($smtp_server) $mail->Host = $smtp_server;
		if($smtp_port) $mail->Port = $smtp_port;
		
		if($smtp_auth)
		{
			$mail->SMTPAuth = $smtp_auth;
			$mail->Username = $smtp_user;
			$mail->Password = $smtp_password;
			
			if($smtp_auth_type) $mail->AuthType = $smtp_auth_type;
		}
		
		// Invio Email
		$mail->Subject = $subject;
		
		$mail->IsHTML($ishtml);
		$mail->Body = $contents;
		$mail->WordWrap = $word_wrap;
		
		if($ishtml) {
			
			if(!$alternative_text) {
				$alternative_text = 'To view this email message, open it in a program that understands HTML!';
			}
			$mail->AltBody = $alternative_text;
		}
		
		// Immagini inline
		if(count($embedded_images))
		{
			foreach($embedded_images AS $array) {
				if(array_key_exists('path', $array) && array_key_exists('cid', $array))
				{
					$tmp_name = array_key_exists('name', $array) && $array['name'] ? $array['name'] : '';
					
					$mail->addEmbeddedImage($array['path'], $array['cid'], $tmp_name);
				}
			}
		}
		
		// Allegati
		if(count($attachments))
		{
			foreach($attachments AS $array) {
				
				if(array_key_exists('path', $array))
				{
					$tmp_name = array_key_exists('name', $array) && $array['name'] ? $array['name'] : '';
					
					$mail->AddAttachment($array['path'], $tmp_name);
				}
			}
		}
		
		if($automatic_html) {
			$mail->msgHTML($contents);
		}

		if($mail->Send()) {
			return true;
		}
		else {
			if($debug != 0)
			{
				echo 'Message was not sent.';
				echo 'Mailer Error: '.$mail->ErrorInfo;
			}
			return false;
		}
	}
}
