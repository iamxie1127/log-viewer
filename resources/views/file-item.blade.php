@if(is_array($file))
    <li>
        <span>{{ $key }}<span>
        <ul>
            @foreach($file['values'] as $key => $value)
                @include('laravel-admin-logs::file-item', ['file' => $value, 'dir' => $file['dir'], 'key' => $key])
            @endforeach
        </ul>
    </li>
@else
    <li>
        <a href="{{ route('log-viewer-file', ['dir' => $dir, 'file' => $file]) }}">{{ $file }}</a>
    </li>
@endif
