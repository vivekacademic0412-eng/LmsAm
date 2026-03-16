@extends('layouts.app')

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Student Courses</h1>
                    <p>All courses are visible category-wise. You can open only your enrolled courses.</p>
                </div>
            </div>
        </section>

        @foreach ($categories as $category)
            <section class="card">
                <div class="page-head">
                    <h2>{{ $category->name }}</h2>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($category->courses as $course)
                            @php $enrolled = in_array($course->id, $enrolledCourseIds, true); @endphp
                            <tr>
                                <td>{{ $course->title }}</td>
                                <td>
                                    @if ($enrolled)
                                        <a class="btn" href="{{ route('student.courses.show', $course) }}">Open Enrolled Course</a>
                                    @else
                                        <span class="muted">Locked (not enrolled)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No courses in this category.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>
@endsection
