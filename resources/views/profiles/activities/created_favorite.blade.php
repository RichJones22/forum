@component('profiles.activities.activity')

    @if( ! is_null($activity->subject->favorited))
        @slot('heading')
            <a href="{{ $activity->subject->favorited->path() }}">
                {{ $profileUser->name }} favorited a reply.
            </a>
        @endslot
    @else
        @slot('heading')
            "heading not found..."
        @endslot
    @endif

    @slot('body')
        @if( ! is_null($activity->subject->favorited))
            {{ $activity->subject->favorited->body }}
        @else
            "body not found..."
        @endif
    @endslot
@endcomponent