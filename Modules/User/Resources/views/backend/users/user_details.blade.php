<div class="d-flex gap-3 align-items-center">
    <img src="{{ setBaseUrlWithFileName($data->file_url) }}" alt="avatar" class="avatar avatar-40 rounded-pill">
    <div class="text-start">
        <h6 class="m-0">{{ $data->full_name ?? default_user_name() }}

            @if ($data->email_verified_at)
                <i class="ph ph-check-circle" style="color: rgba(19, 109, 0, 0.863);"></i>
            @else
                <i class="ph ph-x-circle" style="color: rgba(112, 3, 3, 0.842);"></i>
            @endif

        </h6>
        <span>{{ $data->email ?? '--' }}</span>
    </div>
</div>
