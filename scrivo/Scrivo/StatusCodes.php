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
 * $Id: StatusCodes.php 629 2013-05-20 23:02:09Z geert $
 */

/**
 * Implementation of the \Scrivo\StatusCodes interface.
 */

namespace Scrivo;

/**
 * The StatusCodes interface just defines a number of status codes that
 * can be returned from Scrivo exceptions.
 */
interface StatusCodes {

	/**
	 * Constant to indicate that a choosen user code is too short.
	 */
	const USER_CODE_TOO_SHORT = 1;

	/**
	 * Constant to indicate that a user code is already in use.
	 */
	const USER_CODE_IN_USE = 2;

	/**
	 * Constant to indicate deletion of system users.
	 */
	const CANNOT_DELETE_SYSTEM_USERS = 21;

	/**
	 * Constant to indicate creation of duplicate labels.
	 */
	const LABEL_NOT_UNIQUE = 22;

	/*
	"E00001" => "Een usercode moet uit minimaal drie karakters bestaan",
	"E00002" => "Usercode is al in gebruik",
	"E00003" => "Selectie is mislukt, er werd 1 record verwacht, maar er zijn er geen of meer gevonden",
	"E00004" => "Een pagina kan niet onder zichzelf worden verplaatst",
	"E00005" => "De pagina kan niet worden verwijderd omdat er nog lijst pagina's onder vallen",
	"E00006" => "De pagina kan niet worden verwijderd omdat er nog subpagina's onder vallen",
	"E00007" => "De pagina kan niet worden verwijderd omdat deze is gemarkeerd als 'alleen lezen'",
	"E00008" => "De pagina kan niet worden gewijzigd omdat deze is gemarkeerd  als 'alleen lezen'",
	"E00009" => "De pagina kan niet worden gewijzigd/verwijderd, er is geen geldige bovenliggend pagina",
	"E00010" => "De pagina kan niet worden gewijzigd/verwijderd, de bovenliggend pagina is gemarkeerd als 'alleen lezen'",
	"E00011" => "De pagina kan niet worden verplaatst, de bovenliggend pagina is gemarkeerd als 'alleen lezen'",
	"E00012" => "Kan bestand niet hernoemen, de naam is al in gebruik",
	"E00013" => "De map kan niet worden verwijderd omdat deze niet leeg is",
	"E00014" => "Een map kan niet onder zichzelf worden gehangen",
	"E00015" => "De map(pen) of bestand(en) kunnen niet naar dezelfde map worden verplaats",
	"E00016" => "Het bestand is hernoemd omdat de naam niet uniek was",
	"E00017" => "De pagina kan niet worden verwijderd omdat deze een speciale functie heeft in de menustructuur",
	"E00018" => "Dit type pagina kan niet onder de gekozen pagina worden geplaatst",
	"E00019" => "Niet geinitialiseerd staging item",
	"E00020" => "Het item kan niet worden verwijderd omdat er nog item's onder vallen",
	"E00021" => "Systeemgebruikers kunnen niet worden verwijderd",
	"E00022" => "Label niet uniek",
*/

}
