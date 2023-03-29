<?php

namespace App\Objects\Schedule\Day;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\Schedule\Day\DaySchedule;
use App\Objects\Schedule\Week\GroupWeekSchedule;

use stdClass;

class GroupDaySchedule extends DaySchedule {

	const TABLE = 'group_day_schedules';

	const INDEX_TARGET_ID = 'group_id';

	/**
	 * @param  GroupWeekSchedule $week
	 *
	 * @return Collection
	 */
	public static function byWeek(GroupWeekSchedule $week): Collection {
		$list   = $week->toUniqueData();
		$result = self::fromMultipleData(
			DB::table(self::TABLE)->whereIn(self::INDEX_ID, $list->values())->get()
		);

		foreach($list as $index => $schedule_id) {
			$found = false;

			foreach($result as $schedule) {
				if(!isset($schedule)) {
					continue;
				}

				if($schedule->getId() !== $schedule_id) {
					continue;
				}

				$found        = true;
				$list[$index] = $schedule;

				break;
			}

			if(!$found) {
				$list[$index] = new GroupDaySchedule(null, $week->getTargetId());
			}
		}

		return $list;
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return GroupDaySchedule
	 */
	public static function fromData(stdClass $data) {
		$id         = self::ejectDataValue($data, self::INDEX_ID);
		$group_id   = self::ejectDataValue($data, self::INDEX_TARGET_ID);

		if(!isset($group_id)) {
			return null;
		}

		$subject_ids = new Collection();

		foreach(self::LIST_INDEX_SUBJECT_ID as $index) {
			$subject_ids[$index] = self::ejectDataValue($data, $index);
		}

		return new GroupDaySchedule(
			$id,
			intval($group_id),
			$subject_ids
		);
	}
}