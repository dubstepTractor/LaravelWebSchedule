<?php

namespace App\Objects\Schedule\Week;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\Schedule\Schedule;

use stdClass;

class GroupWeekSchedule extends Schedule {

	const TABLE = 'group_week_schedules';

	const INDEX_TARGET_ID           = 'group_id';
	const INDEX_FIRST_SCHEDULE_ID   = 'first_schedule_id';
	const INDEX_SECOND_SCHEDULE_ID  = 'second_schedule_id';
	const INDEX_THIRD_SCHEDULE_ID   = 'third_schedule_id';
	const INDEX_FOURTH_SCHEDULE_ID  = 'fourth_schedule_id';
	const INDEX_FIFTH_SCHEDULE_ID   = 'fifth_schedule_id';
	const INDEX_SIXTH_SCHEDULE_ID   = 'sixth_schedule_id';
	const INDEX_SEVENTH_SCHEDULE_ID = 'seventh_schedule_id';

	const LIST_INDEX_SCHEDULE_ID = [
		self::INDEX_FIRST_SCHEDULE_ID,
		self::INDEX_SECOND_SCHEDULE_ID,
		self::INDEX_THIRD_SCHEDULE_ID,
		self::INDEX_FOURTH_SCHEDULE_ID,
		self::INDEX_FIFTH_SCHEDULE_ID,
		self::INDEX_SIXTH_SCHEDULE_ID,
		self::INDEX_SEVENTH_SCHEDULE_ID
	];

	/**
	 * @param  int $id
	 *
	 * @return Collection
	 */
	public static function bySchedule(int $id): Collection {
		return self::fromMultipleData(
			DB::table(static::TABLE)
				->where(  self::INDEX_FIRST_SCHEDULE_ID,   $id)
				->orWhere(self::INDEX_SECOND_SCHEDULE_ID,  $id)
				->orWhere(self::INDEX_THIRD_SCHEDULE_ID,   $id)
				->orWhere(self::INDEX_FOURTH_SCHEDULE_ID,  $id)
				->orWhere(self::INDEX_FIFTH_SCHEDULE_ID,   $id)
				->orWhere(self::INDEX_SIXTH_SCHEDULE_ID,   $id)
				->orWhere(self::INDEX_SEVENTH_SCHEDULE_ID, $id)
				->get()
		);
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return GroupWeekSchedule|null
	 */
	public static function fromData(stdClass $data): ?GroupWeekSchedule {
		$id       = self::ejectDataValue($data, self::INDEX_ID);
		$group_id = self::ejectDataValue($data, self::INDEX_TARGET_ID);

		if(!isset($group_id)) {
			return null;
		}

		$schedule_ids = new Collection();

		foreach(self::LIST_INDEX_SCHEDULE_ID as $index) {
			$schedule_ids[$index] = self::ejectDataValue($data, $index);
		}

		return new GroupWeekSchedule(
			$id,
			intval($group_id),
			$schedule_ids
		);
	}

	/**
	 * @var Collection
	 */
	private $schedule_id_list;

	/**
	 * __        __        _     ____       _              _       _
	 * \ \      / /__  ___| | __/ ___|  ___| |__   ___  __| |_   _| | ___
	 *  \ \ /\ / / _ \/ _ \ |/ /\___ \ / __| '_ \ / _ \/ _` | | | | |/ _ \
	 *   \ V  V /  __/  __/   <  ___) | (__| | | |  __/ (_| | |_| | |  __/
	 *    \_/\_/ \___|\___|_|\_\|____/ \___|_| |_|\___|\__,_|\__,_|_|\___|
	 *
	 *
	 * @param int|null        $id
	 * @param int             $target_id
	 * @param Collection|null $schedule_ids
	 */
	public function __construct(?int $id = null, int $target_id, ?Collection $schedule_ids = null) {
		parent::__construct($id, $target_id);

		if(!isset($schedule_ids)) {
			$schedule_ids = new Collection();
		}

		$this->schedule_id_list = new Collection();

		foreach(self::LIST_INDEX_SCHEDULE_ID as $index) {
			$this->schedule_id_list[$index] = isset($schedule_ids[$index]) ? intval($schedule_ids[$index]) : null;
		}
	}

	/**
	 * @param  string $index
	 *
	 * @return int|null
	 */
	public function getScheduleId(string $index): ?int {
		$list = $this->toUniqueData();

		return $list[$index] ?? null;
	}

	/**
	 * @param string $index
	 * @param int    $id
	 */
	public function setScheduleId(string $index, int $id): void {
		if(!isset($this->schedule_id_list[$index])) {
			return;
		}

		$this->schedule_id_list[$index] = $id;
	}

	/**
	 * @return Collection
	 */
	public function toUniqueData(): Collection {
		return $this->schedule_id_list;
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