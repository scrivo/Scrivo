<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: LoginKey.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\LoginKey class.
 */

namespace Scrivo;

/**
 * Add a login_events record with a temporary key that can
 * be used to log in. The following fields should be populated:
 *
 * 	instance_id           Instance id
 * 	date_login   Time of the login event (for timeout)
 * 	user_id           User id
 * 	return_code       0: OK, 1: invalid pwd, 2: invalid ucode
 * 	remote_address       Client IP-address
 * 	access_key        Random key
 *
 * Login return codes:
 * 	0: valid login credentials
 * 	0: login failed, invalid key
 *
 * An alternative function can defined in the .htscrivo file
 * (LOGIN_PROVIDER refers to a php script that implements the
 * function 'login_key()'. This is usefull if you want to use
 * another user database to log in (LDAP). You still need to
 * match roles on both systems, but you can write a function
 * that does the authentication on the external system and if
 * authenticated simply insert a row in the login_events table.
 * Note that you still need a user in the Scrivo database,
 * but that is only functional: a user id is used to retreive
 * the roles. Simply insert a new user on login and link it
 * with the corresponding roles.
 */

class LoginKey {

	/**
	 * Value to indicate that login was unsuccesfull due to an invalid password.
	 */
	const INVALID_PASSWORD = 1;

	/**
	 * Value to indicate that login was unsuccesfull due to an invalid usercode.
	 */
	const INVALID_USERCODE = 2;

	/**
	 * Value to indicate that login was succesfull.
	 */
	const LOGIN_SUCCESSFULL = 3;

	/**
	 * Value to indicate that login was unsuccesfull because login key
	 * generation and verification action were initiated using a different ip
	 * addresses.
	 */
	const INVALID_IP = 4;

	/**
	 * Value to indicate that login was unsuccesfull because of a time out
	 * when verifying the key.
	 */
	const TIMEOUT = 5;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	protected $context = null;

	/**
	 * Create a login object to either generate or verify a login key.
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context) {
		$this->context = $context;
	}

	/**
	 * Generate a login key for a given usercode and password. Override this
	 * function if you want to verify the user against another autentication
	 * database.
	 *
	 * @param \Scrivo\Str $usercode A user code.
	 * @param \Scrivo\Str $password A password.
	 * @return \Scrivo\Str The login key if usercode and password
	 *    verification was successfull, or NULL if not.
	 */
	public function generate(
			\Scrivo\Str $usercode, \Scrivo\Str $password) {

		$pwd = false;
		$usr = false;

		$sth = $this->context->connection->prepare(
			"SELECT * FROM user where instance_id = :instId and user_code = :uCode");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":uCode", $usercode, \PDO::PARAM_STR);

		$sth->execute();

		if ($sth->rowCount() == 1) {
			$usr = true;
			$user = $sth->fetch(\PDO::FETCH_ASSOC);
			$pwd =  $user["password"] == crypt($password, $user["password"]);
		}

		$lres = 0;
		if (!$usr) {
			$lres = self::INVALID_USERCODE;
		} else if (!$pwd) {
			$lres = self::INVALID_PASSWORD;
		}

		$k = new \Scrivo\Str(md5(microtime()));

		$this->insert(
			$lres == self::INVALID_USERCODE ? -1 : intval($user["user_id"]),
			$lres, $k);

		return $lres == 0 ? $k : null;
	}

	/**
	 * Insert an entry in the login_events table, this entry will be checked
	 * by LoginKey::verify().
	 *
	 * @param int $userId A user id if password verification was succesfull,
	 *   -1 if not.
	 * @param int $loginStatus The result of the password verification: either
	 *   \Scrivo\LoginKey::INVALID_USERCODE, \Scrivo\LoginKey::INVALID_PASSWORD
	 *   or 0 (no error).
	 * @param \Scrivo\Str $key A generated random key.
	 */
	public function insert($userId, $loginStatus, \Scrivo\Str $key) {

		$sth = $this->context->connection->prepare(
			"INSERT INTO login_events (
				instance_id, date_login, user_id, user_satus,
				return_code, remote_address, access_key
			) VALUES (
				:instId, NOW(), :userId, 0,
				:returnCode, :remoteAddr, :key
			)");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":userId", $userId, \PDO::PARAM_INT);
		$sth->bindValue(":returnCode", $loginStatus, \PDO::PARAM_INT);
		$sth->bindValue(
			":remoteAddr", $_SERVER["REMOTE_ADDR"], \PDO::PARAM_STR);
		$sth->bindValue(":key", $key, \PDO::PARAM_STR);

		$sth->execute();
	}

	/**
	 * Verify the login key, return the user if succesfull, NULL if not. Also
	 * update the login_events table with the result of the verification.
	 *
	 * @param \Scrivo\Str $key A key that was previously generated by
	 *     LoginKey::generate()
	 * @return \Scrivo\User|NULL A Scrivo user if verification was succesfull,
	 *     NULL if not.
	 */
	public function verify(\Scrivo\Str $key) {

		$addr = $_SERVER["REMOTE_ADDR"];

		$sth = $this->context->connection->prepare(
			"SELECT
				UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(date_login) TP,
				user_id, user_satus, return_code, remote_address
			FROM login_events
			WHERE instance_id = :instId AND access_key = :key"
		);

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":key", $key, \PDO::PARAM_STR);

		$sth->execute();

		$status = 0;

		if ($sth->rowCount() == 1) {

			$le = $sth->fetch(\PDO::FETCH_ASSOC);

			$status = self::LOGIN_SUCCESSFULL;
			$r = intval($le["return_code"]);

			if ($r == self::INVALID_USERCODE || $r == self::INVALID_PASSWORD) {
				$status = $r;
			} else if ($le["remote_address"] != $addr) {
				$status = self::INVALID_IP;
			} else if ($le["TP"] > 10) {
				$status = self::TIMEOUT;
			}

			if ($status == self::LOGIN_SUCCESSFULL ||
					$status == self::INVALID_IP || $status == self::TIMEOUT) {

				$sth = $this->context->connection->prepare(
					"UPDATE login_events SET return_code = :ret
						WHERE instance_id = :instId AND access_key = :key");

				$this->context->connection->bindInstance($sth);
				$sth->bindValue(":ret", $status, \PDO::PARAM_INT);
				$sth->bindValue(":key", $key, \PDO::PARAM_STR);

				$sth->execute();

			}

			if ($status == self::LOGIN_SUCCESSFULL) {

				return \Scrivo\User::fetch($this->context, intval($le["user_id"]));

			}

		}

		return null;
	}

}

?>