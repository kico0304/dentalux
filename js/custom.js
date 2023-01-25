/* shorten text */
$(".article-text>p").each(function(){
	var innerText = $(this)[0].outerHTML;
	console.log(innerText);
	var newText = innerText.slice(0,150);
	$(this).html(newText+"...");
});

$(".singleAlbum-imagePart").each(function(){
	var sirinaElementa = $(this).outerWidth();
	sirinaElementa = 3*(sirinaElementa/4)
	$(this).outerHeight(sirinaElementa);
});