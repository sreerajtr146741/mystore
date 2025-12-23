@extends('layouts.admin')

@section('content')
<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0"><i class="bi bi-chat-left-text-fill me-2 text-info"></i>User Inquiries</h2>
        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Support Intelligence</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-20 text-success alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-glass overflow-hidden shadow-lg animate__animated animate__fadeIn">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead>
                        <tr class="text-white-50 border-bottom border-white border-opacity-10">
                            <th class="ps-4 py-3">Sender</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($messages as $msg)
                            <tr class="border-bottom border-white border-opacity-5 hover-bg-glass">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar shadow-sm border-info border-opacity-25" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                            {{ mb_substr($msg->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white">{{ $msg->first_name }} {{ $msg->last_name }}</div>
                                            <div class="small text-white-50">{{ $msg->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-white-75" style="max-width: 250px;">
                                        {{ $msg->subject }}
                                    </span>
                                </td>
                                <td class="text-white-50 small">{{ $msg->created_at->format('M d, Y • h:i A') }}</td>
                                <td>
                                    @if($msg->replies->count() > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="bi bi-check2-all me-1"></i>Replied
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="bi bi-envelope-fill me-1"></i>New Inquiry
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-glass text-white px-3" 
                                                data-bs-toggle="modal" data-bs-target="#msgModal{{$msg->id}}">
                                            <i class="bi bi-eye-fill me-1"></i>{{ $msg->replies->count() > 0 ? 'Thread' : 'Open' }}
                                        </button>
                                        <form action="{{ route('admin.messages.destroy', $msg->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-glass text-danger px-3 border-start-0" onclick="return confirm('Delete this message permanently?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="msgModal{{$msg->id}}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                    <div class="modal-content" style="background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.15);">
                                        <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                                                    <i class="bi bi-chat-quote-fill fs-4"></i>
                                                </div>
                                                <h5 class="modal-title fw-bold text-white mb-0">{{ $msg->subject }}</h5>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4" style="max-height: 60vh; overflow-y: auto;">
                                            <div class="row g-4 mb-4">
                                                <div class="col-md-6">
                                                    <label class="small text-info text-uppercase tracking-wider fw-bold">Sender Details</label>
                                                    <div class="text-white fw-bold">{{ $msg->first_name }} {{ $msg->last_name }}</div>
                                                    <div class="text-info small">{{ $msg->email }}</div>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <label class="small text-info text-uppercase tracking-wider fw-bold">Timestamp</label>
                                                    <div class="text-white">{{ $msg->created_at->format('F d, Y • h:i A') }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1);">
                                                <label class="small text-info text-uppercase tracking-wider fw-bold mb-2">Message Content</label>
                                                <p class="mb-0 lh-lg" style="white-space: pre-line; color: #ffffff;">{{ $msg->message }}</p>
                                            </div>

                                            @if($msg->replies->count() > 0)
                                                <div class="mt-4 pt-4 border-top border-white border-opacity-10">
                                                    <h6 class="text-info text-uppercase tracking-widest fw-bold mb-3 small"><i class="bi bi-reply-all-fill me-2"></i>Official Responses</h6>
                                                    <div class="d-flex flex-column gap-3">
                                                        @foreach($msg->replies as $reply)
                                                            <div class="p-3 rounded-4" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2);">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <small class="fw-bold text-info">{{ $reply->subject }}</small>
                                                                    <small class="text-white-50">{{ $reply->created_at->format('M d, h:i A') }}</small>
                                                                </div>
                                                                <p class="mb-0 small" style="white-space: pre-line; color: #f0f0f0;">{{ $reply->message }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-top border-white border-opacity-10 p-4 justify-content-between">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close Window</button>
                                            
                                            <button class="btn btn-info rounded-pill px-4 fw-bold shadow-lg" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm{{$msg->id}}">
                                                <i class="bi bi-reply-fill me-1"></i>Send Response
                                            </button>
                                        </div>
                                        <div class="collapse p-4 border-top border-info border-opacity-10" style="background: rgba(6, 182, 212, 0.05);" id="replyForm{{$msg->id}}">
                                            <form action="{{ route('admin.messages.reply', $msg->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label small text-info fw-bold">Response Subject</label>
                                                    <input type="text" name="subject" class="form-control text-white" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2);" value="Re: {{ $msg->subject }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small text-info fw-bold">Message Content</label>
                                                    <textarea name="message" class="form-control text-white" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2);" rows="4" placeholder="Type your response here..." required></textarea>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold">Send Reply <i class="bi bi-send-fill ms-1"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-white-50">
                                    <div class="mb-3"><i class="bi bi-mailbox fs-1 opacity-25"></i></div>
                                    Your support inbox is currently empty.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
                <div class="p-4 border-top border-white border-opacity-10">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
