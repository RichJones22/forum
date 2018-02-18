@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h1>
                        {{ $profileUser->name }}
                    </h1>
                </div>

                @forelse($activities as $date => $activity)
                    <h3 class="page-header">{{ $date }}</h3>

                    @foreach($activity as $record)
                        @if(view()->exists("profiles.activities.{$record->type}"))
                            @include("profiles.activities.{$record->type}", ['activity' => $record])
                        @else
                            <strong>{{ "missing view: profiles.activities.{$record->type}" }}</strong>
                        @endif
                    @endforeach
                @empty
                    <p>No activity for this user...</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection