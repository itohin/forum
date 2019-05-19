<div class="card mb-3">
    <div class="card-header">
        <div class="level">
            <h5 class="flex">
                <a href="#">{{ $reply->owner->name }}</a>
                said {{ $reply->created_at->diffForHumans() }}
            </h5>
            <div>
                <form action="{{route('favorites.reply', $reply)}}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                        {{ $reply->favorites()->count() }} {{ \Illuminate\Support\Str::plural('Favorite', $reply->favorites()->count()) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>