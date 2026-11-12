<div class="book-card">
    <div class="book-card-header">
        <strong>{{ $judul }}</strong>
    </div>

    <div class="book-card-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="book-card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>