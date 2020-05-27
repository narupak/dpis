// JavaScript Document
var agt					=	navigator.userAgent.toLowerCase();
// *** BROWSER VERSION ***
// Note: On IE5, these return 4, so use is_ie5up to detect IE5.
var is_major 		= parseInt(navigator.appVersion);
var is_minor 		= parseFloat(navigator.appVersion);

var is_nav  			= ((agt.indexOf('mozilla')!=-1) && (agt.indexOf('spoofer')==-1)
                					  && (agt.indexOf('compatible') == -1) && (agt.indexOf('opera')==-1)
                					  && (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1));
var is_nav2 			= (is_nav && (is_major == 2));
var is_nav3 			= (is_nav && (is_major == 3));
var is_nav4 			= (is_nav && (is_major == 4));
var is_nav4up 		= (is_nav && (is_major >= 4));
var is_navonly		= (is_nav && ((agt.indexOf(";nav") != -1) || (agt.indexOf("; nav") != -1)) );
var is_nav6 			= (is_nav && (is_major == 5));
var is_nav6up 		= (is_nav && (is_major >= 5));
var is_gecko 		= (agt.indexOf('gecko') != -1);

var is_ie     			= ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
var is_ie3    			= (is_ie && (is_major < 4));
var is_ie4    			= (is_ie && (is_major == 4) && (agt.indexOf("msie 4")!=-1) );
var is_ie4up  		= (is_ie && (is_major >= 4));
var is_ie5    			= (is_ie && (is_major == 4) && (agt.indexOf("msie 5.0")!=-1) );
var is_ie5_5  		= (is_ie && (is_major == 4) && (agt.indexOf("msie 5.5") !=-1));
var is_ie5up  		= (is_ie && !is_ie3 && !is_ie4);
var is_ie5_5up		= (is_ie && !is_ie3 && !is_ie4 && !is_ie5);
var is_ie6    			= (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );
var is_ie6up  		= (is_ie && !is_ie3 && !is_ie4 && !is_ie5 && !is_ie5_5);

var is_opera 		= (agt.indexOf("opera") != -1);
var is_opera2 		= (agt.indexOf("opera 2") != -1 || agt.indexOf("opera/2") != -1);
var is_opera3 		= (agt.indexOf("opera 3") != -1 || agt.indexOf("opera/3") != -1);
var is_opera4 		= (agt.indexOf("opera 4") != -1 || agt.indexOf("opera/4") != -1);
var is_opera5 		= (agt.indexOf("opera 5") != -1 || agt.indexOf("opera/5") != -1);
var is_opera5up 	= (is_opera && !is_opera2 && !is_opera3 && !is_opera4);

function window_resize () {
	menu_space_width =  menu_space.offsetWidth - tablebarmenu.offsetWidth;
}

