	// set browser back button off
	var ie = (window.navigator.appName == "Microsoft Internet Explorer") ? true : false;
// 	alert("ie="+ie);
	function setEventByObject(object, event, func){
 		if (!ie){
 			object.addEventListener(event, func, false);
		} else {
 			object.attachEvent("on" + event, func);
		}
	}
 
	setEventByObject(window, "unload", exitme);
	
//	var block = "false";
// 	function blockBackButton(){
// 		block = "true";
// 	}
//	function resetBackButton(){
//		block = "false";
//	}
	var block = "true";
	
	function jumpforward(){
//		if(window.location.href.indexOf("&jumpforward")!=-1) {
//			history.forward();
//		}
		alert("jumpforward1");
		if(window.location.href.indexOf("jumpforward")!=-1) { 
			alert("jumpforward2");
			history.forward();
		 }
	}
//	jumpforward();
//	function exitme(){
//		if(block == "true") window.location.href += "&jumpforward";
//	}
	function exitme(){
		alert("block="+block+",href="+window.location.href);
		if(block == "true") {
			if (window.location.href.indexOf("?")==-1) {
				alert("1:"+window.location.href);
				window.location.href += "?jumpforward";
				alert("11:"+window.location.href);
			} else if (window.location.href.indexOf("jumpforward")==-1) {
				alert("2:"+window.location.href);
				window.location.href += "&jumpforward";
				alert("22:"+window.location.href);
			}
			alert("Please, please don't use the Back button");
			jumpforward();
		}
	}
