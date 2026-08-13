@extends('layout')

@section('content')
{{-- old system --}}
{{-- <div style="width: 100%">
    <iframe 
        width="100%" 
        height="600"
        src="https://maps.google.com/maps?width=100%25&height=600&hl=en&q={{ $coordinate->latitude }},{{ $coordinate->longitude }}&t=k&z=17&ie=UTF8&iwloc=B&output=embed">
    </iframe>
</div> --}}

{{-- new with error coordinate --}}
{{-- @if($coordinate)
<div style="width: 80%">
    <iframe 
        width="80%" 
        height="400"
        src="https://maps.google.com/maps?width=100%25&height=600&hl=en&q={{ $coordinate->latitude }},{{ $coordinate->longitude }}&t=k&z=17&ie=UTF8&iwloc=B&output=embed">
    </iframe>
</div>
@else
    <p>Coordinates not found.</p>
@endif --}}
{{-- change with label etc --}}
<div class="container mt-4">

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <strong>Plot Location Map</strong>
        </div>

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-4">
                    <label class="form-label"><b>Project</b></label>
                    <input type="text" class="form-control" 
                        {{-- value="{{ $plot->block->project->name ?? '' }}" readonly> --}}
                        value="{{ $project ?? '' }}" readonly>
                       
                </div>

                <div class="col-md-4">
                    <label class="form-label"><b>Block</b></label>
                    <input type="text" class="form-control" 
                        {{-- value="{{ $plot->block->name ?? '' }}" readonly> --}}
                        value="{{ $block ?? '' }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><b>Plot No</b></label>
                    <input type="text" class="form-control" 
                        {{-- value="{{ $plot->plot_no ?? '' }}" readonly> --}}
                        value="{{ $plot_no ?? '' }}" readonly>
                </div>

            </div>

        </div>
    </div>


{{--    @if($coordinate)
    <div class="card shadow-sm">
        <div class="card-body">

            <iframe 
                width="100%" 
                height="500"
                style="border:0"
                src="https://maps.google.com/maps?hl=en&q={{ $coordinate->latitude }},{{ $coordinate->longitude }}&t=k&z=17&ie=UTF8&iwloc=B&output=embed">
            </iframe>

        </div>
    </div> --}}

    {{-- @else

        <div class="alert alert-warning">
            Coordinates not found.
        </div>

    @endif

</div> --}}
{{-- new for share location --}}
    @if($coordinate)

        @php
        $lat = $coordinate->latitude;
        $lng = $coordinate->longitude;

        $mapLink = "https://www.google.com/maps?q=$lat,$lng";
        $directionLink = "https://www.google.com/maps/dir/?api=1&destination=$lat,$lng";
        @endphp

        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">

                <a href="{{ $mapLink }}" target="_blank" class="btn btn-primary me-2">
                    Open in Google Maps
                </a>

                <a href="{{ $directionLink }}" target="_blank" class="btn btn-success me-2">
                    Get Directions
                </a>

                <button class="btn btn-secondary" onclick="copyLocation()">
                    Copy Location Link
                </button>

            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <iframe 
                    width="100%" 
                    height="500"
                    style="border:0"
                    src="https://maps.google.com/maps?hl=en&q={{ $lat }},{{ $lng }}&t=k&z=17&ie=UTF8&iwloc=B&output=embed">
                </iframe>

            </div>
        </div>

    @else

        <div class="alert alert-warning">
            Coordinates not found.
        </div>

    @endif

</div>

    <script>

        function copyLocation() {

            const link = "https://www.google.com/maps?q={{ $coordinate->latitude ?? '' }},{{ $coordinate->longitude ?? '' }}";

            navigator.clipboard.writeText(link);

            alert("Location link copied!");

        }

    </script>


{{-- end new for share location --}}

{{-- new with advance features --}}
{{-- <div id="map" style="width:100%; height:600px;"></div>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAP_KEY"></script>

<script>
function initMap() {

    var latitude = {{ $coordinate->latitude ?? 0 }};
    var longitude = {{ $coordinate->longitude ?? 0 }};

    var plotLocation = {
        lat: parseFloat(latitude),
        lng: parseFloat(longitude)
    };

    var map = new google.maps.Map(document.getElementById("map"), {
        zoom: 17,
        center: plotLocation,
        mapTypeId: 'satellite'
    });

    var marker = new google.maps.Marker({
        position: plotLocation,
        map: map,
        title: "Plot Location"
    });

    var infoWindow = new google.maps.InfoWindow({
        content: "<b>Plot Location</b><br>Lat: " + latitude + "<br>Lng: " + longitude
    });

    marker.addListener("click", function () {
        infoWindow.open(map, marker);
    });
}

window.onload = initMap;
</script> --}}

@endsection