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
 * $Id: ResourceException.php 711 2013-07-04 12:05:36Z geert $
 */

/**
 * Implementation of the \Scrivo\ResourceException class.
 */

namespace Scrivo;

/**
 * Class to represent an error raised due failure of a server resource.
 *
 * PHP scripts typically run on a server where the make use of several
 * server resources, for instance:
 *
 * * a database connection
 * * the file system
 * * executable commands
 *
 * Not to be able to use these resources will generally mean the termination
 * of the script, as errors in the program logic do. You can use these errors
 * to discriminate between resource exceptions, program logic exceptions and
 * application exceptions to take appropriate action: f.i. notify the system
 * admin, send out bug report or prompt the user for new input.
 */
 class ResourceException extends \Exception {

	/**
	 * Construct a \Scrivo\ResourceException. It is possible to create a
	 * \Scrivo\ResourceException based upon an existing exception:
	 *
	 *         ....
	 *     } catch (\PdoException $e) {
	 *         trhow \Scrivo\ResourceException($e);
	 *     }
	 *
	 * or use the standard exception parameters:
	 *
	 *         ....
	 *     } catch (\PdoException $e) {
	 *         trhow \Scrivo\ResourceException("Message", 123, $e);
	 *     }
	 *
	 * @param \Exception|string $messageOrException The error message or
	 *    original exception.
	 * @param int $code Optional exception code if the first parameter was
	 *    an error string, else not applicable.
	 * @param \Exception $previous Optional, the original exception if the
	 *    first parameter was an error string, else not applicable.
	 */
	public function __construct($messageOrException = null , $code = null,
			\Exception $previous = null) {
		if ($messageOrException instanceof \Exception) {
			parent::__construct($messageOrException->message,
				0, $messageOrException);
			$this->code = $messageOrException->code;
			$this->file = $messageOrException->file;
			$this->line = $messageOrException->line;
		} else {
			parent::__construct($messageOrException, $code, $previous);
		}
	}

}

?>