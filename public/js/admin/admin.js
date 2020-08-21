$('document').ready(function(){
    $('.user_tools span').on('click',function(){
        var id = $(this)[0].parentNode.id;
        operationWithUser(id,$(this)[0].innerText);
    });
});

function operationWithUser(id,operation_name){
    $.ajax({
        url:"/changeuser",
        type:"post",
        data:{id:id,operation_name:operation_name}
    }).done(function(data){
       window.location.href = window.location.href;
    })
}