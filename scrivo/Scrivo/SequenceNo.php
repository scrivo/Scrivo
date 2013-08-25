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
 * $Id: SequenceNo.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\SequenceNo class.
 */

namespace Scrivo;

/**
 * Class to manage the sequence (or order) numbers of various Scrivo entities.
 * Most Scrivo entitites like pages and lists items can be ordered manually.
 * This is done by setting a sequence number field in the database for say
 * all pages with a common parent.
 *
 * Note that this ordering has nothing to do with database sequence generation
 * used for generating ids.
 */
class SequenceNo {

	/**
	 * Constant to indicate that we want the entity to move one position up.
	 */
	const MOVE_UP = -1;

	/**
	 * Constant to indicate that we want the entity to move one position down.
	 */
	const MOVE_DOWN = -2;

	/**
	 * Constant to indicate that we want to move the entity to the beginning.
	 */
	const MOVE_FIRST = -3;

	/**
	 * Constant to indicate that we want to move the entity to the end.
	 */
	const MOVE_LAST = -4;

	/**
	 * Regenerate all sequence numbers for a set of entities determined by
	 * the parent of the entity with the given $id. The new sequence numbers
	 * will start with and will have an offset of 2. Sequence numbers with
	 * a value of zero or less will not be updated.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param string $tableName The table name for the entity to resequence.
	 * @param string $parentField The parent id field in the table for the
	 *     entity to resequence.
	 * @param int $id The id of the entity to resequence.
	 */
	private static function resequence(
			$context, $tableName, $parentField, $id) {

		$pJoin = "";
		if (!is_array($parentField)) {
			$parentField = array($parentField);
		}
		foreach ($parentField as $pf) {
			$pJoin .= str_replace("[P]", $pf, " AND D1.[P] = D2.[P]");
		}

		$sth = $context->connection->prepare(
			str_replace("[T]", $tableName,
			"UPDATE [T] T, (
				SELECT @n:=@n+2 NEW_SEQ, D1.instance_id, D1.[T]_id FROM
					(SELECT @n:=0) x, [T] D1, [T] D2 WHERE
					D1.instance_id = :instId AND D2.instance_id = :instId
					{$pJoin} AND D2.[T]_id = :id AND
					D1.sequence_no > 0 ORDER BY D1.sequence_no) R
			SET T.sequence_no = R.NEW_SEQ
			WHERE (T.instance_id = R.instance_id AND T.[T]_id = R.[T]_id)"));

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		$sth->execute();
	}

	/**
	 * Move an entity to a given position. Note that this is a postion number
	 * starting at 1, not a zero based index. Instead of an actual position
	 * number also one of \Scrivo\SequenceNo::MOVE_* constants can be used.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param string $tableName The table name for the entity to resequence.
	 * @param string $parentField The parent id field in the table for the
	 *    entity to resequence.
	 * @param int $id The id of the entity to resequence.
	 * @param int $pos The new position of the entity to resequence, note that
	 *    this is a postion number starting at 1, not a zero based index.
	 */
	public static function position(
			\Scrivo\Context$context, $tableName, $parentField, $id, $pos) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null, null, null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {

			self::resequence($context, $tableName, $parentField, $id);

			$sth = $context->connection->prepare(
				str_replace("[T]", $tableName,
				"SELECT sequence_no FROM [T] WHERE
					instance_id = :instId AND [T]_id = :id"));

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			$seqNo = $sth->fetchColumn(0);

			switch ($pos) {
			case self::MOVE_UP:
				if ($seqNo > 2) {
					$seqNo -= 3;
				}
				break;
			case self::MOVE_DOWN:
				$seqNo += 3;
				break;
			case self::MOVE_FIRST:
				$seqNo = 1;
				break;
			case self::MOVE_LAST:
				$seqNo = 10000000;
				break;
			default:
				if ($pos * 2 < $seqNo) {
					$seqNo = ($pos-1) * 2 + 1;
				} else if ($pos * 2 > $seqNo) {
					$seqNo = $pos * 2 + 1;
				}
				break;
			}

			$sth = $context->connection->prepare(
				str_replace("[T]", $tableName,
				"UPDATE [T] SET sequence_no = :seqNo WHERE
					instance_id = :instId AND [T]_id = :id"));

			$context->connection->bindInstance($sth);
			$sth->bindValue(":seqNo", $seqNo, \PDO::PARAM_INT);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}

	}

}

?>