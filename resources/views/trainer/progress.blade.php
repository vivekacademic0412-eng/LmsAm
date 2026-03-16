@extends('layouts.app')

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Trainer Student Progress</h1>
                    <p>You can only view students assigned to you and track their course progress.</p>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Completed Items</th>
                        <th>Total Items</th>
                        <th>Progress</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['enrollment']->student?->name }}</td>
                            <td>{{ $row['enrollment']->course?->title }}</td>
                            <td>{{ $row['completed_items'] }}</td>
                            <td>{{ $row['total_items'] }}</td>
                            <td>{{ $row['progress_percent'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No students assigned to you.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
