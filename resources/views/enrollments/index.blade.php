@extends('layouts.app')

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Enrollment Management</h1>
                    <p>Assign students to courses and optionally attach trainers.</p>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Assign Enrollment</h2>
            </div>
            <form method="POST" action="{{ route('enrollments.store') }}" class="stack">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label>Course</label>
                        <select name="course_id" required>
                            <option value="">Select course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Student</label>
                        <select name="student_id" required>
                            <option value="">Select student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Trainer</label>
                        <select name="trainer_id">
                            <option value="">No trainer</option>
                            @foreach ($trainers as $trainer)
                                <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="actions-row">
                    <button class="btn" type="submit">Assign Enrollment</button>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Assigned Enrollments</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Course</th>
                        <th>Student</th>
                        <th>Trainer</th>
                        <th>Assigned By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->course?->title }}</td>
                            <td>{{ $enrollment->student?->name }}</td>
                            <td>{{ $enrollment->trainer?->name ?? 'Not Assigned' }}</td>
                            <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                            <td>
                                <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit" onclick="return confirm('Remove enrollment?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No enrollments yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
