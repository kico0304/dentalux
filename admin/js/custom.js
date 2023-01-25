/* MAIN WRAPPER HEIGHT */
/* visina headera */
var visinaHeadera = $("header.topbar").innerHeight();
/* visina footera */
var visinaFootera = $("footer.footer").innerHeight();
/* zbir visina za kalkulaciju */
if(visinaHeadera == undefined){
  var headerAndFooter = visinaFootera + 1;
} else{
  var headerAndFooter = visinaHeadera + visinaFootera + 1;
}
/* postavi visinu contenta */
$('.page-wrapper, .left-sidebar , .login-wrapper').css('height','calc(100vh - '+headerAndFooter+'px)');

/* --------------- */

/* loader hide */
$(window).on('load', function() {
  $('#loader_').fadeOut( "slow", function() {
    // Animation complete.
  });
 });
// setTimeout(function() {
//     $('#loader_').fadeOut( "slow", function() {
//         // Animation complete.
//       });
// }, 1000);

$(document).on("DOMNodeInserted", "", function() {

  $(".deleteThisImage").click(function(){
      $(this).parent().remove();
  });

});

$(".deleteItemNews>i").click(function(){
  var dataID_ = $(this).attr("dataID_");
  //var dataName_ = $(this).attr("dataName_");
  var dataLink = $(this).attr("dataLink");
  $("input#dataID_").val(dataID_);
  //$("input#dataName_").val(dataName_);
  $("#confirmDeleteNews").attr('href', dataLink);
});

$(".deleteItemGallery>i").click(function(){
  //var dataID_ = $(this).attr("dataID_");
  //var dataName_ = $(this).attr("dataName_");
  var dataLink = $(this).attr("dataLink");
  //$("input#dataID_").val(dataID_);
  //$("input#dataName_").val(dataName_);
  $("#confirmDeleteGallery").attr('href', dataLink);
});

$(".deletewholealbum").click(function(){
  //var dataID_ = $(this).attr("dataID_");
  //var dataName_ = $(this).attr("dataName_");
  var dataLink = $(this).attr("dataLink");
  //$("input#dataID_").val(dataID_);
  //$("input#dataName_").val(dataName_);
  $("#confirmDeleteAlbum").attr('href', dataLink);
});