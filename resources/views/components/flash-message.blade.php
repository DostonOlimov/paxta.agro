@if($translatedMessage)
    <div class="row massage">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="alert alert-success text-center">
                <label for="checkbox-10 colo_success">{{ $translatedMessage }}</label>
            </div>
        </div>
    </div>

    @if($downloadUrl)
        <script>
            window.onload = function() {
                window.location.href = "{{ $downloadUrl }}";
            };
        </script>
    @endif
@endif
