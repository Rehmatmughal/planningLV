@extends('layout')

@section('content')
<div class="container mt-4">
    {{-- Success / Errors --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3>Plot: <span class="text-primary">{{ $plot->plot_number }}</span></h3>
            <p class="mb-0">
                <strong>Project:</strong> {{ $plot->project->project_name ?? '-' }} |
                <strong>Block:</strong> {{ $plot->block->block_name ?? '-' }} |
                <strong>Street:</strong> {{ $plot->street->street_name ?? '-' }}
            </p>
            <p class="mb-0">
                <strong>Size (nominal):</strong> {{ $plot->size ?? '-' }}
            </p>
        </div>

        <div class="text-end">
            <a href="{{ route('plots.index') }}" class="btn btn-secondary btn-sm">Back to Plots</a>
            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">Print</button>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs" id="plotTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="development-tab" data-bs-toggle="tab" data-bs-target="#development" type="button" role="tab">Development</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="lop-tab" data-bs-toggle="tab" data-bs-target="#lop" type="button" role="tab">LOP</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="mortgage-tab" data-bs-toggle="tab" data-bs-target="#mortgage" type="button" role="tab">Mortgage</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="possession-tab" data-bs-toggle="tab" data-bs-target="#possession" type="button" role="tab">Possession</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="area-tab" data-bs-toggle="tab" data-bs-target="#area" type="button" role="tab">Area Variations</button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4" id="plotTabsContent">
        {{-- DEVELOPMENT TAB --}}
        <div class="tab-pane fade show active" id="development" role="tabpanel">
            <form action="{{ route('development.store') }}" method="POST">
                @csrf
                <input type="hidden" name="plot_id" value="{{ $plot->id }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Sewer Manholes</label>
                        <select name="sewer_manholes" class="form-select" required>
                            <option value="constructed" {{ ($plot->developmentStatus->sewer_manholes ?? '') == 'constructed' ? 'selected' : '' }}>Constructed</option>
                            <option value="not_constructed" {{ ($plot->developmentStatus->sewer_manholes ?? '') == 'not_constructed' ? 'selected' : '' }}>Not Constructed</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Asphalt / TST</label>
                        <select name="asphalt_tst" class="form-select" required>
                            <option value="yes" {{ ($plot->developmentStatus->asphalt_tst ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ ($plot->developmentStatus->asphalt_tst ?? '') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Overall Status</label>
                        <select name="overall_status" class="form-select" required>
                            <option value="developed" {{ ($plot->developmentStatus->overall_status ?? '') == 'developed' ? 'selected' : '' }}>Developed</option>
                            <option value="under_development" {{ ($plot->developmentStatus->overall_status ?? '') == 'under_development' ? 'selected' : '' }}>Under Development</option>
                            <option value="not_developed" {{ ($plot->developmentStatus->overall_status ?? '') == 'not_developed' ? 'selected' : '' }}>Not Developed</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2">{{ $plot->developmentStatus->remarks ?? '' }}</textarea>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save Development Status</button>
                    <a href="#" class="btn btn-outline-secondary" onclick="location.reload()">Reset</a>
                </div>
            </form>
        </div>

        {{-- LOP TAB --}}
        <div class="tab-pane fade" id="lop" role="tabpanel">
            <form action="{{ route('lop.store') }}" method="POST">
                @csrf
                <input type="hidden" name="plot_id" value="{{ $plot->id }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">LOP Status</label>
                        <select name="lop_status" class="form-select" required>
                            <option value="lop" {{ ($plot->lopStatus->lop_status ?? '') == 'lop' ? 'selected' : '' }}>LOP</option>
                            <option value="non_lop" {{ ($plot->lopStatus->lop_status ?? '') == 'non_lop' ? 'selected' : '' }}>Non-LOP</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $plot->lopStatus->remarks ?? '' }}">
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">Save LOP Status</button>
                </div>
            </form>
        </div>

        {{-- MORTGAGE TAB --}}
        <div class="tab-pane fade" id="mortgage" role="tabpanel">
            <form action="{{ route('mortgage.store') }}" method="POST">
                @csrf
                <input type="hidden" name="plot_id" value="{{ $plot->id }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Is Mortgaged?</label>
                        <select name="is_mortgaged" class="form-select" required>
                            <option value="yes" {{ ($plot->mortgageStatus->is_mortgaged ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ ($plot->mortgageStatus->is_mortgaged ?? '') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $plot->mortgageStatus->remarks ?? '' }}">
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">Save Mortgage Status</button>
                </div>
            </form>
        </div>

        {{-- POSSESSION TAB --}}
        <div class="tab-pane fade" id="possession" role="tabpanel">
            <form action="{{ route('possession.store') }}" method="POST">
                @csrf
                <input type="hidden" name="plot_id" value="{{ $plot->id }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Possession Status</label>
                        <select name="possession_status" class="form-select" required>
                            <option value="possessionable" {{ ($plot->possessionStatus->possession_status ?? '') == 'possessionable' ? 'selected' : '' }}>Possessionable</option>
                            <option value="non_lop_possessionable" {{ ($plot->possessionStatus->possession_status ?? '') == 'non_lop_possessionable' ? 'selected' : '' }}>Non-LOP Possessionable</option>
                            <option value="under_development_possessionable" {{ ($plot->possessionStatus->possession_status ?? '') == 'under_development_possessionable' ? 'selected' : '' }}>Under Development Possessionable</option>
                            <option value="not_possessionable" {{ ($plot->possessionStatus->possession_status ?? '') == 'not_possessionable' ? 'selected' : '' }}>Not Possessionable</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $plot->possessionStatus->remarks ?? '' }}">
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">Save Possession Status</button>
                </div>
            </form>
        </div>

        {{-- AREA VARIATIONS TAB --}}
        <div class="tab-pane fade" id="area" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h5>Add New Area Measurement</h5>
                    <form action="{{ route('area_variations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plot_id" value="{{ $plot->id }}">

                        <div class="mb-3">
                            <label class="form-label">Nominal Area (from plot)</label>
                            <input type="text" class="form-control" value="{{ $plot->size ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Measured Area (sqyd)</label>
                            <input type="number" step="0.01" name="measured_area" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Measured By</label>
                            <input type="text" name="measured_by" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Measured Date</label>
                            <input type="date" name="measured_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>

                        <button class="btn btn-primary" type="submit">Save Measurement</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h5>Previous Area Measurements</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Measured Area</th>
                                    <th>Measured By</th>
                                    <th>Measured Date</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plot->areaVariations->sortByDesc('measured_date') as $k => $av)
                                    <tr>
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $av->measured_area }}</td>
                                        <td>{{ $av->measured_by ?? '-' }}</td>
                                        <td>{{ $av->measured_date ?? $av->created_at->format('d-M-Y') }}</td>
                                        <td>{{ $av->remarks ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No measurements yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end area --}}
    </div>
</div>
@endsection
