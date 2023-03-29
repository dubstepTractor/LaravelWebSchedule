<?php

namespace App\Objects\Schedule\Day;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\Schedule\Schedule;
use App\Objects\Schedule\Week\WeekSchedule;

abstract class DaySchedule extends Schedule {

	const INDEX_FIRST_SUBJECT_ID   = 'first_subject_id';
	const INDEX_SECOND_SUBJECT_ID  = 'second_subject_id';
	const INDEX_THIRD_SUBJECT_ID   = 'third_subject_id';
	const INDEX_FOURTH_SUBJECT_ID  = 'fourth_subject_id';
	const INDEX_FIFTH_SUBJECT_ID   = 'fifth_subject_id';
	const INDEX_SIXTH_SUBJECT_ID   = 'sixth_subject_id';
	const INDEX_SEVENTH_SUBJECT_ID = 'seventh_subject_id';
	const INDEX_EIGHTH_SUBJECT_ID  = 'eighth_subject_id';
	const INDEX_NINTH_SUBJECT_ID   = 'ninth_subject_id';
	const INDEX_TENTH_SUBJECT_ID   = 'tenth_subject_id';

	const LIST_INDEX_SUBJECT_ID = [
		self::INDEX_FIRST_SUBJECT_ID,
		self::INDEX_SECOND_SUBJECT_ID,
		self::INDEX_THIRD_SUBJECT_ID,
		self::INDEX_FOURTH_SUBJECT_ID,
		self::INDEX_FIFTH_SUBJECT_ID,
		self::INDEX_SIXTH_SUBJECT_ID,
		self::INDEX_SEVENTH_SUBJECT_ID,
		self::INDEX_EIGHTH_SUBJECT_ID,
		self::INDEX_NINTH_SUBJECT_ID,
		self::INDEX_TENTH_SUBJECT_ID
	];

	/**
	 * @param  int $id
	 *
	 * @return Collection
	 */
	public static function bySubject(int $id): Collection {
		return self::fromMultipleData(
			DB::table(static::TABLE)
				->where(  self::INDEX_FIRST_SUBJECT_ID,   $id)
				->orWhere(self::INDEX_SECOND_SUBJECT_ID,  $id)
				->orWhere(self::INDEX_THIRD_SUBJECT_ID,   $id)
				->orWhere(self::INDEX_FOURTH_SUBJECT_ID,  $id)
				->orWhere(self::INDEX_FIFTH_SUBJECT_ID,   $id)
				->orWhere(self::INDEX_SIXTH_SUBJECT_ID,   $id)
				->orWhere(self::INDEX_SEVENTH_SUBJECT_ID, $id)
				->orWhere(self::INDEX_EIGHTH_SUBJECT_ID,  $id)
				->orWhere(self::INDEX_NINTH_SUBJECT_ID,   $id)
				->orWhere(self::INDEX_TENTH_SUBJECT_ID,   $id)
				->get()
		);
	}

	/**
	 * @var Collection
	 */
	private $subject_id_list;

	/**
	 *  ____              ____       _              _       _
	 * |  _ \  __ _ _   _/ ___|  ___| |__   ___  __| |_   _| | ___
	 * | | | |/ _` | | | \___ \ / __| '_ \ / _ \/ _` | | | | |/ _ \
	 * | |_| | (_| | |_| |___) | (__| | | |  __/ (_| | |_| | |  __/
	 * |____/ \__,_|\__, |____/ \___|_| |_|\___|\__,_|\__,_|_|\___|
	 *              |___/
	 *
	 * @param int|null        $id
	 * @param int             $target_id
	 * @param Collection|null $subject_ids
	 */
	public function __construct(?int $id = null, int $target_id, ?Collection $subject_ids = null) {
		parent::__construct($id, $target_id);

		if(!isset($subject_ids)) {
			$subject_ids = new Collection();
		}

		$this->subject_id_list = new Collection();

		foreach(self::LIST_INDEX_SUBJECT_ID as $index) {
			$this->subject_id_list[$index] = isset($subject_ids[$index]) ? intval($subject_ids[$index]) : null;
		}
	}

	/**
	 * @param  string $index
	 *
	 * @return int|null
	 */
	public function getSubjectId(string $index): ?int {
		$list = $this->toUniqueData();

		return $list[$index] ?? null;
	}

	/**
	 * @param string $index
	 * @param int    $id
	 */
	public function setSubjectId(string $index, int $id): void {
		if(!isset($this->subject_id_list[$index])) {
			return;
		}

		$this->subject_id_list[$index] = $id;
	}

	/**
	 * @return Collection
	 */
	public function toUniqueData(): Collection {
		return $this->subject_id_list;
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