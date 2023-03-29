<?php

namespace App\Objects\Schedule\Day\Change;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\Schedule\Day\DaySchedule;

use DateTime;
use stdClass;

class ScheduleChange extends DaySchedule {

	const TABLE = 'schedule_changes';

	const INDEX_TARGET_ID = 'week_id';
	const INDEX_DATE      = 'date';

	const FORMAT_DATE = 'Y-m-d';

	/**
	 * @param  DateTime $date
	 * @param  bool     $all_current
	 *
	 * @return Collection
	 */
	public static function byDate(DateTime $date, bool $all_current = false): Collection {
		$date = $date->format(self::FORMAT_DATE);

		if($all_current) {
			return self::fromMultipleData(
				DB::table(self::TABLE)->whereDate(self::INDEX_DATE, '>=', $date)->get()
			);
		}

		return self::fromMultipleData(
			DB::table(self::TABLE)->whereDate(self::INDEX_DATE, $date)->get()
		);
	}

	/**
	 * @param  int      $id
	 * @param  DateTime $date
	 * @param  bool     $all_current
	 *
	 * @return Collection
	 */
	public static function byTargetAndDate(int $id, DateTime $date, bool $all_current = false): Collection {
		$date = $date->format(self::FORMAT_DATE);

		if($all_current) {
			return self::fromMultipleData(
				DB::table(self::TABLE)->where(self::INDEX_TARGET_ID, $id)->whereDate(self::INDEX_DATE, '>=', $date)->get()
			);
		}

		return self::fromMultipleData(
			DB::table(self::TABLE)->where(self::INDEX_TARGET_ID, $id)->whereDate(self::INDEX_DATE, $date)->get()
		);
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return ScheduleChange|null
	 */
	public static function fromData(stdClass $data): ?ScheduleChange {
		$id          = self::ejectDataValue($data, self::INDEX_ID);
		$target_id   = self::ejectDataValue($data, self::INDEX_TARGET_ID);
		$date        = self::ejectDataValue($data, self::INDEX_DATE);

		if(!isset($target_id) or !isset($date)) {
			return null;
		}

		$date = DateTime::createFromFormat(self::FORMAT_DATE, $date)->setTime(0, 0);

		if(!$date) {
			return null;
		}

		$subject_ids = new Collection();

		foreach(self::LIST_INDEX_SUBJECT_ID as $index) {
			$subject_ids[$index] = self::ejectDataValue($data, $index);
		}

		return new ScheduleChange(
			$id,
			intval($target_id),
			$subject_ids,
			$date
		);
	}

	/**
	 * @var DateTime
	 */
	private $date;

	/**
	 *  ____       _              _       _       ____ _
	 * / ___|  ___| |__   ___  __| |_   _| | ___ / ___| |__   __ _ _ __   __ _  ___
	 * \___ \ / __| '_ \ / _ \/ _` | | | | |/ _ \ |   | '_ \ / _` | '_ \ / _` |/ _ \
	 *  ___) | (__| | | |  __/ (_| | |_| | |  __/ |___| | | | (_| | | | | (_| |  __/
	 * |____/ \___|_| |_|\___|\__,_|\__,_|_|\___|\____|_| |_|\__,_|_| |_|\__, |\___|
	 *                                                                   |___/
	 *
	 * @param int|null        $id
	 * @param int             $target_id
	 * @param Collection|null $subject_ids
	 * @param DateTime        $date
	 */
	public function __construct(?int $id = null, int $target_id, ?Collection $subject_ids = null, DateTime $date) {
		parent::__construct($id, $target_id, $subject_ids);

		$this->date = $date;
	}

	/**
	 * @return DateTime
	 */
	public function getDate(): DateTime {
		return $this->date;
	}
}