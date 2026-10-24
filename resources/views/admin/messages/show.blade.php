@extends('admin.layouts.app')

@section('title', 'Message')

@section('content')
<div class="card" style="max-width:820px">
    <div class="card-header">
        <h3>{{ $message->subject ?: '(no subject)' }}</h3>
        <span class="text-muted">{{ $message->created_at->format('d M Y, h:i A') }}</span>
    </div>
    <div class="card-body">
        <dl class="detail-list" style="margin-bottom:18px">
            <div><dt>Name</dt><dd>{{ $message->name }}</dd></div>
            <div><dt>Email</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd></div>
            @if($message->phone)<div><dt>Phone</dt><dd><a href="tel:{{ $message->phone }}">{{ $message->phone }}</a></dd></div>@endif
        </dl>
        <div class="message-body">{{ $message->message }}</div>

        <div class="form-actions" style="margin-top:20px">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'Your message')) }}" class="btn btn-primary"><i class="fa-solid fa-reply"></i> Reply by email</a>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message?">
                @csrf @method('DELETE')
                <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
