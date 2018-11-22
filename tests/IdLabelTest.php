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
 * $Id: IdLabelTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\IdLabel
 */
class IdLabelTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for a test into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml"));
	}

	/**
	 * Tests for creating, updating and deleting an id-label pair.
	 */
	public function testCrud() {

		// Instantiate an IdLabel object: note that this is only necessary
		// when you want a "fresh" object, for instance after updating an id-
		// label pair. Normally you would use the one provided by the context.
		$idLabel = \Scrivo\IdLabel::select(self::$context);

		// Set an id-label pair
		$idLabel->set(self::$context, 123, new \Scrivo\Str("CONTACT"));

		// Reload labels and check
		$idLabel = \Scrivo\IdLabel::select(self::$context);
		$this->assertEquals($idLabel->CONTACT, 123);

		// Update an id-label pair
		$idLabel->set(self::$context, 123, new \Scrivo\Str("CONTACT_B"));

		// Reload labels and check
		$idLabel = \Scrivo\IdLabel::select(self::$context);
		$this->assertEquals($idLabel->CONTACT_B, 123);

		// Delete an id-label pair
		$idLabel->set(self::$context, 123);

		// Reload labels and check
		$idLabel = \Scrivo\IdLabel::select(self::$context);
		$this->assertFalse(isset($idLabel->CONTACT_B));
	}

	/**
	 * Creating a duplicate label should generate an error.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 */
	public function testDuplicate() {

		$idLabel = \Scrivo\IdLabel::select(self::$context);

		// Set an id-label pair
		self::$context->labels->set(
			self::$context, 123, new \Scrivo\Str("CONTACT"));

		// Set another using the same label
		self::$context->labels->set(
			self::$context, 1234, new \Scrivo\Str("CONTACT"));
	}

	/**
	 * Requesting a nonexistent label should generate an error.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidLabel() {
		// Get an invalid label
		$dummy = self::$context->labels->HANDY_DANDY_LABEL;
	}

	/**
	 * Test iterator functionality by iterating through the labels.
	 */
	public function testIterator() {
		$count = 0;
		foreach (self::$context->labels as $key => $value) {
			$this->assertEquals(self::$context->labels->$key, $value);
			$count++;
		}
		$this->assertEquals(1, $count);
	}

	/**
	 * Check if the permission function throw an exception if the database
	 * fails.
	 */
	public function testDbFailure() {

		$idLabel1 = \Scrivo\IdLabel::select(self::$context);
		// invalidate cache
		$idLabel1->set(self::$context, 123, new \Scrivo\Str("INV_CACHE"));

		// Perform idLable construction.
		$test = "";
		try {
			$idLabel2 = \Scrivo\IdLabel::select($this->ctxDbFailureStub());
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform set operation.
		$test = "";
		try {
			$idLabel1->set(
				$this->ctxDbFailureStub(), 123, new \Scrivo\Str("CONTACT"));
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}
}

?>