// ================= Menu Control ================
var tmp_menu_lv1_id = 0;
function call_menu_lv1_show (menu_id) {
//	alert("tmp_menu_lv1_id:"+tmp_menu_lv1_id+", menu_id:"+menu_id);
	if (tmp_menu_lv1_id > 0){ 	
		var objLayer = document.getElementById("Layer1Menu" + tmp_menu_lv1_id);
		objLayer.style.display = "none";
	}
	if (menu_id > 0 && menu_id < 999) {
		var menu_space_width =  menu_space.offsetWidth - tablebarmenu.offsetWidth;
		var  top_position_menu = HeaderBar.offsetHeight + menu_space.offsetHeight - 26;
//		alert("top_position_menu=HeaderBar.offsetHeight("+HeaderBar.offsetHeight+") + menu_space.offsetHeight("+menu_space.offsetHeight+") - 1 = "+top_position_menu);
		var objMenu = document.getElementById("menu" + menu_id);
		var objLayer = document.getElementById("Layer1Menu" + menu_id);
		var objIfrm = document.getElementById("DivShimLV1");	
		var objTable = document.getElementById("Table1Menu" + menu_id);

		tmp_menu_lv1_id = menu_id;
		objLayer.style.display = "block";
		objLayer.style.pixelHeight = 476;
		objLayer.style.overflowY = "auto";

		var SpaceLength =  menu_space_width + objMenu.offsetLeft + (objMenu.offsetWidth/2);
		var left_position_menu = SpaceLength -  (objTable.offsetWidth/2) + 17;
//		alert("objLayer.offsetHeight("+objLayer.offsetHeight+") , objLayer.offsetWidth ("+objLayer.offsetWidth+") , objTable.offsetHeight ("+objTable.offsetHeight+") , objTable.offsetWidth ("+objTable.offsetWidth+") , objIfrm.offsetHeight ("+objIfrm.offsetHeight+") , objIfrm.offsetWidth ("+objIfrm.offsetWidth+") , objMenu.offsetHeight ("+objMenu.offsetHeight+") , objMenu.offsetWidth ("+objMenu.offsetWidth+")");

		objLayer.style.top = top_position_menu;
		objLayer.style.left = left_position_menu;

		if (objLayer.offsetWidth < objTable.offsetWidth) objLayer.style.width = objTable.offsetWidth+17;
		objIfrm.style.width = objLayer.offsetWidth;
		if(objTable.offsetHeight > objLayer.offsetHeight){ 
			objIfrm.style.height = objLayer.offsetHeight;
		}else{ 
			objIfrm.style.height = objTable.offsetHeight;
		}

		var a1 = objLayer.style.left.replace(/[^\d.-]/g, '');	// มันมี px อยู่ เลือก a1[0] เป็นส่วนตัวเลข
		
		objIfrm.style.top = objLayer.style.top;
		objIfrm.style.left = objLayer.style.left;
		if (Number(a1) < 0) { 
//			alert('-------left (a1)='+a1); 
			objIfrm.style.left = '1px'; 
			objLayer.style.left  = '1px'; 
		} else { 
//			alert("objLayer...top="+objLayer.style.top+" , left (a1)="+a1); 
		}
		objIfrm.style.zIndex = objLayer.style.zIndex - 1;
		objIfrm.style.display = "block";
//		alert("objLayer.offsetHeight("+objLayer.offsetHeight+") , objLayer.offsetWidth ("+objLayer.offsetWidth+") , objTable.offsetHeight ("+objTable.offsetHeight+") , objTable.offsetWidth ("+objTable.offsetWidth+") , objIfrm.offsetHeight ("+objIfrm.offsetHeight+") , objIfrm.offsetWidth ("+objIfrm.offsetWidth+") , objMenu.offsetHeight ("+objMenu.offsetHeight+") , objMenu.offsetWidth ("+objMenu.offsetWidth+")");
	}
//	eval("var menuObj = menu" + menu_id);
//	menuObj.className = "menu_active";
}

function call_menu_lv1_hide (menu_id) {
	if (menu_id > 0 && menu_id < 999){ 
		var objLayer = document.getElementById("Layer1Menu" + menu_id);
		var objIfrm = document.getElementById("DivShimLV1");		

	    objLayer.style.display = "none";
	    objIfrm.style.display = "none";
	}
//	eval("var menuObj = menu" + menu_id);
//	menuObj.className = "menu_inactive";
}

