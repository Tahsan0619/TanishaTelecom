@extends('admin.layouts.app')

@section('title', 'Messages')

@section('content')
<div class="card">
    <div class="card-header"><h3>Contact form messages</h3></div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th class="text-right"></th></tr></thead>
            <tbody>
            @forelse($messages as $message)
                <tr class="{{ $message->is_read ? '' : 'unread' }}">
                    <td>{{ $message->name }}<br><small class="text-muted">{{ $message->email }}</small></td>
                    <td>{{ $message->subject ?: '(no subject)' }}</td>
                    <td class="text-muted">{{ \Illuminate\Support\Str::limit($message->message, 70) }}</td>
                    <td class="text-muted">{{ $message->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-outline btn-sm">Open</a>
                            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message?">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-envelope-open"></i><p>No messages yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
        <div class="pagination-bar"><span></span>{{ $messages->links() }}</div>
    @endif
</div>
@endsection
