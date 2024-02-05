$(function() {
	window.current_div = null;
	window.page = null;
	function UrlExists(url, cb){
		jQuery.ajax({
			url:      url,
			type:     'GET',
			complete:  function(xhr){
				if(xhr.status === 200){
					cb.attr('src', url);
					cb.removeClass('fiximageclass');
				}else{
					cb.addClass('fiximageclass');
				}
			}
		});
	}
	window.slidnext = function(){
		$(".remainingC").html('');
		var div   	= $("fieldset.active");
		var radios	= div.find("input[type='radio']");
		
		var names 	= [];
		var i = 0;
		var current_page;
		var myAjax = $('input[name="ajaxurl"]').val();
		if($('fieldset.active #finishbtn').length == 0){
		}else{
			$.ajax({
				type : "post",
				dataType : "json",
				url : myAjax,
				data : {action: "tf_reviewsystemresponsestore", form_data : $('#msform').serialize(),tf_reviewsystem_id : $('input[name="tf_reviewsystem_id"]').val() },
				success: function(response) {
					if(response.success == true){
						document.cookie = "enable="+$('input[name="tf_reviewsystem_id"]').val();
						$('#fix_image').hide();
						$('.maintf_reviewsystemcontent').hide();
						$('.thankyoutf_reviewsystem').show();
					}
				}
			});
		}
		$("fieldset.active input[type='radio']").each(function() {
			if ($("input[name='"+ $(this).attr('name') +"']:checked").length == 0){ 
				alert('Please Select One response');
				throw new Error('Please Select One response');
			}
		});
		$('#top').removeAttr('id');
		if(animating) return false;
		animating = true;
		current_fs = $("#msform fieldset.active");
		next_fs = current_fs.next();
		current_fs.removeClass("active");
		next_fs.addClass("active");
		next_fs.attr('id','top');
		logos = $('.logos');
		$('#tf_reviewsystemcontainer').height(next_fs.outerHeight());
		next_fs.show(); 
		current_fs.animate({opacity: 0}, {
			start: function(){
				if ($("fieldset.active .OpenEnded").length == 1) {
					$(".nextquestion").show();
				}else{
					$(".nextquestion").hide();
				}
				//console.log($("fieldset.active .OpenEnded").length);
				window.page = $(".active .currentpage").val();
				window.current_div = $('#fix_image').position().top;
				// console.log(window.page);
				if (window.page != undefined) {
					current_page = $('#current_page').val();
					var fix_image = $('#fix_image');
					if(current_page != window.page){
						if ($('#Image'+ window.page).attr('src') != undefined) {
							fix_image.attr('src', $('#Image'+ window.page).attr('src'));
						}else{
							fix_image.attr('src', "");
						}
						// console.log($('#Image'+ window.page).attr('src'))
						// UrlExists('https://tf_reviewsystem.techforceinfotech.in/demo/images/'+ window.page +'.png', fix_image );
						fix_image.attr('src', $('#Image'+ window.page).attr('src'));
						$('#current_page').val(window.page);
					}
				}
			},
			step: function(now, mx) {
				scale = 1 - (1 - now) * 0.2;
				left = (now * 50)+"%";
				opacity = 1 - now;
				current_fs.css({'transform': 'scale('+scale+')'});
				next_fs.css({'left': left, 'opacity': opacity});
				logos.css({'left': left, 'opacity': opacity});
			}, 
			duration: 1200, 
			complete: function(){
				current_fs.hide();
				animating = false;
				if($('#top').length == 0){
				}else{
					$("html, body").animate({ scrollTop: 200 }, "slow");	
				}
			}, 
			easing: 'easeInOutBack'
		});
	}
	$( window ).resize(function() {
		$('#tf_reviewsystemcontainer').height($('#msform .active').outerHeight());
	});
	$('#tf_reviewsystemcontainer').height($('#msform .active').outerHeight());
	var current_fs, next_fs, previous_fs, logos;
	var left, opacity, scale; 
	var animating; 
	$("input[name='lang']").click(async function(){
		$('.glyphicon-volume-up').hide();
		if ($(this).val() == 'ur') {
			$('.glyphicon-volume-up').hide();
		}
		var lang = $(this).val();
		var defaultval = 'en';
		var translation_data = jQuery.parseJSON(atob($('#translation_data').val()));
		var tf_reviewsystem_translation_data = jQuery.parseJSON(atob($('#tf_reviewsystem_translation_data').val()));
		$('#current_language').val(lang);
		//console.log(lang);
		await $(".translate").each(function(index) {
			var t_type = $(this).data('key');
			switch(lang) {
				default:
				var translation = ['Welcometext', 'changelanguage','starttf_reviewsystem','previous','next','thankyoutext'];
				if(translation.indexOf($(this).data('id')) !== -1){
					if (tf_reviewsystem_translation_data.hasOwnProperty(lang)) {
						var t_text = $(this).data('id');
						if(tf_reviewsystem_translation_data[lang][t_text] != ""){
							if ( $(this).is( "input" ) ) {
								$(this).val(tf_reviewsystem_translation_data[lang][t_text]);
							}else{
								$(this).html(tf_reviewsystem_translation_data[lang][t_text]);
							}	
						}else{
							if ( $(this).is( "input" ) ) {
								$(this).val(tf_reviewsystem_translation_data[defaultval][t_text]);
							}else{
								$(this).html(tf_reviewsystem_translation_data[defaultval][t_text]);
							}
						}
					}else{
						if ( $(this).is( "input" ) ) {
							$(this).val(tf_reviewsystem_translation_data[defaultval][t_text]);
						}else{
							$(this).html(tf_reviewsystem_translation_data[defaultval][t_text]);
						}
					}
				} else if(t_type == "question"){
					var t_text = $(this).data('id');
					if (translation_data.hasOwnProperty(lang)) {
						if(translation_data[lang][t_text][t_type] != ""){
							$(this).html(translation_data[lang][t_text][t_type]);	
						}else{
							$(this).html(translation_data[defaultval][t_text][t_type])
						}
					}
				} else if(t_type == "options"){
					var t_text = $(this).data('id');
					var option = $(this).data('option');
			  	//console.log(translation_data.hasOwnProperty(lang));
			  	if (translation_data.hasOwnProperty(lang)) {
			  		//console.log(translation_data[lang][t_text][t_type]);
			  		if(translation_data[lang][t_text][t_type] != ""){
			  			var p = Object.values(translation_data[lang][t_text][t_type]);
			  			for ( var i = 0; i < p.length; i++) {
			  				if(option == i+1){
			  					if(p[i] != ''){
			  						$(this).text(p[i]);
			  					}else{
			  						$(this).text(translation_data[defaultval][t_text][t_type][i+1]);
			  					}
			  				}
			  			}
			  		}
			  	}
			  }
			}
		});
		slidnext();
	});
	$('.choice').click(function(){
		document.getElementById('questionAudio').pause();
		document.getElementById('optionAudio').pause();
		//document.getElementsByClassName("glyphicon-volume-up").style.color = '#6a6a6a';
		var spans = document.getElementsByClassName('glyphicon-volume-up');
		for (var i = 0; i < spans.length; i++) {
			var span = spans[i];
			span.style.color = '#6a6a6a';
		} 
		slidnext();
	});
	$("#starttf_reviewsystem").click(function(){
		document.getElementById('welcomeAudio').pause();
		var spans = document.getElementsByClassName('glyphicon-volume-up');
		for (var i = 0; i < spans.length; i++) {
			var span = spans[i];
			span.style.color = '#6a6a6a';
		} 
		slidnext();
	});
	$(".previous").click(function(){
		$(".remainingC").html('')
		document.getElementById('welcomeAudio').pause();
		document.getElementById('questionAudio').pause();
		document.getElementById('optionAudio').pause();
		var spans = document.getElementsByClassName('glyphicon-volume-up');
		for (var i = 0; i < spans.length; i++) {
			var span = spans[i];
			span.style.color = '#6a6a6a';
		} 
		if(animating) return false;
		animating = true;
		$('#top').removeAttr('id');
		current_fs = $(this).parent();
		previous_fs = $(this).parent().prev();
		logos = $('.logos');
		current_fs.removeClass("active");
		previous_fs.addClass("active");
		previous_fs.show(); 
		previous_fs.attr('id','top');
		// window.page = $("fieldset.active .currentpage").val();
		// window.current_div = $('#fix_image').position().top;
		// if (window.page != undefined) {
		// 	current_page = $('#current_page').val();
		// 	var fix_image = $('#fix_image');
		// 	if(current_page != window.page){
		// 		fix_image.attr('src', 'http://tf_reviewsystem.techforceinfotech.in/demo/images/'+ window.page +'.png');
		// 		$('#current_page').val(window.page);
		// 	}
		// }
		current_fs.animate({opacity: 0}, {
			start: function(){
				if ($("fieldset.active .OpenEnded").length == 1) {
					$(".nextquestion").show();
				}else{
					$(".nextquestion").hide();
				}
				window.page = $(".active .currentpage").val();
				window.current_div = $('#fix_image').position().top;
				if (window.page != undefined) {
					current_page = $('#current_page').val();
					var fix_image = $('#fix_image');
					if(current_page != window.page){
						if ($('#Image'+ window.page).attr('src') != undefined) {
							fix_image.attr('src', $('#Image'+ window.page).attr('src'));
						}else{
							fix_image.attr('src', "");
						}
						// console.log($('#Image'+ window.page).attr('src'))
						// UrlExists('https://tf_reviewsystem.techforceinfotech.in/demo/images/'+ window.page +'.png', fix_image );
						$('#current_page').val(window.page);
					}
				}
			},
			step: function(now, mx) {
				scale = 0.8 + (1 - now) * 0.2;
				left = ((1-now) * 50)+"%";
				opacity = 1 - now;
				current_fs.css({'left': left});
				previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
				$('#tf_reviewsystemcontainer').height(previous_fs.outerHeight());
				logos.css({'left': left, 'opacity': opacity});
			}, 
			duration: 1200, 
			complete: function(){
				current_fs.hide();
				animating = false;
				window.page = $(".active .currentpage").val();
				window.current_div = $('#fix_image').position().top;
				if(window.page == undefined){
					var fix_image = $('#fix_image');
					fix_image.attr('src', "");
					$('#current_page').val(window.page);
				}else{
					/*console.log("window.page");
					console.log(window.page);*/
					/*current_page = $('#current_page').val();
					var fix_image = $('#fix_image');
					if(current_page != window.page){
						if($('#Image'+ window.page).length == 0){
							fix_image.attr('src', "");
						}else{
							fix_image.attr('src', $('#Image'+ window.page).attr('src'));
						}
						// UrlExists('https://tf_reviewsystem.techforceinfotech.in/demo/images/'+ window.page +'.png', fix_image );
						$('#current_page').val(window.page);
					}*/
				}
				if($('#top').length == 0){
				}else{
					$("html, body").animate({ scrollTop: 200 }, "slow");	
				}
			}, 
			easing: 'easeInOutBack'
		});
	});
	$(".nextquestion").click(function(){
		slidnext();
	});
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	}
	window.onload = function(e) {
		var enable = getCookie("enable");
		if ((enable == $('input[name="tf_reviewsystem_id"]').val()) && (typeof $('input[name="tf_reviewsystem_id"]').val() !== 'undefined')) {
	        // $('#fix_image').hide();
	        $('.maintf_reviewsystemcontent').hide();
	        $('.thankyoutf_reviewsystem').show();
	        // window.location.href = "https://tf_reviewsystem.techforceinfotech.in/demo/thankyou.html";
	    } else {
	    	return true;
	    }
	};
	$(window).bind("pageshow", function(event) {
		if (event.originalEvent.persisted) {
			window.location.reload(); 
		}
	});
	$('.gts').click(function(){
		var questioId= $(this).data('id');
		var translation_data = jQuery.parseJSON(atob($('#translation_data').val()));
		var tf_reviewsystem_translation_data = jQuery.parseJSON(atob($('#tf_reviewsystem_translation_data').val()));
		var current_language = $('#current_language').val();
		if(translation_data[current_language][questioId]['question'] == ''){
			var current_language = 'en';
		}
		var options = [];
		var obj = translation_data[current_language][questioId]['options'];
		var enobj = translation_data['en'][questioId]['options'];
		jQuery.each(obj, function(i, val) {
			if(val == ''){
				options.push(enobj[i]);
			}else{
				options.push(val);
			}
		});
		var myAjax = $('input[name="ajaxurl"]').val();
		$.ajax({
			type : "post",
			dataType : "json",
			url : myAjax,
			data : {action: "textToSpeech",question : translation_data[current_language][questioId]['question'],current_language:current_language,options: options},
			success: function(response) {
				if(response.success == true){
					var questionAudio = document.getElementById('questionAudio');
					var questionplay = document.getElementById('questionAudioData'); 
					questionplay.src='data:audio/mpeg;base64,'+response.data.audiodata;
					questionAudio.load();
					var optionAudio = document.getElementById('optionAudio');
					var optionplay = document.getElementById('optionAudioData'); 
					optionplay.src='data:audio/mpeg;base64,'+response.data.audioContentOptions; 
					optionAudio.load();
					questionAudio.play();
					$(".glyphicon-volume-up").css("color", "#337ab7");
					questionAudio.onended = function() {
						setTimeout(function () {
							if (response.data.audioContentOptions != '') {
								optionAudio.play();
							}else{
								$(".glyphicon-volume-up").css("color", "#6a6a6a");
							}
						}, 400);		
					};
					optionAudio.onended = function() {
						$(".glyphicon-volume-up").css("color", "#6a6a6a");
					};
				}
			}
		});
	})
	$('.gtswt').click(function(){
		var translation_data = jQuery.parseJSON(atob($('#translation_data').val()));
		var tf_reviewsystem_translation_data = jQuery.parseJSON(atob($('#tf_reviewsystem_translation_data').val()));
		var current_language = $('#current_language').val();
		if(tf_reviewsystem_translation_data[current_language] == ''){
			var current_language = 'en';
		}
		var myAjax = $('input[name="ajaxurl"]').val();
		$.ajax({
			type : "post",
			dataType : "json",
			url : myAjax,
			data : {action: "textToSpeech",question : tf_reviewsystem_translation_data[current_language]["Welcometext"],current_language:current_language},
			success: function(response) {
				if(response.success == true){
					$(".glyphicon-volume-up").css("color", "#337ab7");
					var welcomeAudio = document.getElementById('welcomeAudio');
					var welcomeplay = document.getElementById('welcomeAudioData'); 
					welcomeplay.src='data:audio/mpeg;base64,'+response.data.audiodata;
					welcomeAudio.load();
					welcomeAudio.play();
					welcomeAudio.onended = function() {
						$(".glyphicon-volume-up").css("color", "#6a6a6a");
					};
				}
			}
		});
	})
	$('.gtstt').click(function(){
		var translation_data = jQuery.parseJSON(atob($('#translation_data').val()));
		var tf_reviewsystem_translation_data = jQuery.parseJSON(atob($('#tf_reviewsystem_translation_data').val()));
		var current_language = 'en';
		var current_language = $('#top').val();
		if(tf_reviewsystem_translation_data[current_language] == ''){
			var current_language = 'en';
		}
		if (typeof(current_language) == 'undefined') {
			var current_language = 'en';
		}
		var myAjax = $('input[name="ajaxurl"]').val();
		$.ajax({
			type : "post",
			dataType : "json",
			url : myAjax,
			data : {action: "textToSpeech",question : tf_reviewsystem_translation_data[current_language]["thankyoutext"],current_language:current_language},
			success: function(response) {
				if(response.success == true){
					$(".glyphicon-volume-up").css("color", "#337ab7");
					var thankAudio = document.getElementById('thankAudio');
					var thankplay = document.getElementById('thankAudioData'); 
					thankplay.src='data:audio/mpeg;base64,'+response.data.audiodata;
					thankAudio.load();
					thankAudio.play();
					thankAudio.onended = function() {
						$(".glyphicon-volume-up").css("color", "#6a6a6a");
					};
				}
			}
		});
	})
});
