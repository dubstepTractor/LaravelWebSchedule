<?php

namespace App\Objects;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use stdClass;

abstract class DataObject {

	/**
	 * Override me.
	 */
	const TABLE = '';

	const INDEX_ID = 'id';

	/**
	 * @param  int $id
	 *
	 * @return DataObject|null
	 */
	public static function byId(int $id) {
		$data = DB::table(static::TABLE)->where(static::INDEX_ID, $id)->get();

		if($data->isEmpty()) {
			return null;
		}

		return static::fromData($data->last());
	}

	/**
	 * @param  int|null $id
	 * @param  stdClass $data
	 *
	 * @return DataObject|null
	 */
	public static function fromDataEntry(?int $id = null, stdClass $data) {
		$data[self::INDEX_ID] = $id;

		return static::fromData($data);
	}

	/**
	 * @param  Collection $list
	 *
	 * @return Collection
	 */
	public static function fromMultipleData(Collection $list): Collection {
		foreach($list as $index => $data) {
			$list[$index] = static::fromData($data);
		}

		return $list;
	}

	/**
	 * @param  stdClass $data
	 *
	 * @return DataObject|null
	 */
	abstract public static function fromData(stdClass $data);

	/**
	 * @param  stdClass $data
	 * @param  string   $index
	 * @param  mixed    $default
	 *
	 * @return mixed|null
	 */
	protected static function ejectDataValue(stdClass $data, string $index, $default = null) {
		return $data->$index ?? $default;
	}

	/**
	 * @var int|null
	 */
	private $id;

	/**
	 *  ____        _         ___  _     _           _
	 * |  _ \  __ _| |_ __ _ / _ \| |__ (_) ___  ___| |_
	 * | | | |/ _` | __/ _` | | | | '_ \| |/ _ \/ __| __|
	 * | |_| | (_| | || (_| | |_| | |_) | |  __/ (__| |_
	 * |____/ \__,_|\__\__,_|\___/|_.__// |\___|\___|\__|
	 *                                |__/
	 *
	 * @param int|null $id
	 */
	protected function __construct(?int $id = null) {
		$this->id = $id;
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * @return Collection
	 */
	abstract public function toDataEntry(): Collection;

	/**
	 * @return Collection
	 */
	public function toData(): Collection {
		$data = [
			self::INDEX_ID => $this->getId()
		];

		foreach($this->toDataEntry() as $index => $value) {
            $data[$index] = $value;
        }

        return $data;
	}
}