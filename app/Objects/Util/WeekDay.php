<?php

namespace App\Objects\Util;

use InvalidArgumentException;
use Exception;
use DateTime;

class WeekDay {

	const ID_MONDAY    = 0;
	const ID_TUESDAY   = 1;
	const ID_WEDNESDAY = 2;
	const ID_THIRSDAY  = 3;
	const ID_FRIDAY    = 4;
	const ID_SATURDAY  = 5;
	const ID_SUNDAY    = 6;

	const NAME_MONDAY    = 'Понедельник';
	const NAME_TUESDAY   = 'Вторник';
	const NAME_WEDNESDAY = 'Среда';
	const NAME_THIRSDAY  = 'Четверг';
	const NAME_FRIDAY    = 'Пятница';
	const NAME_SATURDAY  = 'Суббота';
	const NAME_SUNDAY    = 'Воскресенье';

	/**
	 * From format "N" - ISO day of the week.
	 */
	const FORMAT_MONDAY    = 1;
	const FORMAT_TUESDAY   = 2;
	const FORMAT_WEDNESDAY = 3;
	const FORMAT_THIRSDAY  = 4;
	const FORMAT_FRIDAY    = 5;
	const FORMAT_SATURDAY  = 6;
	const FORMAT_SUNDAY    = 7;

	const LIST = [
		self::ID_MONDAY    => [ self::NAME_MONDAY,    self::FORMAT_MONDAY    ],
		self::ID_TUESDAY   => [ self::NAME_TUESDAY,   self::FORMAT_TUESDAY   ],
		self::ID_WEDNESDAY => [ self::NAME_WEDNESDAY, self::FORMAT_WEDNESDAY ],
		self::ID_THIRSDAY  => [ self::NAME_THIRSDAY,  self::FORMAT_THIRSDAY  ],
		self::ID_FRIDAY    => [ self::NAME_FRIDAY,    self::FORMAT_FRIDAY    ],
		self::ID_SATURDAY  => [ self::NAME_SATURDAY,  self::FORMAT_SATURDAY  ],
		self::ID_SUNDAY    => [ self::NAME_SUNDAY,    self::FORMAT_SUNDAY    ]
	];

	/**
	 * @var int
	 */
	private static $pointer = 0;

	/**
	 * @return WeekDay|null
	 */
	public static function next(): ?WeekDay {
		$list = array_keys(self::LIST);

		if( !isset($list[self::$pointer]) )
		{
			return null;
		}

		return self::get($list[self::$pointer++]);
	}

	public static function reset(): void {
		self::$pointer = 0;
	}

	/**
	 * @return int
	 */
	public static function getCount(): int {
		return count(self::LIST);
	}

	/**
	 * @param  int $id
	 *
	 * @return WeekDay|null
	 */
	public static function get(int $id): ?WeekDay {
		try {
			$result = new WeekDay($id);
		} catch( InvalidArgumentException $exception ) {
			$result = null;
		}

		return $result;
	}

	public static function getByDateTime(DateTime $date): WeekDay {
		$format = intval($date->format('N'));

		foreach(self::LIST as $id => $data) {
			if($format !== $data[1]) {
				continue;
			}

			return new WeekDay($id);
		}

		throw new Exception('WeekDay does not set up correctly!');
	}

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var int
	 */
	private $format;

	/**
	 * @param  int $id
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(int $id) {
		$this->id = $id;

		if(!isset(self::LIST[$id])) {
			throw new InvalidArgumentException('Invalid id '.$id.'!');
		}

		$this->name   = self::LIST[$id][0];
		$this->format = self::LIST[$id][1];
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getFormat(): int {
		return $this->format;
	}
}