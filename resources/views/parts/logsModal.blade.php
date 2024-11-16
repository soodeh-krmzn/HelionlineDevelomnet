{{--
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
  </button> --}}

<!-- Modal -->
<div class="modal fade" id="log-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="exampleModalLabel">{{$logInfo->title}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body py-0">
              {!!$logInfo->text!!}
            </div>
            <hr>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success"
                    data-bs-dismiss="modal">@lang('این اطلاعیه را خواندم')</button>
            </div>
        </div>
    </div>
</div>

