@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/chat/chat.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
    <script src="{{ asset('administrator/chat/chat.js') }}"></script>
    <script>
        var pusher = new Pusher('af88ad31025c923bf4f8', {
            forceTLS: true,
            cluster: 'ap1'
        });
        var channel = pusher.subscribe('chat-with-admin');
        channel.bind('chat-client', function(data) {
            let countMessageNew = parseInt($('#header_inbox_bar .badge').text());
            $('#header_inbox_bar .badge').text(countMessageNew + 1);
            let activeEl = $('#contacts').find('.contact.active');
            if(activeEl.length) {
                if (activeEl.data('id') == data.user.id) {
                    $(`<li class="replies">
                            <span>
                                <img src="${ data.user.avatar !== null ? data.user.avatar : '/storage/avatar/default.jpg' }"
                                    alt="" />
                            </span>
                            <p>${ data.message.message }</p>
                        </li>`).appendTo('#frame .content .messages ul');
                    activeEl.find('.preview').text(data.message.message);
                }
            } else {
                $(`<li data-contact="${ data.message.id }" data-id="${ data.user.id }" class="contact">
                    <div class="wrap">
                        <span class="contact-status online"></span>
                        <img src="${ data.user.avatar !== null ? data.user.avatar : '/storage/avatar/default.jpg' }" alt="" />
                        <div class="meta">
                            <p class="name">${ data.user.name }</p>
                            <p class="preview not-read">${ data.message.message }</p>
                        </div>
                    </div>
                </li>`).appendTo('#contacts ul');
            }
        });
    </script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">Chat</li>
                    </ul>
                </div>
            </div>
            <div id="frame" class="panel flex-between">
                <div id="sidepanel">
                    <div id="search">
                        <label><i class="fa fa-search" aria-hidden="true"></i></label>
                        <input type="text" placeholder="Search Customer..." />
                    </div>
                    <div id="contacts">
                        <ul>
                            @if ($userChats !== null)
                                @foreach ($userChats as $chat)
                                    <li data-contact="{{ $chat->id }}"
                                        data-id="{{ $chat->repfor == 0 ? $chat->user_id : $chat->repfor }}"
                                        class="contact {{ $chat->repfor == 0 ? ($chat->user_id == $user->id ? 'active' : '') : ($chat->repfor == $user->id ? 'active' : '') }}">
                                        <div class="wrap">
                                            @php
                                                $userId = $chat->repfor == 0 ? $chat->user_id : $chat->repfor;
                                            @endphp
                                            @if (Cache::has('user-is-online-' . $userId))
                                                <span class="contact-status online"></span>
                                            @else
                                                <span class="contact-status"></span>
                                            @endif
                                            <img src="{{ \App\Models\User::find($userId)->avatar !== null ? \App\Models\User::find($userId)->avatar : '/storage/avatar/default.jpg' }}" alt="" />
                                            <div class="meta">
                                                <p class="name">{{ \App\Models\User::find($userId)->name }}</p>
                                                <p class="preview @if ($chat->read == 0) not-read @endif">{{ $chat->message }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="content">
                    <div class="contact-profile" data-id="{{ $user->id }}">
                        @if ($user !== null)
                            <img src="{{ $user->avatar !== null ? $user->avatar : '/storage/avatar/default.jpg' }}" alt="" />
                            <p>{{ $user->name }}</p>
                        @endif
                    </div>
                    <div class="messages">
                        <ul>
                            @if ($activeChats != null)
                                @foreach ($activeChats as $chat)
                                    <li class="{{ $chat->type == 'admin' ? 'sent' : 'replies' }}">
                                        <span>
                                            <img src="{{ $chat->type == 'client' ? ($chat->user->avatar !== null ? $chat->user->avatar : '/storage/avatar/default.jpg') : asset('guest/images/logo/logo.png') }}"
                                                alt="" />
                                        </span>
                                        <p>{{ $chat->message }}</p>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <form class="message-input" data-action="{{ route('admin.chat.store') }}">
                        @csrf
                        <div class="wrap flex-between">
                            <input hidden type="text" name="user_id" value="{{ $user !== null ? $user->id : null }}" />
                            <input class="message-description" type="text" name="message"
                                placeholder="Write your message..." />
                            <button class="submit" type="submit"><i class="fa fa-location-arrow"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </section>
@endsection
