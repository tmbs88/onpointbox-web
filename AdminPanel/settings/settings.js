/*
 * OnPointBox a schedule manager for group classes
 * Copyright (C) 2015  Tiago Miguel Basilio da Silva
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @author Tiago Miguel Basilio Silva
 */

	
$(document).ready(function(){

	getset();
	
	$('#fp').on('submit', function(ev){
		var oData = new FormData(this);
	
		var oReq = new XMLHttpRequest();
		oReq.open("POST", "/php/h_user_changep.php", true);
		oReq.onload = function(oEvent) {
			if (oReq.status == 200) {
				var response = JSON.parse(oReq.responseText);
  				
  				if(!response.error){
  					div = $("<div>").attr("class","alert alert-success").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					cbutton.append(span);
					div.append(cbutton);
					div.append("Password changed successfully!");
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					
					$('#fp').find('#fgwarning').remove();
					$('#fp').prepend(fgdiv);
					
  					var fade_out = function() {
						$('#fp').find('#fgwarning').fadeOut().empty();
					};
					setTimeout(fade_out, 5000);
  					
					$('#fiold').val('');
					$('#finew').val('');
						
				}else{
					//handles error!!
					div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					strong = $("<strong>").append("Error: ");
					cbutton.append(span);
					div.append(cbutton);
					div.append(strong);
					div.append(response.error_message);
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					$('#fp').prepend(fgdiv);
					
				}
				
  			} else {
			}
		};
		
		oReq.send(oData);
		ev.preventDefault();
	});
	
	
	
	$('#fta').on('submit', function(ev){
		var oData = new FormData(this);
	
		
		var oReq = new XMLHttpRequest();
		oReq.open("POST", "/php/h_def_change.php", true);
		oReq.onload = function(oEvent) {
	  		if (oReq.status == 200) {
	    		var response = JSON.parse(oReq.responseText);

				if(!response.error){
					div = $("<div>").attr("class","alert alert-success").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					cbutton.append(span);
					div.append(cbutton);
					div.append("Data changed successfully!");
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					
					$('#fta').parent().find('#fgwarning').remove();
					$('#fta').parent().prepend(fgdiv);
					
  					var fade_out = function() {
						$('#fta').parent().find('#fgwarning').fadeOut().empty();
					};
					setTimeout(fade_out, 5000);
					
					getset();
					
				}else{
					div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					strong = $("<strong>").append("Error: ");
					cbutton.append(span);
					div.append(cbutton);
					div.append(strong);
					div.append(response.error_message);
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					$('#fta').parent().prepend(fgdiv);
					
				}
		    } else {
		    }
		};
	
		oReq.send(oData);
		ev.preventDefault();
	});
	
	$('#fli').on('submit', function(ev){
		var oData = new FormData(this);
	
		var oReq = new XMLHttpRequest();
		oReq.open("POST", "/php/h_def_change.php", true);
		oReq.onload = function(oEvent) {
			if (oReq.status == 200) {
				var response = JSON.parse(oReq.responseText);
  				  
  				if(!response.error){
					
					div = $("<div>").attr("class","alert alert-success").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					cbutton.append(span);
					div.append(cbutton);
					div.append("Data changed successfully!");
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					
					$('#fli').parent().find('#fgwarning').remove();
					$('#fli').parent().prepend(fgdiv);
					
  					var fade_out = function() {
						$('#fli').parent().find('#fgwarning').fadeOut().empty();
					};
					setTimeout(fade_out, 5000);
					
					getset();
					
				}else{
					div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					strong = $("<strong>").append("Error: ");
					cbutton.append(span);
					div.append(cbutton);
					div.append(strong);
					div.append(response.error_message);
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					$('#fli').parent().prepend(fgdiv);
					
				}
  			} else {
			}
		};
		
		oReq.send(oData);
		ev.preventDefault();
	});
	
	
	
});
	
function getset(){
	$.ajax({
		url: "/php/h_def_data.php",
		type : "post",
		datatype: "json"
	}).done(function(data) {
		var parsed = JSON.parse(data);
		if(!parsed.error){
			
			$('#fta-d').val(parsed.ta);
			$('#fli-d').val(parsed.li.d);
			$('#fli-h').val(parsed.li.h);
			$('#fli-m').val(parsed.li.m);
			
		}
	}).fail(function() {
	});
}

function ajaxSelectList(rop) {
	return $.ajax({
		url: "/php/h_trainee.php",
		type : "POST",
		data : { "option" : "selectlist",
				 "radio" : rop,
				 "type" : "Trainee" },
		datatype: "json" 
	}).done(function(dates) {
		var parsed = JSON.parse(dates);
		
		acparent = $('#acomp').parent();
		$('#acomp').remove();
		acparent.append("<input type='text' class='form-control' placeholder='Search trainee' id='acomp'>");
				
		$('#acomp').autocomplete({
		limit:5,
  		valueKey:'title',
	  	source:[{
	  		data:parsed,
	  		
			getTitle:function(item){
				return item.title;
			},
			getValue:function(item){
				return item.title;
			}
			}]
		}).on('selected.xdsoft',function(e,datum){
			
			$.ajax({
				url: "/php/h_trainee.php",
				type : "post",
				data : { "option" : "selectid",
						 "uid" : datum.id },
				datatype: "json"
			}).done(function(datass) {
				var parsedd = JSON.parse(datass);
				
				$('#iimg').attr("src", "/"+parsedd.img);
				$('#iname').text(parsedd.name);
				$('#idtborn').text(parsedd.dtnasc);
				if(parsedd.gender=="M") {$('#igender').text("Male");}
				if(parsedd.gender=="F") {$('#igender').text("Female");}
				if(parsedd.gender=="") {$('#igender').text("");}
				$('#iemail').text(parsedd.email);
				$('#iphone').text(parsedd.phone);
				$('#istate').prop('checked', parsedd.state);
				$('#idp').show();
				$('#btn_e').show();
				$('#btn_a').show();
				
				
			}).fail(function() {
			});
			
		});
		
	}).fail(function() {
	});
}

function ajaxDeleteUser(email) {
	return $.ajax({
		url: "/php/h_trainee.php",
		type : "POST",
		data : { "option" : "delete",
				 "uemail" : email },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
		if(!response.error){
			window.location.reload(true);
		}
	}).fail(function() {
	});
}

function ajaxUpdateState(email) {
	return $.ajax({
		url: "/php/h_trainee.php",
		type : "POST",
		data : { "option" : "editstate",
				 "uemail" : email },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
		if(response.db==0){
			$("#radmail").prop( "checked", true );
			$("#radmail").click();
			$('#acomp').val(email).trigger('paste.xdsoft').trigger('pick.xdsoft');
		}
	}).fail(function() {
	});
}


