<?php

namespace App\Objects\Schedule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Objects\DataObject;

abstract class Schedule extends DataObject {

	const INDEX_TARGET_ID = 'target_id';

	/**
	 * @param  int $id
	 *
	 * @return Collection
	 */
	public static function byTarget(int $id): Collection {
		return self::fromMultipleData(
			DB::table(static::TABLE)->where(static::INDEX_TARGET_ID, $id)->get()
		);
	}

	/**
	 * @param  int $id
	 *
	 * @return Schedule|null
	 */
	public static function byTargetCurrent(int $id): ?Schedule {
		$data = DB::table(static::TABLE)->where(static::INDEX_TARGET_ID, $id)->orderByDesc(self::INDEX_ID)->limit(1)->get();

		if($data->isEmpty()) {
			return null;
		}

		return static::fromData($data->last());
	}

	/**
	 * @var int
	 */
	private $target_id;

	/**
	 *  ____       _              _       _
	 * / ___|  ___| |__   ___  __| |_   _| | ___
	 * \___ \ / __| '_ \ / _ \/ _` | | | | |/ _ \
	 *  ___) | (__| | | |  __/ (_| | |_| | |  __/
	 * |____/ \___|_| |_|\___|\__,_|\__,_|_|\___|
	 *
	 *
	 * @param int|null $id
	 * @param int      $target_id
	 */
	public function __construct(int $id = null, int $target_id) {
		parent::__construct($id);

		$this->target_id = $target_id;
	}

	/**
	 * @return int
	 */
	public function getTargetId(): int {
		return $this->target_id;
	}

	/**
	 * @return Collection
	 */
	public function toUniqueData(): Collection {
		$data = new Collection([
			static::INDEX_TARGET_ID => $this->getTargetId()
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