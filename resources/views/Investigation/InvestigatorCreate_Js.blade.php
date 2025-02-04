<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    // ========== Form Submission ========= Start =========
    
    // ================= Activities ========= end =========
    function replaceNumbers(input) {
        
        var numbers = {
            0: '০',
            1: '১',
            2: '২',
            3: '৩',
            4: '৪',
            5: '৫',
            6: '৬',
            7: '৭',
            8: '৮',
            9: '৯'
        };
        var output = [];
        for (var i = 0; i < input.length; ++i) {
            
            if (numbers.hasOwnProperty(input[i])) {
                output.push(numbers[input[i]]);
            } else {
                output.push(input[i]);
            }
        }
        // return output;
        return output.join('');
    }
    // ============= Add Attachment Row ========= start =========
    $("#addFileRow").click(function(e) {
        addFileRowFunc();
    });
    //add row function
    function addFileRowFunc() {
        var count = parseInt($('#other_attachment_count').val());
        $('#other_attachment_count').val(count + 1);
        var slshow =""+count+"";
        slshow = NumToBangla.replaceNumbers(slshow);
        var items = '';
        items += '<tr>';
        items += '<td>'+  slshow  +'</td>';
        items += '<td><input type="text" name="file_type[]" class="form-control form-control-sm" placeholder="ফাইলের নাম দিন" id="file_name_important' + count + '" ></td>';

        items += '<td><div class="custom-file"><input type="file" name="file_name[]" onChange="attachmentTitle(' +
            count + ',this)" class="custom-file-input" id="customFile' + count +
            '" /><label class="custom-file-label custom-input' + count + '" for="customFile' + count +
            '">ফাইল নির্বাচন করুন </label></div></td>';
        items += '<td><input type="text" name="single_comment[]" class="form-control form-control-sm" placeholder="মন্তব্য" id="single_comment' + count + '" ></td>';
        items +=
            '<td width="40"><a href="javascript:void();" class="btn btn-sm btn-danger font-weight-bolder pr-2" onclick="removeBibadiRow(this)"> <i class="fas fa-minus-circle"></i></a></td>';
        items += '</tr>';
        $('#fileDiv tr:last').after(items);
    }
    //Attachment Title Change
    function attachmentTitle(id,obj) {
        // var value = $('#customFile' + id).val();
        var value = $('#customFile' + id)[0].files[0];
        
        const fsize  = $('#customFile' + id)[0].files[0].size;
        const file_size = Math.round((fsize / 1024));
                
        var file_extension=value['name'].split('.').pop().toLowerCase();      
        
        if($.inArray(file_extension, ['pdf','docx']) == -1) {
            Swal.fire(
                        
                        'ফাইল ফরম্যাট PDF,docx হতে হবে ',
                        
                        )
                $(obj).closest("tr").remove();
            }
            if (file_size > 30720 ) {
                Swal.fire(
                        
                        'ফাইল সাইজ অনেক বড় , ফাইল সাইজ ১৫ মেগাবাইটের কম হতে হবে',
                        
                        )
                $(obj).closest("tr").remove();
            }
            
            var custom_file_name=$('#file_name_important'+id).val();
            if(custom_file_name =="")
            {
                Swal.fire(
                        
                        'ফাইল এর প্রথমে যে নাম দেয়ার field আছে সেখানে ফাইল এর নাম দিন ',
                        
                        )
                $(obj).closest("tr").remove();
            }



        // console.log(value['name']);
        $('.custom-input' + id).text(value['name']);
    }
    //remove Attachment
    function removeBibadiRow(id) {
        $(id).closest("tr").remove();
    }
    // =============== Add Attachment Row ===================== end =========================
   
    $('#main_file_input').on('change',function(){
        attachmentTitleMainReport();
    });
    
    $(document).ready(function(){
        $("#step1Content").load(location.href + " #step1Content");
    });

    function attachmentTitleMainReport() {
        // var value = $('#customFile' + id).val();
        var value = $('#main_file_input')[0].files[0];
        
        const fsize  = $('#main_file_input')[0].files[0].size;
        const file_size = Math.round((fsize / 1024));
                
        var file_extension=value['name'].split('.').pop().toLowerCase();      
        //alert(value['name']);
        if($.inArray(file_extension, ['pdf','docx','doc']) == -1) {
            Swal.fire(
                        
                     'ফাইল ফরম্যাট PDF, docx, doc হতে হবে ',
                        
                        );

                       $('#main_file_input').val('');
                       $("#step1Content").load(location.href + " #step1Content");
                       
            }
            else if (file_size > 30720 ) {
                Swal.fire(
                        
                        'ফাইল সাইজ অনেক বড় , ফাইল সাইজ ১৫ মেগাবাইটের কম হতে হবে',
                        
                        );

                        $('#main_file_input').val('');
                        $("#step1Content").load(location.href + " #step1Content");
                        
            }
            
           else
           {
               
               $('.custom-input').text(value['name']); 
           }

        
       
    }
    
        $( document ).ready(function() {
            $('#home input,#applicant input,#victim input,#deafulter input,#witness input,#lawyer input').prop('disabled', true);
            $('#home select,#applicant select,#victim select,#deafulter select,#witness select,#lawyer select').prop('disabled', true);
            $('#home textarea,#applicant textarea,#victim textarea,#deafulter textarea,#witness textarea,#lawyer textarea').prop('disabled', true);
        });
    

</script>


