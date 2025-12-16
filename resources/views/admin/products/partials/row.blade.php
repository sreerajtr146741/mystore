@use('Illuminate\Support\Facades\Storage')
@php
    $resolveImg = function($path){
        if(!$path) return null;
        if(filter_var($path, FILTER_VALIDATE_URL)) return $path;
        return Storage::url($path);
    };
@endphp
@forelse($products as $p)
@php
    $img = $resolveImg($p->image);
    $isActive = ($p->status === 'active') || ($p->is_active == 1);
@endphp
<tr>
    <td>#{{ $p->id }}</td>
    <td>
        @if($img)
            <img src="{{ $img }}" class="img-thumb" alt="Product">
        @else
            <div class="img-thumb bg-secondary d-flex align-items-center justify-content-center text-white-50">N/A</div>
        @endif
    </td>
    <td>{{ $p->name }}</td>
    <td>{{ $p->category }}</td>
    <td class="text-end">₹{{ number_format($p->price,2) }}</td>
    <td class="text-end">{{ $p->stock }}</td>
    <td>
        @if($isActive)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Hidden</span>
        @endif
    </td>
    <td>{{ $p->user->name ?? '—' }}</td>
    <td class="text-end">
        <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-del btn-act" onclick="return confirm('Delete this product?')">
                Delete
            </button>
        </form>
        <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-edit btn-act">
            Edit
        </a>
    </td>
</tr>
@empty
@endforelse
