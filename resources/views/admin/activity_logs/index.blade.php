@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Activity Logs</h2>

    <!-- Filters -->
    <form method="GET" class="row mb-4">

        <div class="col-md-3">
            <label>User</label>
            <select name="user_id" class="form-control">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" 
                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Model</label>
            <select name="model" class="form-control">
                <option value="">All Models</option>
                @foreach($models as $model)
                    <option value="{{ $model }}" 
                        {{ request('model') == $model ? 'selected' : '' }}>
                        {{ class_basename($model) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label>To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>

    <!-- Table -->
    <div class="card">
        <div class="card-body">

            <a href="{{ route('activity.logs.export') }}" 
            class="btn btn-success mb-3">
            Export to Excel
            </a>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Description</th>
                        <th>Changes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ optional($activity->causer)->name ?? 'System' }}</td>
                            <td>{{ $activity->event }}</td>
                            <td>{{ class_basename($activity->subject_type) }}</td>
                            <td>{{ $activity->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No activity found</td>
                        </tr>
                    @endforelse --}}
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ optional($activity->causer)->name ?? 'System' }}</td>
                            <td>{{ ucfirst($activity->event) }}</td>
                            <td>{{ class_basename($activity->subject_type) }}</td>
                            <td>{{ $activity->description }}</td>

                            <td>
                                @if(isset($activity->properties['attributes']))
                                    @foreach($activity->properties['attributes'] as $key => $value)

                                        <strong>{{ $key }}</strong> :

                                        @if(isset($activity->properties['old'][$key]))
                                            <span class="text-danger">
                                                {{ $activity->properties['old'][$key] }}
                                            </span>
                                            →
                                        @endif

                                        <span class="text-success">{{ $value }}</span>
                                        <br>

                                    @endforeach
                                @endif
                            </td>

                            <td>
                                <button class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#logModal{{ $activity->id }}">
                                    View
                                </button>
                            </td>
                        </tr>
                        <!-- 👇 YAHAN MODAL CODE LAGANA HAI -->
                        <div class="modal fade" id="logModal{{ $activity->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Activity Detail</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <p><strong>User:</strong> {{ optional($activity->causer)->name ?? 'System' }}</p>
                                        <p><strong>Model:</strong> {{ class_basename($activity->subject_type) }}</p>
                                        <p><strong>Event:</strong> {{ $activity->event }}</p>
                                        <p><strong>Date:</strong> {{ $activity->created_at }}</p>

                                        <hr>

                                        <h6>Raw Properties</h6>
                                        <pre>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>

                                    </div>

                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                        <td colspan="7" class="text-center">No activity found</td>
                        </tr>
                    @endforelse
                        

                </tbody>
            </table>

            {{ $activities->withQueryString()->links() }}

        </div>
    </div>

</div>
@endsection
