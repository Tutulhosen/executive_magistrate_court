<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">আদেশের প্রিভিউ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @if (globalUserInfo()->role_id == 27)
        <div class="modal-body">
          <div class="col-md-12" id="orderContaint"> </div>
        </div>
      @endif

      @if (globalUserInfo()->role_id == 38)
        <div class="modal-body">
          <div class="col-md-12" id=""> test order</div>
        </div>
      @endif
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">বন্ধ করুণ </button>
        <button type="button" class="btn btn-primary" onclick="myFunction()"> আদেশ সংরক্ষণ করুন</button>
      </div>
    </div>
  </div>
</div>