<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    @include('head_use')
    <title>留言板</title>
</head>
<body>
    @include('top_box')
    @include('message_box')

    <section class="py-5 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                @if($search == '')
                    <h1 class="fw-light">留言板</h1>
                @else
                    <h1 class="fw-light">搜尋：{{ $search }}</h1>
                @endif
                <div>
                    <form action="{{ route('msg.index') }}">
                        @csrf
                        <input class="form-control" type="text" id="site-search" name="search" placeholder="輸入標題" value="{{ $search }}"></label>
                        <button class="btn btn-primary my-2">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <main class="container">
        {{ $message_boards->onEachSide(2)->links('vendor.pagination.bootstrap-5') }}
            @foreach($message_boards as $message_board)
                @if($message_board->is_del == 0)
                    <div class="d-flex align-items-center p-3 my-3 text-white rounded shadow-sm" style="background-color: #6f42c1;">
                        <div class="lh-1">
                            <h1 class="h6 mb-0 text-white lh-1">樓：{{ $message_board->id }}</h1>
                        </div>
                    </div>
                    <div class="my-3 p-3 bg-body rounded shadow-sm">
                        <a href="{{ route('reply.show', $message_board->id) }}">
                            <h6 class="border-bottom pb-2 mb-0">[{{ $message_board->subtitle }}] {{ $message_board->title }}</h6>
                        </a>
                        <div class="d-flex text-body-secondary pt-3">
                            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false">
                                <rect width="100%" height="100%" fill="#007bff"></rect>
                            </svg>
                            <p class="pb-3 mb-0 small lh-sm border-bottom">
                                <strong class="d-block text-gray-dark">{{ $message_board->user_name }}</strong>
                            </p>
                        </div>
                        <div style="overflow:hidden; white-space: nowrap; text-overflow: ellipsis;">
                            <pre style="overflow:hidden; white-space: nowrap; text-overflow: ellipsis;">{{ $message_board->content }}</pre>
                        </div>
                        <small class="d-block text-end mt-3">
                            <span>建立日期：{{ $message_board->created_at }}</span> |
                            <span>修改日期：{{ $message_board->updated_at }}</span>
                        </small>
                    </div>
                @else
                    <div class="d-flex align-items-center p-3 my-3 text-white rounded shadow-sm" style="background-color: #6f42c1;">
                        <div class="lh-1">
                            <h1 class="h6 mb-0 text-white lh-1">樓：{{ $message_board->id }}</h1>
                        </div>
                    </div>
                    <div class="my-3 p-3 bg-body rounded shadow-sm">
                        <a href="{{ route('reply.show', $message_board->id) }}">
                            <h6 class="border-bottom pb-2 mb-0">留言已刪除</h6>
                        </a>
                        <small class="d-block text-end mt-3">
                            <span>刪除日期：{{ $message_board->updated_at }}</span>
                        </small>
                    </div>
                @endif
            @endforeach
        {{ $message_boards->onEachSide(2)->links('vendor.pagination.bootstrap-5') }}
    </main>
</body>
</html>