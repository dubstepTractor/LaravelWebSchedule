<?php

namespace App\Objects\Base;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\DataObject;
use App\Objects\Base\Subject;

use stdClass;

class Teacher extends DataObject {

	const TABLE = 'teachers';

	const INDEX_NAME       = 'name';
	const INDEX_SURNAME    = 'surname';
	const INDEX_PATRONYMIC = 'patronymic';

	/**
	 * @return Collection
	 */
	public static function all(): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->get()
		);
	}

	/**
	 * @param  Subject $subject
	 *
	 * @return Collection
	 */
	public static function bySubject(Subject $subject): Collection {
		$list   = $subject->toUniqueData();
		$result = self::fromMultipleData(
			DB::table(self::TABLE)->whereIn(self::INDEX_ID, $list->values())->get()
		);

		foreach($list as $index => $teacher_id) {
			$found = false;

			foreach($result as $teacher) {
				if(!isset($teacher)) {
					continue;
				}

				if($teacher->getId() !== $teacher_id) {
					continue;
				}

				$found        = true;
				$list[$index] = $teacher;

				break;
			}

			if(!$found) {
				$list[$index] = null;
			}
		}

		return $list;
	}

	/**
	 * @param  string $name
	 *
	 * @return Collection
	 */
	public static function byName(string $name): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_NAME, $name)->get()
		);
	}

	/**
	 * @param  string $surname
	 *
	 * @return Collection
	 */
	public static function bySurname(string $surname): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_SURNAME, $surname)->get()
		);
	}

	/**
	 * @param  string $patronymic
	 *
	 * @return Collection
	 */
	public static function byPatronymic(string $patronymic): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_PATRONYMIC, $patronymic)->get()
		);
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return Teacher|null
	 */
	public static function fromData(stdClass $data): ?Teacher {
		$id         = self::ejectDataValue($data, self::INDEX_ID);
		$name       = self::ejectDataValue($data, self::INDEX_NAME);
		$surname    = self::ejectDataValue($data, self::INDEX_SURNAME);
		$patronymic = self::ejectDataValue($data, self::INDEX_PATRONYMIC);

		if(!isset($name) or !isset($surname)) {
			return null;
		}

		return new Teacher(
			$id,
			strval($name),
			strval($surname),
			$patronymic
		);
	}

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $surname;

	/**
	 * @var string|null
	 */
	private $patronymic;

	/**
	 *  _____               _
	 * |_   _|__  __ _  ___| |__   ___ _ __
	 *   | |/ _ \/ _` |/ __| '_ \ / _ \ '__|
	 *   | |  __/ (_| | (__| | | |  __/ |
	 *   |_|\___|\__,_|\___|_| |_|\___|_|
	 *
	 *
	 * @param int|null    $id
	 * @param string      $name
	 * @param string      $surname
	 * @param string|null $patronymic
	 */
	public function __construct(?int $id = null, string $name, string $surname, ?string $patronymic = null) {
		parent::__construct($id);

		$this->name       = $name;
		$this->surname    = $surname;
		$this->patronymic = $patronymic;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSurname(): string {
		return $this->surname;
	}

	/**
	 * @return string|null
	 */
	public function getPatronymic(): ?string {
		return $this->patronymic;
	}

	/**
	 * @return Collection
	 */
	public function toUniqueData(): Collection {
		$data = new Collection([
			self::INDEX_NAME       => $this->getName(),
			self::INDEX_SURNAME    => $this->getSurname(),
			self::INDEX_PATRONYMIC => $this->getPatronymic()
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