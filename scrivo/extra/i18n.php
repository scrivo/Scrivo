<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: i18n.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

$i18n = array(

	"common.rights" => 				"Alle rechten voorbehouden.",
	"common.login" => 				"Login",
	"common.statistics" => 			"Statistieken",
	"common.bugreport" => 			"Bugrapportage",
	"common.openwindow" => 			"Open CMS Window (popup)",

	"label.email_usercode" => 		"E-mail/Usercode",
	"label.email" => 				"E-mailadres",
	"label.pwd" => 					"Wachtwoord",
	"label.pwdagain" => 			"Herhaal wachtwoord",
	"label.phone" =>				"Telefoon",
	"label.firstname" =>			"Naam",
	"label.surname" =>				"Achternaam",
	"label.prefix" =>				"Tussenvoegsel",

	"error.noaccount" => 			"Bij het door u opgegeven e-mailadres is geen gebruikeraccount gevonden.",
	"error.pwddiffer" => 			"De door u opgegeven wachtwoorden verschillen.",
	"error.pwdtoshort" => 			"Het wachtwoord moet uit minimaal 5 karakters bestaan.",
	"error.missingdata"	=>			"Een van de vereiste velden is niet ingevuld.",
	"error.accountexists" =>		"Er bestaat al een gebruikeraccount met het door u opgegeven e-mailadres.",
	"error.invalidemail" =>			"U heeft geen geldig e-mailadres ingevuld.",
	"error.invalidcaptcha" =>		"U heeft de beveiligingscode niet goed overgenomen",

	"button.login" => 				"Login",
	"button.newaccount" => 			"Vraag een gebruikeraccount aan",
	"button.newpassword" => 		"Vraag een nieuw wachtwoord aan",
	"button.verstuur" => 			"Verstuur",

	"login.title" => 				"Login CMS",
	"login.text.introduction" => 	"<p>Wachtwoord vergeten of wachtwoord wijzigen? Vraag hier een
									[LINKSTART.NEWPWD]nieuw wachtwoord[LINKEND] aan
									(alleen van toepassing voor gebruikers die met hun e-mail adres inloggen).</p>
									<p>Nog geen gebruikeraccount? U kunt hier een [LINKSTART.NEWACC]account aanvragen[LINKEND].</p>",

	"newpassword.title" => 			"Nieuw wachtwoord",
	"newpassword.text.introduction" => "<p>Hieronder kunt u een nieuw wachtwoord aanvragen. Uw nieuwe wachtwoord
									is niet direct actief. U ontvangt een e-mail met verificatielink
									waarmee u uw keuze kunt bevestigen.</p>",
	"newpassword.text.ok" => 		"<p>Er is een e-mail verstuurd naar '[email_address]' met een link
									waarmee u uw aanvraag voor een nieuw wachtwoord kunt bevestigen.",
	"newpassword.mail.subject" => 	"Bevestig uw wachtwoord voor Scrivo inloggen",
	"newpassword.mail.body" => 		"U heeft online uw Scrivo wachtwoord aangepast.\nKlik op deze link om dit te bevestigen.\n\n[MAIL.LINK]",
	"newpassword.text.confirmok" =>	"<p>Het door u opgegeven wachtwoord is geactiveerd.</p>
									<p>U kunt nu [LINKSTART.LOGIN]inloggen[LINKEND] met uw e-mailadres en wachtwoord.</p>",
	"newpassword.text.confirmerror"=> "<p>Er is een fout opgetreden tijdens het uitvoeren van de
									procedure. Mogelijke oorzaken zijn:<ul>
									<li>U heeft deze bevesting al eerder uitgevoerd. U kunt
									dan al inloggen met uw e-mailadres en de link in uw
									e-mail zal dan niet meer werken.</li>
									<li>Mogelijk is een ongeldige verficatie-sleutel doorgegeven.
									Dit kan gebeuren als u de link vanuit uw e-mailprogramma
									in uw browser kopieerd.</li></ul></p>",

	"newaccount.title" => 			"Nieuw gebruikeraccount",
	"newaccount.text.introduction" => "<p>Hieronder kunt u een gebruikeraccount aanvragen. Alle velden gemarkeerd
									met een asterisk (*) moeten daarvoor worden ingevuld. De beheerder van de
									site wordt van uw aanvraag op de hoogte gesteld en
									zal de aanvraag afhandelen. Uw aanvraag is dus niet
									direct actief.</p>",
	"newaccount.text.ok" => 		"<p>Bedankt voor uw aanmelding.</p><p>Uw aanvraag zal in behandeling worden genomen.</p>",
	"newaccount.mail.subject" => 	"Aanmelding van gebruiker voor Scrivo beheer",
	"newaccount.mail.body" => 		"Er is een aanvraag gedaan voor toegang tot het Scrivo beheer van\n[SITE] De aanvraag is gedaan door.\n",
	"newaccount.mail.mailto" => 	"requestaccount@scrivo.nl",

	"getemail.title" =>				"Verzoek inloggen met e-mailadres",
	"getemail.text.introduction" => "<p>In het vervolg dient u in te loggen met uw e-mailadres in plaats
									van uw usercode. In onderstaand formulier kunt u uw e-mail
									adres opgeven.</p>
									<p>Hierna ontvangt u een e-mail met een verificatielink
									om uw keuze te bevestigen.</p>",
	"getemail.text.ok" => 			"<p>Bedankt voor het doorgeven van uw e-mailadres.
									<p>Er is een e-mail verstuurd naar '[email_address]' met daarin een
									link om uw keuze te bevestigen. Nadat u dit heeft gedaan kunt
									u in het vervolg met uw e-mailadres en wachtwoord inloggen.",
	"getemail.mail.subject" => 		"Bevestig uw e-mailadres voor Scrivo inloggen",
	"getemail.mail.body" => 		"U heeft uw e-mailadres opgegeven om in het vervolg met uw e-mailadres\n".
									"en password in Scrivo in te kunnen loggen. Klik op deze link om\n".
									"dit te bevestigen:\n\n[MAIL.LINK]",
	"getemail.text.confirmok" =>	"<p>Bedankt voor het doorgeven van uw e-mailadres.</p>
									<p>U kunt nu [LINKSTART.LOGIN]inloggen[LINKEND] met uw e-mailadres en wachtwoord.</p>",
	"getemail.text.confirmerror" =>	"<p>Er is een fout opgetreden tijdens het uitvoeren van de
									procedure. Mogelijke oorzaken zijn:<ul>
									<li>U heeft deze bevesting al eerder uitgevoerd. U kunt
									dan al inloggen met uw e-mailadres; de link in uw
									e-mail zal dan niet meer werken.</li>
									<li>Het door u opgegeven e-mailadres is al in gebruik voor
									een ander gebruikeraccount. Eenzelfde e-mailadres kan niet
									worden gebruikt door meerdere Scrivo gebruikeraccounts.</li>
									<li>Mogelijk is een ongeldige verficatie-sleutel doorgegeven.
									Dit kan gebeuren als u de link vanuit uw e-mailprogramma
									in uw browser kopieert.</li></ul></p>",

	"bugreport.title" =>			"Scrivo Bug Report",
	"bugreport.thanks" =>			"Hartelijk dank voor uw melding",
	"bugreport.text.introduction" =>"<p>Help Scrivo te verbeteren. De beste manier om dat te doen is door fouten aan ons door te geven.</p>",
	"bugreport.text.sysinfo" =>		"Systeem Informatie",
	"bugreport.text.email" =>		"E-mail (optioneel)",
	"bugreport.text.errortype" =>	"Foutsoort<br>(optioneel)",
	"bugreport.text.jserror" =>		"JavaScript Error",
	"bugreport.text.jserrorinfo" =>	"Dit is een programmeerfout in het
									systeem. Als een dergelijke fout optreedt , kan een fout-dialoog
									worden getoond zoals hiernaast is weergegeven. Of dit gebeurt is
									afhankelijk van de Internet Explorer instellingen. Weet u niet
									zeker of het een JavaScript Error betreft, vul dan 'Andersoortig / Ik
									weet het niet' in. JavaScript Errors worden altijd op korte termijn
									door ons gecorrigeerd.",
	"bugreport.text.apperror" =>	"IE Application Error",
	"bugreport.text.apperrorinfo" =>"Dit is de meest zware fout die op kan
									treden. Dit is <i>geen</i> fout in de Scrivo programmatuur maar een
									fout in de Internet Explorer. Dit zijn lastige fouten omdat ze niet
									door ons zijn op te lossen in tegenstelling tot de hiervoor
									beschreven JavaScript Errors. Als u tegen een dergelijke fout op
									loopt probeer hieronder dan zo goed mogelijk te verwoorden wat je
									aan het doen was toen de fout optrad. Wij zullen dan proberen om hier een
									zogenaamde workarround voor te schrijven.",
	"bugreport.text.othererr" =>	"Andersoortig / Ik weet het niet",
	"bugreport.text.description" =>	"Omschrijving van de fout / Wat u aan het doen was toen de fout optrad",
	"bugreport.button.send" =>		"Verstuur",

	"captcha.infotext" =>			"Typ de onderstaande beveiligingscode over, of als deze	onduidelijk is
									[LINKSTART.NEWCAPTCHA]vraag nieuwe code aan[LINKEND]. Ook is de beveilingscode
									te downloaden als [LINKSTART.AUDIOCAPTCHA]audiobestand[LINKEND].",

	"end" => ""
);

$parts = explode("/", $_SERVER["SCRIPT_FILENAME"]);
for ($i = 0; $i < 3; $i++) {
	array_pop($parts);
}
$pth = implode("/",$parts)."/login_text.php";
if (file_exists($pth)) {
	include $pth;
}

?>