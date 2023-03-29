<?php

namespace App\Objects\Base;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\DataObject;
use App\Objects\Schedule\Day\DaySchedule;

use stdClass;

class Subject extends DataObject {

	const TABLE = 'subjects';

	const INDEX_NAME              = 'name';
	const INDEX_CLASSROOM         = 'classroom';
	const INDEX_TEACHER_ID        = 'teacher_id';
	const INDEX_SECOND_TEACHER_ID = 'second_teacher_id';
	const INDEX_COMMENTARY        = 'commentary';

	/**
	 * @return Collection
	 */
	public static function all(): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->get()
		);
	}

	/**
	 * @param  DaySchedule $day
	 *
	 * @return Collection
	 */
	public static function byDay(DaySchedule $day): Collection {
		$list   = $day->toUniqueData();
		$result = self::fromMultipleData(
			DB::table(self::TABLE)->whereIn(self::INDEX_ID, $list->values())->get()
		);

		foreach($list as $index => $subject_id) {
			$found = false;

			foreach($result as $subject) {
				if(!isset($subject)) {
					continue;
				}

				if($subject->getId() !== $subject_id) {
					continue;
				}

				$found        = true;
				$list[$index] = $subject;

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
	 * @param  string $room
	 *
	 * @return Collection
	 */
	public static function byClassroom(string $room): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_CLASSROOM, $room)->get()
		);
	}

	/**
	 * @param  int $id
	 *
	 * @return Collection
	 */
	public static function byTeacher(int $id): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_TEACHER_ID, $id)->orWhere(self::INDEX_SECOND_TEACHER_ID, $id)->get()
		);
	}

	/**
	 * @param  string $comment
	 *
	 * @return Collection
	 */
	public static function byCommentary(string $comment): Collection {
		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_COMMENTARY, $comment)->get()
		);
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return Subject|null
	 */
	public static function fromData(stdClass $data): ?Subject {
		$id      = self::ejectDataValue($data, self::INDEX_ID);
		$name    = self::ejectDataValue($data, self::INDEX_NAME);
		$room    = self::ejectDataValue($data, self::INDEX_CLASSROOM);
		$teacher = self::ejectDataValue($data, self::INDEX_TEACHER_ID);
		$second  = self::ejectDataValue($data, self::INDEX_SECOND_TEACHER_ID);
		$comment = self::ejectDataValue($data, self::INDEX_COMMENTARY);

		if(!isset($name)) {
			return null;
		}

		return new Subject(
			$id,
			strval($name),
			$room,
			$teacher,
			$second,
			$comment
		);
	}

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $classroom;

	/**
	 * @var int|null
	 */
	private $teacher_id;

	/**
	 * @var int|null
	 */
	private $second_teacher_id;

	/**
	 * @var string|null
	 */
	private $commentary;

	/**
	 *  ____        _     _           _
	 * / ___| _   _| |__ (_) ___  ___| |_
	 * \___ \| | | | '_ \| |/ _ \/ __| __|
	 *  ___) | |_| | |_) | |  __/ (__| |_
	 * |____/ \__,_|_.__// |\___|\___|\__|
	 *                 |__/
	 *
	 * @param int|null    $id
	 * @param string      $name
	 * @param string|null $room
	 * @param int|null    $teacher
	 * @param int|null    $second
	 * @param string|null $comment
	 */
	public function __construct(
		?int $id = null, string $name, ?string $room = null,
		?string $teacher = null, ?string $second = null, ?string $comment = null
	) {
		parent::__construct($id);

		$this->name              = $name;
		$this->classroom         = $room;
		$this->teacher_id        = $teacher;
		$this->second_teacher_id = $second;
		$this->commentary        = $comment;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getClassroom(): ?string {
		return $this->classroom;
	}

	/**
	 * @return int|null
	 */
	public function getTeacherId(): ?int {
		return $this->teacher_id;
	}

	/**
	 * @return int|null
	 */
	public function getSecondTeacherId(): ?int {
		return $this->second_teacher_id;
	}

	/**
	 * @return string|null
	 */
	public function getCommentary(): ?string {
		return $this->commentary;
	}

	/**
	 * @return Collection
	 */
	public function toUniqueData(): Collection {
		$data = new Collection([
			self::INDEX_NAME              => $this->getName(),
			self::INDEX_CLASSROOM         => $this->getClassroom(),
			self::INDEX_TEACHER_ID        => $this->getTeacherId(),
			self::INDEX_SECOND_TEACHER_ID => $this->getSecondTeacherId(),
			self::INDEX_COMMENTARY        => $this->getCommentary()
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