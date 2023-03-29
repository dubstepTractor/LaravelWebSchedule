<?php

namespace App\Http\Controllers\Schedule;

use Illuminate\Support\Collection;

use App\Objects\Base\Group;
use App\Objects\Base\Teacher;
use App\Objects\Base\Subject;
use App\Objects\Util\WeekDay;
use App\Objects\Schedule\Day\GroupDaySchedule;
use App\Objects\Schedule\Day\Change\ScheduleChange;
use App\Objects\Schedule\Week\GroupWeekSchedule;

use App\Http\Controllers\Controller;

use DateTime;

class CurrentScheduleController extends Controller
{
    /**
     * @param  string|null $alert
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(?string $alert = null)
    {
        // NOTE: ScheduleChange and GroupDaySchedule may have same id.

        // Declare current day.

        $date = new DateTime();

        // Switch to tomorrow at the end of the day.

        if($date->format('H') >= 18) {
            $date = new DateTime('tomorrow');
        }

        $weekday  = WeekDay::getByDateTime($date);
        $groups   = new Collection();
        $days     = new Collection();
        $subjects = new Collection();
        $teachers = new Collection();

        foreach(Group::all() as $group) {
            if(!isset($group) or $group->getId() === null) {
                continue;
            }

            $week = GroupWeekSchedule::byTargetCurrent($group->getId());

            if(!isset($week) or $week->getId() === null) {
                continue;
            }

            // Sort groups by letters.

            $letter = $group->getLetter();

            if(!isset($groups[$letter])) {
                $groups[$letter] = new Collection();
            }

            $groups[$letter][] = $group;

            //

            $changes = ScheduleChange::byTargetAndDate($week->getId(), $date);
            $change  = $changes->isEmpty() ? null : $changes[0];

            foreach(GroupDaySchedule::byWeek($week) as $day) {
                if($weekday->getId() !== WeekDay::next()->getId()) {
                    continue;
                }

                $day = $change ?? $day;

                $days[$group->getId()] = $day;

                if(!isset($day) or $day->getId() === null) {
                    break;
                }

                $subject_i = 0;

                $subjects[$group->getId()] = new Collection();

                foreach(Subject::byDay($day) as $subject) {
                    $subjects[$group->getId()][] = $subject;

                    if(!isset($subject) or $subject->getId() === null) {
                        $subject_i--;

                        continue;
                    }

                    $subject_i = 0;

                    $teachers[$subject->getId()] = new Collection();

                    foreach(Teacher::bySubject($subject) as $teacher) {
                        if(!isset($teacher) or $teacher->getId() === null) {
                            continue;
                        }

                        $teachers[$subject->getId()][] = $teacher;
                    }
                }

                // Remove last unused subjects.

                if($subject_i !== 0) {
                    $subjects[$group->getId()] = $subjects[$group->getId()]->slice(0, $subject_i);
                }
            }

            WeekDay::reset();
        }

        if($groups->isEmpty()) {
            return view('schedule.current', [
                'alert'   => 'На сегодня расписаний нет',
                'current' => $weekday,
                'date'    => $date
            ]);
        }

        // Sort groups by descending order.

        foreach($groups as $letter => $data) {
            $groups[$letter] = $groups[$letter]->sort(function (Group $a, Group $b): int {
                if($a->getNumber() > $b->getNumber()) {
                    return 1;
                }

                return -1;
            });
        }

        return view('schedule.current', [
            'alert'    => $alert,
            'groups'   => $groups,
            'days'     => $days,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'current'  => $weekday,
            'date'     => $date
        ]);
    }
}
