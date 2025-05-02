<div wire:ignore class="trix-container">
    <input id="{{ $trixId }}" type="hidden" name="content" value="{{ $value }}">
    <trix-editor input="{{ $trixId }}"></trix-editor>
</div>
@pushonce('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.1.14/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.1.14/dist/trix.umd.min.js"></script>
@endpushonce
@pushonce('scripts')
    @script
        <script>
            document.addEventListener('livewire:navigated', () => {
                addEventListener('trix-change', function(event) {
                    const content = event.target.value;
                    @this.set('value', content);
                });
                // document.addEventListener('trix-file-accept', function(event) {
                //     event.preventDefault();
                // });
            }, { once: true });
        </script>
    @endscript
@endpushonce
