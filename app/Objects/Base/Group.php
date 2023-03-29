<?php

namespace App\Objects\Base;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\DataObject;

use stdClass;

class Group extends DataObject {

	const TABLE = 'groups';

	const INDEX_NUMBER = 'number';
	const INDEX_LETTER = 'letter';

	/**
	 * @param  bool $sort
	 *
	 * @return Collection
	 */
	public static function all(bool $sort = false): Collection {
		$groups = self::fromMultipleData(
			DB::table(self::TABLE)->get()
		);

		if(!$sort) {
			return $groups;
		}

		$result = new Collection();

        foreach($groups as $group) {
            if(!isset($group) or $group->getId() === null) {
                continue;
            }

            // Sort groups by letters.

            $letter = $group->getLetter();

            if(!isset($result[$letter])) {
                $result[$letter] = new Collection();
            }

            $result[$letter][] = $group;
        }

        // Sort groups by descending order.

        foreach($result as $letter => $data) {
            $result[$letter] = $result[$letter]->sort(function (Group $a, Group $b): int {
                if($a->getNumber() > $b->getNumber()) {
                    return 1;
                }

                return -1;
            });
        }

        return $result;
	}

	/**
	 * @param  string $number
	 *
	 * @return Collection
	 */
	public static function byNumber(string $number): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_NUMBER, $number)->get()
		);
	}

	/**
	 * @param  string $letter
	 *
	 * @return Collection
	 */
	public static function byLetter(string $letter): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_LETTER, $letter)->get()
		);
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return Group|null
	 */
	public static function fromData(stdClass $data): ?Group {
		$id     = self::ejectDataValue($data, self::INDEX_ID);
		$number = self::ejectDataValue($data, self::INDEX_NUMBER);
		$letter = self::ejectDataValue($data, self::INDEX_LETTER);

		if(!isset($number) or !isset($letter)) {
			return null;
		}

		return new Group(
			$id,
			intval($number),
			strval($letter)
		);
	}

	/**
	 * @var int
	 */
	private $number;

	/**
	 * @var string
	 */
	private $letter;

	/**
	 *   ____
	 *  / ___|_ __ ___  _   _ _ __
	 * | |  _| '__/ _ \| | | | '_ \
	 * | |_| | | | (_) | |_| | |_) |
	 *  \____|_|  \___/ \__,_| .__/
	 *                       |_|
	 *
	 * @param int|null $id
	 * @param int      $number
	 * @param string   $letter
	 */
	public function __construct(?int $id = null, int $number, string $letter) {
		parent::__construct($id);

		$this->number = $number;
		$this->letter = $letter;
	}

	/**
	 * @return int
	 */
	public function getNumber(): int {
		return $this->number;
	}

	/**
	 * @return string
	 */
	public function getLetter(): string {
		return $this->letter;
	}

	/**
	 * @return Collection
	 */
	public function toUniqueData(): Collection {
		$data = new Collection([
			self::INDEX_NUMBER => $this->getNumber(),
			self::INDEX_LETTER => $this->getLetter()
		]);

		return $data;
	}

	/**
	 * @return Collection
	 */
	public function toDataEntry(): Collection {
		$data = parent::toDataEntry();

		foreach($this->toUniqueData() as $index => $value) {
            $data[$index] = $value;
        }

        return $data;
	}
}