var tmp_menu_lv2_id = 0;
function call_menu_lv2_show (menu_id, menu_order, ref_id_lv1) {
	if (tmp_menu_lv2_id > 0){ 	
		var objLayer = document.getElementById("Layer2Menu" + tmp_menu_lv2_id);
		objLayer.style.display = "none";
	}
	if (menu_id > 0) {
		var objParent = document.getElementById("Layer1Menu" + tmp_menu_lv1_id);
		var objParentRow = document.getElementById("menu_lv1_" + tmp_menu_lv1_id + "_" + ref_id_lv1);		
		var objLayer = document.getElementById("Layer2Menu" + menu_id);
		var objIfrm = document.getElementById("DivShimLV2");
		var objTable = document.getElementById("Table2Menu" + menu_id);	

//		alert("SELECTED_MENU_LV1="+SELECTED_MENU_LV1);
		if(SELECTED_MENU_LV1 != ref_id_lv1) objParentRow.className ="menu_lv1_active";
		tmp_menu_lv2_id = menu_id;
		objLayer.style.display = "block";

//		alert(objParent.style.left + " :: " + objParent.offsetWidth + " :: " + objLayer.offsetWidth + " :: " + document.body.clientLeft + " vs " + document.body.clientWidth);	
		var 	left_position_menu = 0;
		if(is_ie6up){
			if( document.body.clientWidth - (parseInt(objParent.style.left, 10) + objParent.offsetWidth + document.body.clientLeft) < 250 ){
				left_position_menu = parseInt(objParent.style.left, 10) - objLayer.offsetWidth + 1;
				app_left = 1;
			}else{
				left_position_menu = parseInt(objParent.style.left, 10) + objParent.offsetWidth - 1;
				app_left = 0;
			} // end if
		}else{
			if( (parseInt(objParent.style.left, 10) + objParent.offsetWidth + objLayer.offsetWidth + document.body.clientLeft) > document.body.clientWidth ){
//				alert("1..("+objParent+") objParent.style.left="+objParent.style.left+" , objParent.offsetWidth="+objParent.offsetWidth);
				left_position_menu = parseInt(objParent.style.left, 10) - objLayer.offsetWidth + 1;
				app_left = 1;
			}else{
//				alert("2..("+objParent+") objParent.style.left="+objParent.style.left+" , objParent.offsetWidth="+objParent.offsetWidth);
				left_position_menu = parseInt(objParent.style.left, 10) + objParent.offsetWidth - 1;
				app_left = 0;
			} // end if
		} // end if
		
//		alert(objParent.style.top + " : " + menu_order);
		var  top_position_menu = parseInt(objParent.style.top, 10) + (25 * (menu_order - 1));
//		alert("top_position_menu=" + top_position_menu + " :: " + "objTable.offsetHeight=" + objTable.offsetHeight + " :: " + "document.body.clientTop=" + document.body.clientTop + " vs document.body.clientHeight=" + document.body.clientHeight);
		if( (top_position_menu + objTable.offsetHeight + document.body.clientTop) > document.body.clientHeight ){
				objLayer.style.pixelHeight = (Math.floor((document.body.clientHeight - (top_position_menu + document.body.clientTop)) / 25) * 25) + 1;
		}else{
				objLayer.style.pixelHeight = objTable.offsetHeight;
		}

		objLayer.style.overflowY = "auto";
		objLayer.style.top = top_position_menu+20; // เพิ่มจุดเริ่มต้นทางแนวตั้งจะได้เห็น เมนูที่จี้ไว้
//		objLayer.style.left = left_position_menu-7;	// ถอยเพื่อให้เมนูย่อยซ้อนบนแมนูหลัก เพื่อไม่ให้เกิดข่องว่าง
//		alert("app_left:"+app_left);
		if (app_left==0) {
			objLayer.style.left = left_position_menu-Math.floor(objParent.offsetWidth / 2);		//// ถอยเพื่อให้เมนูย่อยซ้อนบนแมนูหลัก ไปครึ่งเมนู
		} else {
			objLayer.style.left = left_position_menu+Math.floor(objParent.offsetWidth / 2);		//// ถอยเพื่อให้เมนูย่อยซ้อนบนแมนูหลัก ไปครึ่งเมนู
		}
//		alert("top_position_menu="+top_position_menu+", left_position_menu="+left_position_menu+ ", objLayer.style.left="+objLayer.style.left+" ("+(objLayer.style.left < 0)+")");
		if (parseInt(objLayer.style.left) < 0) objLayer.style.left=10;
		
		objIfrm.style.width = objLayer.offsetWidth;
		if(objTable.offsetHeight > objLayer.offsetHeight){ 
			objIfrm.style.height = objLayer.offsetHeight;
		}else{ 
			objIfrm.style.height = objTable.offsetHeight;
		}
		objIfrm.style.top = objLayer.style.top;
		objIfrm.style.left = objLayer.style.left;
		objIfrm.style.zIndex = objLayer.style.zIndex - 1;
		objIfrm.style.display = "block";		
	}
}

function call_menu_lv2_hide (menu_id, ref_id_lv1) {
	if (menu_id > 0){ 
//		alert(menu_id);
		var objParentRow = document.getElementById("menu_lv1_" + tmp_menu_lv1_id + "_" + ref_id_lv1);		
		var objLayer = document.getElementById("Layer2Menu" + menu_id);
		var objIfrm = document.getElementById("DivShimLV2");	

		if(SELECTED_MENU_LV1 != ref_id_lv1) objParentRow.className ="menu_lv1_inactive";
	    objLayer.style.display = "none";
	    objIfrm.style.display = "none";
	}
//	eval("var menuObj = menu" + menu_id);
//	menuObj.className = "menu_inactive";
}
// JavaScript Document