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
 * $Id: ResourceExceptionTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\ResourceException
 */
class ResourceExceptionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test if the thing is of the correct type and throwable.
	 *
	 * @expectedException \Scrivo\ResourceException
	 */
	public function testApplicationException() {

		$re = new \Scrivo\ResourceException();

		$this->assertInstanceOf('Exception', $re);

		throw $re;
	}

	/**
	 * Test exception wrapping if the \Scrivo\ResourceException was derived
	 * from another exception.
	 */
	public function testExceptionWrapping() {

		$re = new \PDOException("A failing db", 666);
		$eTest = null;

		try {
			throw $re;
		} catch (\PDOException $e) {
			try {
				throw new \Scrivo\ResourceException($e);
			} catch (\Scrivo\ResourceException $e2) {
				$eTest = $e2;
			}
		}

		$this->assertNotNull($eTest);
		$this->assertEquals($re, $eTest->getPrevious());
		$this->assertEquals("A failing db" , $eTest->getMessage());
		$this->assertEquals(666, $eTest->getCode());

	}

	/**
	 * Test exception wrapping if the \Scrivo\ResourceException was derived
	 * from another exception using standard exception parameters.
	 */
	public function testExceptionWrappingUsingStandardParameters() {

		$re = new \PDOException("A failing db", 666);
		$eTest = null;

		try {
			throw $re;
		} catch (\PDOException $e) {
			try {
				throw new \Scrivo\ResourceException("New message", 123, $e);
			} catch (\Scrivo\ResourceException $e2) {
				$eTest = $e2;
			}
		}

		$this->assertNotNull($eTest);
		$this->assertEquals($re, $eTest->getPrevious());
		$this->assertEquals("New message" , $eTest->getMessage());
		$this->assertEquals(123, $eTest->getCode());

	}

}

?>