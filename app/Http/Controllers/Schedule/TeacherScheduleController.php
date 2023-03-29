<?php

namespace App\Http\Controllers\Schedule;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use App\Objects\Base\Group;
use App\Objects\Base\Teacher;
use App\Objects\Base\Subject;
use App\Objects\Util\WeekDay;
use App\Objects\Schedule\Day\GroupDaySchedule;
use App\Objects\Schedule\Day\Change\ScheduleChange;
use App\Objects\Schedule\Week\GroupWeekSchedule;

use App\Http\Controllers\Controller;

use DateInterval;
use DateTime;

class TeacherScheduleController extends Controller
{
    /**
     * Show the choosing form.
     *
     * @param  string|null $alert
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(?string $alert = null)
    {
        return view('schedule.teacher', [
            'alert'    => $alert,
            'teachers' => Teacher::all()
        ]);
    }

    /**
     * Show the schedule for certain teacher.
     *
     * @param  mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id = null)
    {
        // NOTE: ScheduleChange and GroupDaySchedule may have same id.

        if($id === null or !is_numeric($id)) {
            return $this->index('Выберите преподавателя из списка');
        }

        $id      = intval(trim($id));
        $teacher = Teacher::byId($id);

        if(empty($teacher)) {
            return $this->index('Указанный преподаватель не найден');
        }

        // Get ISO start of the week to show all changes.

        $date = (new DateTime())->setTimestamp(strtotime('last monday', strtotime('tomorrow')));

        $weekdays = new Collection();
        $days     = new Collection();
        $subjects = new Collection();

        foreach(Group::all() as $group) {
            $week = GroupWeekSchedule::byTargetCurrent($group->getId());

            if(empty($week)) {
                continue;
            }

            $changes = ScheduleChange::byTargetAndDate($week->getId(), $date, true);

            foreach(GroupDaySchedule::byWeek($week) as $day) {
                $weekday                     = WeekDay::next();
                $weekdays[$weekday->getId()] = $weekday;

                foreach($changes as $change) {
                    $check = WeekDay::getByDateTime($change->getDate());

                    if(!isset($check)) {
                        continue;
                    }

                    if($check->getId() !== $weekday->getId()) {
                        continue;
                    }

                    // Check for next week.

                    $next = (clone $date)->add(new DateInterval('P7D'));

                    if($next <= $change->getDate()) {
                        continue;
                    }

                    $day = $change;
                }

                if(!isset($day) or $day->getId() === null) {
                    continue;
                }

                $found_day = false;
                $subject_i = -1;

                foreach(Subject::byDay($day) as $subject) {
                    $subject_i++;

                    if(!isset($subject) or $subject->getId() === null) {
                        continue;
                    }

                    if($subject->getTeacherId() !== $id and $subject->getSecondTeacherId() !== $id) {
                        continue;
                    }

                    // Found one.

                    if(!isset($subjects[$weekday->getId()])) {
                        $subjects[$weekday->getId()] = new Collection();
                    }

                    $subjects[$weekday->getId()][$subject_i] = $subject;

                    $found_day = true;
                }

                if(!$found_day) {
                    continue;
                }

                // add day by weekday id (0-6).

                if(!isset($days[$weekday->getId()])) {
                    // insert.
                    $days[$weekday->getId()] = $day;

                    continue;
                }

                // merge.
                foreach($day->toUniqueData() as $index => $value) {
                    if(!isset($value)) {
                        continue;
                    }

                    $days[$weekday->getId()]->setSubjectId($index, $value);
                }

                // end.
            }

            WeekDay::reset();
        }

        // Sort subjects.

        $sorted_subjects = new Collection();

        foreach($weekdays as $weekday) {
            $weekday_id = $weekday->getId();

            if(!isset($days[$weekday_id])) {
                $days[$weekday_id] = null;
            }

            if(!isset($subjects[$weekday_id])) {
                $subjects[$weekday_id] = new Collection();
            }

            $slice_i = 0;

            for($i = 0; $i < 10; $i++) {
                if(!isset($sorted_subjects[$weekday_id])) {
                    $sorted_subjects[$weekday_id] = new Collection();
                }

                if(!isset($subjects[$weekday_id][$i])) {
                    $slice_i--;
                    $sorted_subjects[$weekday_id][$i] = null;

                    continue;
                }

                $slice_i = 0;

                $sorted_subjects[$weekday_id][$i] = $subjects[$weekday_id][$i];
            }

            // Remove last unused subjects.

            if($slice_i !== 0) {
                $sorted_subjects[$weekday_id] = $sorted_subjects[$weekday_id]->slice(0, $slice_i);
            }
        }

        return view('schedule.teacher', [
            'teachers' => Teacher::all(),
            'current'  => WeekDay::getByDateTime(new DateTime('today')),
            'weekdays' => $weekdays,
            'days'     => $days,
            'subjects' => $sorted_subjects
        ]);
    }

    /**
     * For POST request.
     *
     * @param  Request $request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $id = $request->input('select') ?? '';

        return redirect('/teacher/'.$id);
    }
}
