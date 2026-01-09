<div class="row py-5 col-4">

    <div class="col-6 position-relative">
        {{ html()->label(__('Content '.$index), 'selectedImageContainer')->class('form-label')}}
            <span class="badge bg-primary rounded-pill text-middle" style="position: absolute; right: 18px;">
                {{__('Votes')}}:{{$ranking->contents[$index]['votes']??'0'}}
            </span>
        <div class="input-group btn-file-upload">
            {{ html()->button(__('<i class="ph ph-image"></i>' . __('messages.lbl_choose_image')))
    ->class('input-group-text form-control')
    ->type('button')
    ->attribute('data-bs-toggle', 'modal')
    ->attribute('data-bs-target', '#exampleModal')
    ->attribute('data-image-container', 'selectedImageContainer' . $index)
    ->attribute('data-hidden-input',  'file_url'. $index)
            }}
            {{ html()->text('contents['.$index.'][image_url]')
    ->class('form-control')
    ->placeholder(__('placeholder.lbl_image'))
    ->attribute('aria-label', 'Image Input ' . $index)
    ->attribute('data-bs-toggle', 'modal')
    ->attribute('data-bs-target', '#exampleModal')
    ->attribute('data-image-container', 'selectedImageContainer' . $index)
    ->attribute('data-hidden-input', 'file_url1')
    ->attribute('aria-describedby', 'basic-addon' . $index)
            }}
            </div>
            <div class="mb-3 uploaded-image" id="selectedImageContainer{{ $index }}">
                @if ($ranking->contents[$index] ?? false)
                    <img src="{{ $ranking->contents[$index]['image_url'] ?? '' }}" class="img-fluid mb-2"
                        style="max-width: 100px; max-height: 100px;">
                    <span class="remove-media-icon"
                        style="cursor: pointer; font-size: 24px; position: absolute; top: 0; right: 0; color: red;"
                        onclick="removeImage('contents['.$index.'][image_url]', 'remove_image_flag{{ $index }}')">×</span>
                @endif
            </div>
            {{ html()->hidden('contents['.$index.'][image_url]')->id('file_url' . $index)->value($ranking->contents[$index]['image_url'] ?? '') }}
            {{ html()->hidden('contents['.$index.'][remove_image]')->id('remove_image_flag' . $index)->value(0) }}
        </div>
        <div class="col-6">
            <div class="col-12">

            {{ html()->hidden('contents['.$index.'][votes]')->id('remove_image_flag' . $index)->value($ranking->contents[$index]['votes'] ?? 0) }}
            {{ html()->hidden('contents['.$index.'][slug]')->value($ranking->contents[$index]['slug']??'') }}
                {{ html()->label(__(''), 'contents['.$index.'][name]')->class('form-label') }}
                {{
    html()->text('contents['.$index.'][name]', $ranking->contents[$index]['name'] ?? '')
        ->class('form-control')
        ->id('content_title_' . $index)
        ->placeholder(__('Content Title ' . $index))
        }}
                @error('contents.content_title_' . $index)
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-12">
                {{ html()->label(__(''), 'contents['.$index.'][description]')->class('form-label') }}
                {{
    html()->textarea('contents['.$index.'][description]', $ranking->contents[$index]['description'] ?? '')
        ->class('form-control')
        ->id('content_description_' . $index)
        ->placeholder(__('Content Description ' . $index))
        ->rows('5')
        }}
                @error('contents.content_description_' . $index)
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>