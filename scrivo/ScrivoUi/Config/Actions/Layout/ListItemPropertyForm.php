<?php
/* Copyright (c) 2012, Geert Bergman (geert@scrivo.nl)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "Scrivo" nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * $Id: ListItemPropertyForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * \ScrivoUi\Config\Actions\Layout\ListItemPropertyForm action class.
 */

namespace ScrivoUi\Config\Actions\Layout;

use \Scrivo\LayoutAction;
use \Scrivo\I18n;
use \Scrivo\Request;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\String;

/**
 * The ListItemPropertyForm layout action class sets up the layout for a list
 * item property form.
 */
class ListItemPropertyForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		if (isset($this->session->errorCode)) {
			$listItemProperty = unserialize($this->session->formData);
			$applicationId = $listItemProperty->applicationDefinitionId;
		} else {
			$listItemPropertyId = Request::get(
				"list_item_property_id", Request::TYPE_INTEGER, 0);
			if ($listItemPropertyId) {
				$listItemProperty = ListItemPropertyDefinition::fetch(
					$this->context, $listItemPropertyId);
				$applicationId = $listItemProperty->applicationDefinitionId;
			} else {
				$listItemProperty =
					new ListItemPropertyDefinition($this->context);
				$applicationId = Request::get(
					"application_definition_id", Request::TYPE_INTEGER, 0);
			}
		}

		$title = $listItemProperty->id
			? $i18n["Edit list item property"]
			: $i18n["Create new list item property"];

		$helpTexts = array(

		ListItemPropertyDefinition::TYPE_INPUT =>
		$i18n["\nWIDTH".
			"\tWidth of the input box in pixels, number".
			"\tWIDTH=100".
			"\nMAXLENGTH".
			"\tMaximum number of characters that will be accepted".
			"\tMAXLENGTH=50".
			"\n"],
		/*$i18n["\nWIDTH".
			"\tBreedte in pixels van de input box, getal".
			"\tWIDTH=100".
			"\nMAXLENGTH".
			"\tMaximaal aantal karkaters dat wordt geaccepteerd".
			"\tMAXLENGTH=50".
			"\n"],*/

		ListItemPropertyDefinition::TYPE_INFO =>
		$i18n["\nThe info property provides you with a possibility to add ".
			"informative texts in the dialog window. Just enter the text in ".
			"the  \"type data\" field. The text can contain HTML."],
		/*$i18n["\nHet info data type is een mogelijkheid om eigen tekst in ".
		  "het dialoogvenster te zetten. Alleen het \"type data\" veld hoeft ".
		  "hier ingevuld te worden. Het veld kan HTML bevatten."],*/

		ListItemPropertyDefinition::TYPE_HTML_TEXT =>
		$i18n["\nHEIGHT".
			"\tHeight if the text field in pixels".
			"\tHEIGHT=100".
			"\n"],
		/*$i18n["\nHEIGHT".
			"\tHoogte van het textvak in pixels".
			"\tHEIGHT=100".
			"\n"],*/

		ListItemPropertyDefinition::TYPE_TEXT =>
		$i18n["\nROWS".
			"\tHeight if the text field in rows".
			"\tROWS=5".
			"\n"],
		/*$i18n["\nROWS".
			"\tHoogte van het textvak in regels".
			"\tROWS=5".
			"\n"],*/

		ListItemPropertyDefinition::TYPE_IMAGE =>
		$i18n["\nWIDTH".
			"\tWidth in pixels of the image in the CMS dialog window, number".
			"\tWIDTH=100".
			"\nHEIGHT".
			"\tHeight in pixels of the image in the CMS dialog window, number".
			"\nTEMPLATE_WIDTH".
			"\tSuggested width in pixels when cropping the image, number".
			"\nTEMPLATE_HEIGHT".
			"\tSuggested height in pixels when cropping the image, number".
			"\n"],
		/*$i18n[
			"\nWIDTH".
			"\tBreedte in pixels van het plaatje in cms dialoogvenster, getal".
			"\tWIDTH=100".
			"\nHEIGHT".
			"\tHoogte in pixels van het plaatje in cms dialoogvenster, getal".
			"\nTEMPLATE_WIDTH".
			"\tVoorgestelde breedte voor het croppen van de afbeelding, getal".
			"\nTEMPLATE_HEIGHT".
			"\tVoorgestelde hoogte voor het croppen van de afbeelding, getal".
			"\n"],*/

		ListItemPropertyDefinition::TYPE_DATE_TIME =>
		$i18n["\nDEFAULT_VALUE".
			"\tThe 'default' DEFAULT_VALUE is the current date. You can enter ".
			"NULL if you don't want a default date set. Else you can enter ".
			"any by PHP:strtotime parseble value as DEFAULT_VALUE.".
			"\tDEFAULT_VALUE=NULL".
			"\tDEFAULT_VALUE=-2 days".
			"\n"],
		/*$i18n[
			"\nDEFAULT_VALUE".
			"\tDe 'default' DEFAULT_VALUE is de huidige datum. NULL kan je ".
			"opgeven als je geen default geen waarde ingevuld wilt. Daarnaast ".
			"kan je elke door PHP:strtotime parsebare waarde als ".
			"DEFAULT_VALUE opgeven.".
			"\tDEFAULT_VALUE=NULL".
			"\tDEFAULT_VALUE=-2 days".
			"\n"],*/

		ListItemPropertyDefinition::TYPE_SELECT =>
		$i18n["\nDATA".
			"\tList with items to display in the select list. These are ".
			"value/text pairs separated by a semicolon. Each value/text pair ".
			"is on its turn separated by a colon.".
			"\tDATA=M:male;F:Female".
			"\nTYPE".
			"\tSelect list type".
			"\tTYPE=MULTIPLE".
			"\nSIZE".
			"\tSize of the (multiple) select list in rows".
			"\tSIZE=4".
			"\n"],
		/*$i18n[
			"\nDATA".
			"\tLijst met items die worden getoond in de select list. ".
			"Waarde-tekst paren gescheiden door een punt-komma. Elk ".
			"waarde-tekst paar is weer gescheiden door een dubbele punt.".
			"\tDATA=M:male;F:Female".
			"\nTYPE".
			"\tType van de select list".
			"\tTYPE=MULTIPLE".
			"\nSIZE".
			"\tGrootte van de (multiple) select list".
			"\tSIZE=4".
			"\n"],*/

		"col" =>
		$i18n["Settings for the property in column layout".
			"\nCOL_WIDTH".
			"\tcolumn width, number (defaults to 100)".
			"\tCOL_WIDTH=125".
			"\nCOL_ALIGN".
			"\tAlingment of the column. left-center-right (defaults to 'left')".
			"\tCOL_ALIGN=center".
			"\nCOL_IS_NUMERIC".
			"\tTo indicate that column contains a numerical value so that ".
			"these columns will be sorted correctly, true-false (defaults ".
			"to false)".
			"\tCOL_IS_NUMERIC=true".
			"\n"],
		/*$i18n["Instellingen voor velden in kolom lay out".
			"\nCOL_WIDTH".
			"\tkolombreedte, getal (default waarde is 100)".
			"\tCOL_WIDTH=125".
			"\nCOL_ALIGN".
			"\tUitlijning van de kolom. left-center-right (default waarde ".
			"is 'left')".
			"\tCOL_ALIGN=center".
			"\nCOL_IS_NUMERIC".
			"\tGeef aan dat de waarde nummeriek is, zodat kolommen met ".
			"getallen goed worden gesorteerd, true-false (default waarde "
			"is false)".
			"\tCOL_IS_NUMERIC=true".
			"\n"],*/
		);

		$tr = "../ScrivoUi/Config/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/list_item_property_form.tpl.php";
		$this->useLayout("{$tr}/master.tpl.php");

		$this->setResult(self::SUCCESS);

	}

	private function formatHelp($text) {

		$str = new String($text);
		$txts = $str->split(new String("\n"));
		if (count($txts)) {
			if (!$txts[0]->equals(new String(""))) {
				echo "<p>".$txts[0]."</p>";
			}
			for ($i=1; $i<count($txts); $i++) {
				$items = $txts[$i]->split(new String("\t"));
				echo "<dl>";
				if (count($items)) {
					echo "<dt>".$items[0]."</dt>";
					echo "<dd>";
					for ($j=1; $j<count($items); $j++) {
						echo $items[$j]."<br>";
					}
					echo "</dd>";
				}
				echo "</dl>";
			}
		}
	}

	private function typeDataAsString($data) {
		$d = array();
		foreach($data as $k=>$v) {
			$d[] = $k."=".$v;
		};
		return new String(implode("\n", $d));
	}


}

?>