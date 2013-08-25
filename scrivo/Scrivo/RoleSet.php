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
 * $Id: RoleSet.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\RoleSet class.
 */

namespace Scrivo;

/**
 * A role set is a utilitity object to determine the if a specific item (page,
 * list or asset) is readable. The role ids of the roles assigned to that item
 * are stored in an array-like structure. The role set provides the methods
 * RoleSet::canRead(\Scrivo\User $user) and
 * RoleSet::checkReadPermission(\Scrivo\User $user) that can be used to check
 * if the specific item is readable.
 */
class RoleSet implements \ArrayAccess {

	/**
	 * An array of role ids.
	 * @var int[]
	 */
	private $roleIds = array();

	/**
	 * Construct a role set object.
	 */
	public function __construct() {
		$this->roleIds = array();
	}

	/**
	 * Add a role id to the role set.
	 *
	 * @param int $offset Not applicable.
	 * @param int $value A role id to set in the array.
	 */
	public function offsetSet($offset, $value) {
		$this->roleIds[$value] = $value;
	}

	/**
	 * Check if a role id is set at the given index position.
	 *
	 * @param int $index The index position for which to check.
	 *
	 * @return boolean True if a role id was set at that given index postition
	 *    false if not.
	 */
	public function offsetExists($index) {
		return isset($this->roleIds[$index]);
	}

	/**
	 * Illegal method, necessary for the implementation of the ArrayAccess
	 * interface.
	 *
	 * @param int $offset Not applicable.
	 */
	public function offsetUnset($offset) {
		throw new \Scrivo\SystemException("Illegal method");
	}

	/**
	 * Get the role id at the given index position.
	 *
	 * @param int $offset The index position for which to get the role id.
	 *
	 * @return int The role id at the given index postion, null if the index
	 *    postion was invalid.
	 */
	public function offsetGet($offset) {
		return isset($this->roleIds[$offset]) ? $this->roleIds[$offset] : null;
	}

	/**
	 * Test of the given user has read access according to this role set
	 * object.
	 *
	 * @param \Scrivo\User $user The user for which to test read access.
	 *
	 * @return boolean True if the user has read access, false if not.
	 */
	public function canRead(\Scrivo\User $user) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		if ($user->status <= \Scrivo\User::STATUS_EDITOR) {
			return true;
		}
		$rls = array();
		foreach ($user->roles as $role) {
			if ($role->type == \Scrivo\Role::PUBLIC_ROLE) {
				$rls[] = $role->id;
			}
		}
		return count(array_intersect($rls, $this->roleIds)) != 0;
	}

	/**
	 * Test of the given user has read access according to this role set
	 * object.
	 *
	 * @param \Scrivo\User $user The user for which to test read access.
	 *
	 * @throws \Scrivo\ApplicationException if no access was granted.
	 */
	public function checkReadPermission(\Scrivo\User $user) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		if (!$this->canRead($user)) {
			throw new \Scrivo\ApplicationException("Access violation");
		}
	}

	/**
	 * Test of the given user has write access according to this role set
	 * object.
	 *
	 * @param \Scrivo\User $user The user for which to test write access.
	 *
	 * @return boolean True if the user has write access, false if not.
	 */
	public function canWrite(\Scrivo\User $user) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		if ($user->status < \Scrivo\User::STATUS_ADMIN) {
			return true;
		}
		$rls = array();
		foreach ($user->roles as $role) {
			if ($role->type == \Scrivo\Role::EDITOR_ROLE) {
				$rls[] = $role->id;
			}
		}
		return count(array_intersect($rls, $this->roleIds)) != 0;
	}

}

?>