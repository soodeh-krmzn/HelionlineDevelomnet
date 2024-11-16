<div id="edit-meta-form">
    <h5 class="text-center" >تغییر بازه زمانی</h5>
    <div class="row">
        <div class="alert alert-danger" id="edit-meta-error" style="display: none">

        </div>
        <input type="hidden" name="meta_id" value="{{$meta->id}}">
        <div class="col-6">
            <div class="form-group">
                <label class="form-label">شروع</label>
                <input type="text" readonly class="form-control timePicker" value="{{timeFormat($meta->start,1)->format('H:i')}}" name="start">
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label class="form-label">پایان</label>
                <input type="text" placeholder="پایان" readonly class="form-control timePicker" value="{{timeFormat($meta->end,1)?->format('H:i')}}" name="end">
                {{-- <input type="text" placeholder="پایان" readonly class="form-control timePicker" value="{{verta($meta->end)}}" name="end"> --}}
            </div>
        </div>
        <div class="col-12 text-center mt-2">
            <button class="btn btn-primary" id="store-meta-period-btn">اعمال</button>
        </div>
    </div>
</div>
