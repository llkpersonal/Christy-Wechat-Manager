$(document).ready(function(){
        var strLocate = window.location.pathname;
        if( strLocate=="/index.php"||strLocate=="/" ){
            $("#home").addClass("tab_selected");
        } else if ( strLocate.indexOf("/login.php")>=0 ){
            $("#login").addClass("tab_selected");
        } else if ( strLocate.indexOf("/links.php")>=0||strLocate.indexOf("/weconfig.php")>=0||strLocate.indexOf("/account.php")>=0
                  || strLocate.indexOf("/users.php")>=0){
            $("#manage").addClass("tab_selected");
        } else if ( strLocate.indexOf("/checkreply.php")>=0||strLocate.indexOf("/textreply.php")>=0||strLocate.indexOf('/musicreply.php')>=0
                   ||strLocate.indexOf("/pictextreply.php")>=0){
            $("#message").addClass("tab_selected");
        } else if ( strLocate.indexOf("plugin")>=0 ){
            $("#extend").addClass("tab_selected");
        }
    }
);

var displayReply = function(obj){
    $("#table_content").html("loading...");
    $.ajax({
        type:"GET",
        url:"ajax_getreply.php?wid="+obj.value,
        dataType:"text",
        success:function(data){
                    $("#table_content").html(data);
                }
    });
}

var display_plugin = function(obj){
    $("#table_content").html("loading...");
    $.ajax({
        type:"GET",
        url:"ajax_uninsplu.php?wid="+obj.value,
        dataType:"text",
        success:function(data){
                    $("#table_content").html(data);
                }
    });
}

var display_installed_plugin = function(obj){
    $("#table_content").html("loading...");
    $.ajax({
        type:"GET",
        url:"ajax_plugin.php?wid="+obj.value,
        dataType:"text",
        success:function(data){
                    $("#table_content").html(data);
                }
    });
}
