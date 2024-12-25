<div class="d-flex justify-content-around">

    <div>
        <a href="{{ route('links.edit', $link->id) }}" class="btn btn-sm btn-warning">Edit</a>
    </div>
    <div>
        <form action="{{ route('links.destroy', $link->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="Submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </div>


</div>
