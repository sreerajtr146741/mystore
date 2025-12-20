@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Messages</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $msg->first_name }} {{ $msg->last_name }}</div>
                                    <div class="small text-muted">{{ $msg->email }}</div>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                        {{ $msg->subject }}
                                    </span>
                                </td>
                                <td>{{ $msg->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($msg->replies->count() > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Replied</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">New</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                            data-bs-toggle="modal" data-bs-target="#msgModal{{$msg->id}}">
                                        {{ $msg->replies->count() > 0 ? 'View Thread' : 'View' }}
                                    </button>
                                    <form action="{{ route('admin.messages.destroy', $msg->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="msgModal{{$msg->id}}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $msg->subject }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <small class="text-muted fw-bold">From:</small>
                                                <div>{{ $msg->first_name }} {{ $msg->last_name }} &lt;{{ $msg->email }}&gt;</div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted fw-bold">Sent:</small>
                                                <div>{{ $msg->created_at->format('F d, Y h:i A') }}</div>
                                            </div>
                                            <hr>
                                            <p class="mb-0" style="white-space: pre-line;">{{ $msg->message }}</p>

                                            @if($msg->replies->count() > 0)
                                                <div class="mt-4 pt-3 border-top">
                                                    <h6 class="small fw-bold text-muted mb-3">Admin Replies</h6>
                                                    <div class="d-flex flex-column gap-3">
                                                        @foreach($msg->replies as $reply)
                                                            <div class="p-3 bg-white border rounded">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <small class="fw-bold">{{ $reply->subject }}</small>
                                                                    <small class="text-muted">{{ $reply->created_at->format('M d, h:i A') }}</small>
                                                                </div>
                                                                <p class="mb-0 small text-muted" style="white-space: pre-line;">{{ $reply->message }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            
                                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm{{$msg->id}}">
                                                Reply via Email
                                            </button>
                                        </div>
                                        <div class="collapse p-3 bg-light border-top" id="replyForm{{$msg->id}}">
                                            <form action="{{ route('admin.messages.reply', $msg->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Subject</label>
                                                    <input type="text" name="subject" class="form-control" value="Re: {{ $msg->subject }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Message</label>
                                                    <textarea name="message" class="form-control" rows="4" required></textarea>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-success">Send Reply <i class="bi bi-send"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No messages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
                <div class="p-3 border-top">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
