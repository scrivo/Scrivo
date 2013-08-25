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
 * $Id: DownloadableTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\Downloadable
 */
class DownloadableTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test a downloadable based on variable data.
	 */
	public function testDownloadable() {

		// Set up a Scrivo context.
		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, Scrivo\User::PRIMARY_ADMIN_ID);

		// The output filename.
		$filename = new \Scrivo\String("afile2.dat");

		// Create a downloadble based on a physical file.
		$dwnl = new \Scrivo\Downloadable($context, $filename,
			\Scrivo\Downloadable::type_data, "Lots of data");

		$this->assertTrue($dwnl->getFileName()->contains($filename));
		$this->assertFalse($dwnl->getFileName()->equals($filename));

		// Test output
		$this->expectOutputString("Lots of data");
		$dwnl->outputData();
	}

	/**
	 * Test a downloadable based on file data.
	 */
	public function testDownloadableFile() {

		// Set up a Scrivo context.
		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, Scrivo\User::PRIMARY_ADMIN_ID);

		// Create a file to download/output.
		$file = tempnam(sys_get_temp_dir(), "PHPUnit_Scrivo_DownloadableTest");
		file_put_contents($file, "Muchos datos");

		// The output filename.
		$filename = new \Scrivo\String("afile2.dat");

		// Create a downloadble based on a physical file.
		$dwnl = new \Scrivo\Downloadable($context, $filename,
			\Scrivo\Downloadable::TYPE_FILE, new \Scrivo\String($file));

		$this->assertTrue($dwnl->getFileName()->contains($filename));
		$this->assertFalse($dwnl->getFileName()->equals($filename));

		// Test output.
		$this->expectOutputString("Muchos datos");
		$dwnl->outputData();

		// The file should be erased after downloading.
		if (file_exists($file)) {
			$this->fail("File not removed after reading/downloading");
		}
	}

	/**
	 * Test illegal argument.
	 * @expectedException \Scrivo\SystemException
	 */
	public function testIllegalArgument() {

		// Set up a Scrivo context.
		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, Scrivo\User::PRIMARY_ADMIN_ID);

		// The output filename.
		$filename = new \Scrivo\String("afile2.dat");

		$dwnl = new \Scrivo\Downloadable($context, $filename,
			\Scrivo\Downloadable::TYPE_FILE, "blahblah");
	}
}

?>