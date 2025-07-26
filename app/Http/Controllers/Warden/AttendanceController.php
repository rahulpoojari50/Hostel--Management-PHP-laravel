<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function report(Request $request)
    {
        $hostels = \App\Models\Hostel::where('warden_id', auth()->id())->get();

        // Filters
        $hostelId = $request->input('hostel_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $name = $request->input('name');
        $roomType = $request->input('room_type');
        $email = $request->input('email');
        $roomNo = $request->input('room_no');
        $category = $request->input('category');

        $students = collect();
        $dates = [];
        $attendanceData = [];
        $selectedHostel = null;

        $anyStudentFilter = $name || $roomType || $email || $roomNo || $category;

        if ($hostelId && $dateFrom && $dateTo) {
            $selectedHostel = \App\Models\Hostel::where('warden_id', auth()->id())->find($hostelId);
            if ($selectedHostel) {
                // Build date range
                $start = \Carbon\Carbon::parse($dateFrom);
                $end = \Carbon\Carbon::parse($dateTo);
                $dates = [];
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $dates[] = $date->format('Y-m-d');
                }

                // Query students assigned to this hostel
                $studentsQuery = \App\Models\User::whereHas('roomAssignments.room', function($q) use ($hostelId) {
                    $q->where('hostel_id', $hostelId);
                });
                if ($name) {
                    $studentsQuery->where('name', 'like', "%$name%");
                }
                if ($email) {
                    $studentsQuery->where('email', 'like', "%$email%");
                }
                if ($category) {
                    $studentsQuery->where('category', $category);
                }
                if ($roomNo) {
                    $studentsQuery->whereHas('roomAssignments.room', function($q) use ($roomNo) {
                        $q->where('room_no', $roomNo);
                    });
                }
                if ($roomType) {
                    $studentsQuery->whereHas('roomAssignments.room.roomType', function($q) use ($roomType) {
                        $q->where('type', $roomType);
                    });
                }
                $students = $studentsQuery->with(['roomAssignments.room', 'roomAssignments.room.hostel'])->get();

                // Fetch attendance for all students for the date range
                $attendanceRecords = \App\Models\HostelAttendance::whereIn('student_id', $students->pluck('id'))
                    ->where('hostel_id', $hostelId)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->get();
                // Build attendance data: [student_id][date] = status
                foreach ($attendanceRecords as $record) {
                    $attendanceData[$record->student_id][$record->date] = $record->status;
                }
            }
        } elseif ($anyStudentFilter) {
            // Show students matching filters, even if no hostel or date range is selected
            $studentsQuery = \App\Models\User::query();
            if ($name) {
                $studentsQuery->where('name', 'like', "%$name%");
            }
            if ($email) {
                $studentsQuery->where('email', 'like', "%$email%");
            }
            if ($category) {
                $studentsQuery->where('category', $category);
            }
            if ($roomNo) {
                $studentsQuery->whereHas('roomAssignments.room', function($q) use ($roomNo) {
                    $q->where('room_no', $roomNo);
                });
            }
            if ($roomType) {
                $studentsQuery->whereHas('roomAssignments.room.roomType', function($q) use ($roomType) {
                    $q->where('type', $roomType);
                });
            }
            $students = $studentsQuery->with(['roomAssignments.room', 'roomAssignments.room.hostel'])->get();
        }

        $mealAttendanceReport = [];
        $mealAttendanceMatrix = [];
        $mealAttendanceSummary = [];
        if ($request->input('attendance_type') === 'meal' && $hostelId && $dateFrom && $dateTo) {
            $selectedHostel = \App\Models\Hostel::where('warden_id', auth()->id())->find($hostelId);
            if ($selectedHostel) {
                $start = \Carbon\Carbon::parse($dateFrom);
                $end = \Carbon\Carbon::parse($dateTo);
                $dates = [];
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $dates[] = $date->format('Y-m-d');
                }
                $students = \App\Models\User::whereHas('roomAssignments.room', function($q) use ($hostelId) {
                    $q->where('hostel_id', $hostelId);
                });
                if ($name) {
                    $students->where('name', 'like', "%$name%");
                }
                if ($email) {
                    $students->where('email', 'like', "%$email%");
                }
                if ($category) {
                    $students->where('category', $category);
                }
                if ($roomNo) {
                    $students->whereHas('roomAssignments.room', function($q) use ($roomNo) {
                        $q->where('room_no', $roomNo);
                    });
                }
                if ($roomType) {
                    $students->whereHas('roomAssignments.room.roomType', function($q) use ($roomType) {
                        $q->where('type', $roomType);
                    });
                }
                $students = $students->with(['roomAssignments.room', 'roomAssignments.room.hostel'])->get();
                $studentIds = $students->pluck('id');
                $mealRecords = \App\Models\MealAttendance::whereIn('student_id', $studentIds)
                    ->where('hostel_id', $hostelId)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->get();
                $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
                foreach ($students as $student) {
                    $summary = ['present' => 0, 'total' => 0];
                    foreach ($dates as $date) {
                        foreach ($mealTypes as $mealType) {
                            $record = $mealRecords->first(function($rec) use ($student, $date, $mealType) {
                                return $rec->student_id == $student->id && $rec->date == $date && $rec->meal_type == $mealType;
                            });
                            $status = $record ? $record->status : null;
                            $mealAttendanceMatrix[$student->id][$date][$mealType] = $status;
                            if (in_array($status, ['Taken', 'Skipped', 'On Leave', 'Holiday'])) {
                                $summary['total']++;
                                if ($status === 'Taken') {
                                    $summary['present']++;
                                }
                                // 'Skipped', 'On Leave', 'Holiday' are NOT counted as present
                            }
                        }
                    }
                    $mealAttendanceSummary[$student->id] = $summary;
                }
            }
        }

        // Handle downloads
        if ($request->input('download') === 'csv' && count($students) && count($dates)) {
            $filename = 'attendance_report_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $handle = fopen('php://output', 'w');
            $columns = ['Name', 'Room Number', 'Email', 'Hostel Name'];
            foreach ($dates as $date) {
                $columns[] = \Carbon\Carbon::parse($date)->format('d M Y');
            }
            $columns = array_merge($columns, ['Total Present', 'Total Absent', 'Total On Leave', 'Total Holiday', 'Attendance %', 'Status']);
            $rows = [];
            foreach ($students as $student) {
                $present = $absent = $onLeave = $holiday = 0;
                $row = [
                    $student->name,
                    optional($student->roomAssignments->first()->room ?? null)->room_no,
                    $student->email,
                    optional($student->roomAssignments->first()->room->hostel ?? null)->name,
                ];
                foreach ($dates as $date) {
                    $status = $attendanceData[$student->id][$date] ?? null;
                    if ($status === 'Taken') $present++;
                    elseif ($status === 'Skipped') $absent++;
                    elseif ($status === 'On Leave') $onLeave++;
                    elseif ($status === 'Holiday') $holiday++;
                    if ($status === 'Taken') $row[] = 'Present';
                    elseif ($status === 'Skipped') $row[] = 'Absent';
                    elseif ($status === 'On Leave') $row[] = 'On Leave';
                    elseif ($status === 'Holiday') $row[] = 'Holiday';
                    else $row[] = '-';
                }
                $total = count($dates);
                $percent = $total ? round(($present / $total) * 100) : 0;
                if ($percent >= 90) $statusText = 'Excellent';
                elseif ($percent >= 75) $statusText = 'Good';
                else $statusText = 'Poor';
                $row = array_merge($row, [$present, $absent, $onLeave, $holiday, $percent . '%', $statusText]);
                $rows[] = $row;
            }
            // Output CSV
            return response()->stream(function() use ($columns, $rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
                fclose($out);
            }, 200, $headers);
        }
        if ($request->input('download') === 'pdf' && count($students) && count($dates)) {
            // For wide tables, use landscape and A3
            ini_set('memory_limit', '512M');
            set_time_limit(120);
            $pdf = \PDF::loadView('warden.attendance-report-pdf', [
                'students' => $students,
                'dates' => $dates,
                'attendanceData' => $attendanceData
            ])->setPaper('a3', 'landscape');
            return $pdf->download('attendance_report_' . now()->format('Ymd_His') . '.pdf');
        }

        if ($request->input('download') === 'csv' && $request->input('attendance_type') === 'meal' && count($students) && count($dates)) {
            $dateFromStr = $dateFrom ?? ($dates[0] ?? '');
            $dateToStr = $dateTo ?? (end($dates) ?: '');
            $filename = 'meal_attendance_report_' . $dateFromStr . '_to_' . $dateToStr . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $columns = ['S.No', 'Name', 'Email', 'Room', 'Hostel'];
            foreach ($dates as $date) {
                $columns[] = \Carbon\Carbon::parse($date)->format('d M Y');
            }
            $columns[] = 'Report';
            $rows = [];
            $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
            $serial = 1;
            foreach ($students as $student) {
                $row = [
                    $serial++,
                    $student->name,
                    $student->email,
                    optional($student->roomAssignments->first()->room ?? null)->room_no,
                    optional($student->roomAssignments->first()->room->hostel ?? null)->name,
                ];
                foreach ($dates as $date) {
                    $meals = $mealAttendanceMatrix[$student->id][$date] ?? [];
                    $cell = '';
                    foreach ($mealTypes as $mealType) {
                        $short = substr($mealType,0,1);
                        $status = $meals[$mealType] ?? null;
                        if ($status === 'Taken') $cell .= "$short-P ";
                        elseif ($status === 'Skipped') $cell .= "$short-A ";
                        elseif ($status === 'On Leave') $cell .= "$short-L ";
                        elseif ($status === 'Holiday') $cell .= "$short-H ";
                        else $cell .= "$short-N ";
                    }
                    $row[] = trim($cell);
                }
                $summary = $mealAttendanceSummary[$student->id] ?? ['present'=>0,'total'=>0];
                if ($summary['total'] > 0) {
                    $row[] = $summary['present'] . '/' . $summary['total'] . ' meals taken (' . round(($summary['present']/$summary['total'])*100) . '% present)';
                } else {
                    $row[] = 'No meals marked';
                }
                $rows[] = $row;
            }
            return response()->stream(function() use ($columns, $rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
                fclose($out);
            }, 200, $headers);
        }
        if ($request->input('download') === 'pdf' && $request->input('attendance_type') === 'meal' && count($students) && count($dates)) {
            $dateFromStr = $dateFrom ?? ($dates[0] ?? '');
            $dateToStr = $dateTo ?? (end($dates) ?: '');
            $filename = 'meal_attendance_report_' . $dateFromStr . '_to_' . $dateToStr . '.pdf';
            ini_set('memory_limit', '512M');
            set_time_limit(120);
            $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
            $serial = 1;
            $tableRows = [];
            foreach ($students as $student) {
                $row = [
                    $serial++,
                    $student->name,
                    $student->email,
                    optional($student->roomAssignments->first()->room ?? null)->room_no,
                    optional($student->roomAssignments->first()->room->hostel ?? null)->name,
                ];
                foreach ($dates as $date) {
                    $meals = $mealAttendanceMatrix[$student->id][$date] ?? [];
                    $cell = [];
                    foreach ($mealTypes as $mealType) {
                        $short = substr($mealType,0,1);
                        $status = $meals[$mealType] ?? null;
                        if ($status === 'Taken') $cell[] = "$short-P";
                        elseif ($status === 'Skipped') $cell[] = "$short-A";
                        elseif ($status === 'On Leave') $cell[] = "$short-L";
                        elseif ($status === 'Holiday') $cell[] = "$short-H";
                        else $cell[] = "$short-N";
                    }
                    $row[] = implode(' ', $cell);
                }
                $summary = $mealAttendanceSummary[$student->id] ?? ['present'=>0,'total'=>0];
                if ($summary['total'] > 0) {
                    $row[] = $summary['present'] . '/' . $summary['total'] . ' meals taken (' . round(($summary['present']/$summary['total'])*100) . '% present)';
                } else {
                    $row[] = 'No meals marked';
                }
                $tableRows[] = $row;
            }
            $pdf = \PDF::loadView('warden.attendance-report-meal-pdf', [
                'students' => $students,
                'dates' => $dates,
                'mealAttendanceMatrix' => $mealAttendanceMatrix,
                'mealAttendanceSummary' => $mealAttendanceSummary,
                'tableRows' => $tableRows,
            ])->setPaper('a3', 'landscape');
            return $pdf->download($filename);
        }

        return view('warden.attendance-report', compact('hostels', 'students', 'dates', 'attendanceData', 'selectedHostel', 'mealAttendanceReport', 'mealAttendanceMatrix', 'mealAttendanceSummary'));
    }
} 