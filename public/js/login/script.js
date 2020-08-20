$('document').ready(function(){
    $('input[tag="login"]').click();
    $('input:radio').on('change',function(){

        if($(this)[0].attributes[1].nodeValue=='register'){
            $('.regform_container').css('display','block')
            $('.logform_container').css('display','none')
        }else{
            $('.logform_container').css('display','block')
            $('.regform_container').css('display','none')
        }
    })

})