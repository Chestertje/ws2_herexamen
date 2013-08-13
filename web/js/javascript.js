if(getURLParameter('update')==='true'){
    var n = noty({text: 'Vastgoed is aangepast!',layout: 'top',timeout:5000, type: 'success' });
};
if(getURLParameter('add')==='true'){
    var n = noty({text: 'Vastgoed is toegevoegd!',layout: 'top',timeout:5000, type: 'success' });
}
if(getURLParameter('delete')==='true'){
    var n = noty({text: 'Vastgoed is verwijderd!',layout: 'top',timeout:5000, type: 'success' });
}
if(getURLParameter('profile')==='true'){
    var n = noty({text: 'Profiel is aangepast!',layout: 'top',timeout:5000, type: 'success' });
}
function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}
$('.pagination .disabled a, .pagination .active a').on('click', function(e) {
    e.preventDefault();
});

/*var mycontent='<div class="btn-group"> <button class="btn">Left</button> <button class="btn">Middle</button> <button class="btn">Right</button> </div>'

$('.edit').popover({
    html: true,
    content:mycontent,
    trigger: 'manual'
}).click(function(e) {
    e.preventDefault();
     $('.edit').popover('hide');
    $(this).popover('toggle');
    e.stopPropagation();
});
*/
//$(".multi-upload").attr('name', $(".multi-upload").attr('name')+"[]");
$(".delete").click(function (e) {
    
    $.noty.closeAll();
    e.preventDefault();
    var id = $(this).attr('data-vastgoed-id');
    $test = noty({
        text: 'Ben je zeker dat je dit vastgoed wilt verwijderen?',
        layout: 'center',
        type: 'warning',
        buttons: [
          {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                $noty.close();
                console.log(id);
                $.ajax({
                    url: "delete?js=true&id="+id,
                    context: document.body,
                    success: function(){
                      noty({text: 'Vastgoed is Verwijderd!',layout: 'top',timeout:5000, type: 'success' });
                      $('#item-'+id).slideUp(400);
                    },
                    error: function(){
                      noty({text: 'Fout bij verwijderen',layout: 'top',timeout:5000, type: 'error' });
                    }
                });
            }
          },
          {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
              $noty.close();
            }
          }
        ]
    });
    
    $test.close();
    
    
});
$("#addForm_vastgoed_type").change(function () {
     var keuze = $("#addForm_vastgoed_type :selected").text();
    if(keuze ==="Grond" || keuze ==="Kot/Kamer" || keuze==="Bedrijfsvastgoed"|| keuze==="Garage"){
        $("#woningen").slideUp(400);
    }
    else{
        $("#woningen").slideDown(400);
    }
        
});
$("#editForm_vastgoed_type").change(function () {
     var keuze = $("#editForm_vastgoed_type :selected").text();
    if(keuze ==="Grond" || keuze ==="Kot/Kamer" || keuze==="Bedrijfsvastgoed"|| keuze==="Garage"){
        $("#woningen").slideUp(400);
    }
    else{
        $("#woningen").slideDown(400);
    }
        
});
$('#myTab a').click(function ($e) {
  $e.preventDefault();
  if(CheckFrom()===0){
    $(this).tab('show');
  };
});
 
$(function () {
        $("[data-toggle='tooltip']").tooltip();
});
var $currentTab = 1;
$('a[data-toggle="tab"]').on('shown', function ($e) {
    $currentTab = $(this).attr("href");
    $currentTab = $currentTab.replace('#', '');
    $currentTab = parseInt($currentTab);  
    console.log($($e.relatedTarget).attr('href'));
   // $($e.relatedTarget).tab('show')
});
$(".next").click(function (e) {
    e.preventDefault();
    
    if(CheckFrom()===0){
        $currentTab++;
        $('#myTab li:eq('+ ($currentTab-1) + ') a').tab('show');
        $('.active input[required="required"]').popover('destroy');
    }
    
});
$(".prev").click(function (e) {
    e.preventDefault();
    
        $currentTab--;
        $('#myTab li:eq('+ ($currentTab-1) + ') a').tab('show');
    
});
function CheckFrom(){
    var errors = 0;
    if($('.active input[required="required"]') !== 0){
        $('.active input[required="required"]').popover('destroy');
        $('a[data-toggle="tab"]').parent().popover('destroy');
        $('.active input[required="required"]').each(function( index ) {
        //console.log($('.active input[required="required"]:eq(' + index + ')').val());
        if($('.active input[required="required"]:eq(' + index + ')').val()===''){
            $('.active input[required="required"]:eq(' + index + ')').popover({trigger: 'manual', content:'Gelieve dit veld in te vullen'});
            $('.active input[required="required"]:eq(' + index + ')').popover('show');
            
            var tab_id = $('.active input[required="required"]:eq(' + index + ')').closest( '.tab-pane').attr('id');
            $('a[data-toggle="tab"][href="#'+ tab_id +'"]').parent().popover({placement	:'top' , trigger: 'manual', content:'Je bent hier iets vergeten!'});
                $('a[data-toggle="tab"][href="#'+ tab_id +'"]').parent().popover('show');
            errors++;
        }
        });
        
        
    }
    return errors;
}