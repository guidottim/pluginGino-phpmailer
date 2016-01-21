pluginGino-phpmailer
====================

Plugin per [gino CMS](https://github.com/otto-torino/gino) per l'invio di email con la libreria PHPMailer. In particolare PHPMailer gestisce l'invio di email in formato html e con autenticazione smtp.   
Il sito ufficiale della libreria PHPMailer è http://phpmailer.worxware.com/.

È richiesta una versione di **gino >= 2.0.0**.

# Installazione

* Scaricare la libreria dal sito del progetto
* Decomprimere il file del progetto nella directory lib di gino e rinominare la directory PHPMailer, ad esempio:

	mv PHPMailer_master PHPMailer

Il file PHPMailer/class.phpmailer.php deve essere caricato una sola volta nello script (poi si potranno inviare infinite email).

# Utilizzo

Includere nel metodo che si occupa dell'invio delle email il file plugin.phpmailer.php:

	require_once(PLUGIN_DIR.OS.'plugin.phpmailer.php');

# File

* plugin.phpmailer.php, interfaccia alla libreria PHPMailer
* mailing.php, template di esempio per la costruzione di una email html

# Links

Si prega di segnalare bug, errori e consigli alla pagina del progetto su github: http://github.com/guidottim/pluginGino-phpmailer.

La documentazione relativa alla libreria si può trovare all'indirizzo <a href="http://gino.otto.to.it/page/view/plugin" target="_blank">http://gino.otto.to.it/page/view/plugin</a>.

# Copyright
Copyright © 2005-2016 [Otto srl](http://www.otto.to.it), [MIT License](http://opensource.org/licenses/MIT)