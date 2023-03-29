<?php

namespace App\Http\Controllers\Schedule;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use App\Objects\Base\Group;
use App\Objects\Base\Teacher;
use App\Objects\Base\Subject;
use App\Objects\Util\WeekDay;
use App\Objects\Schedule\Day\GroupDaySchedule;
use App\Objects\Schedule\Week\GroupWeekSchedule;

use App\Http\Controllers\Controller;

use DateTime;

class DefaultScheduleController extends Controller
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
        return view('schedule.default', [
            'alert'  => $alert,
            'groups' => Group::all(true)
        ]);
    }

    /**
     * Show the default schedule for certain group.
     *
     * @param  mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id = null)
    {
        if($id === null or !is_numeric($id)) {
            return $this->index('Выберите группу из списка');
        }

        $id    = intval(trim($id));
        $group = Group::byId($id);

        if(empty($group)) {
            return $this->index('Указанная группа не найдена');
        }

        $week = GroupWeekSchedule::byTargetCurrent($group->getId());

        if(empty($week)) {
            return $this->index('Группа '.$group->getNumber(). ' '. $group->getLetter(). ' не имеет расписания');
        }

        $weekdays = new Collection();
        $days     = new Collection();
        $subjects = new Collection();
        $teachers = new Collection();

        foreach(GroupDaySchedule::byWeek($week) as $day) {
            $weekday = WeekDay::next();

            $weekdays[$weekday->getId()] = $weekday;
            $days[$weekday->getId()]     = $day;

            if(!isset($day) or $day->getId() === null) {
                continue;
            }

            $subject_i = 0;

            $subjects[$weekday->getId()] = new Collection();

            foreach(Subject::byDay($day) as $subject) {
                $subjects[$weekday->getId()][] = $subject;

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
                $subjects[$weekday->getId()] = $subjects[$weekday->getId()]->slice(0, $subject_i);
            }
        }

        WeekDay::reset();

        return view('schedule.default', [
            'groups'   => Group::all(true),
            'current'  => WeekDay::getByDateTime(new DateTime('today')),
            'weekdays' => $weekdays,
            'days'     => $days,
            'subjects' => $subjects,
            'teachers' => $teachers
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

        return redirect('/default/'. $id);
    }
}
