//  function ในการค้นหา เกี่ยงกับโครงสร้างสังกัด ระดับต่าง ๆ 
//		ตัวแปรส่งเข้า มี ctrl_type, sess_usergroup_level, และ ข้อความเตือนต่าง ๆ
//		ต้องมี element ต่อไปนี้เกี่ยวข้อง
//			form1.select_org_structure เป็น radio
//				[0] = ตามกฎหมาย
//				[1] = ตามมอบหมายงาน
//			form1.btn_ministry
//			form1.btn_department
//			form1.btn_org
//			form1.btn_org_ass
//			form1.btn_org_1
//			form1.btn_org_ass_1
//			form1.list_type
//			eval("document.all.RPTORD_ORDER_" + i 
//			eval("document.all.RPTORD_ROW_" + i
//			eval("document.all.IMG_UP_" + i
//			eval("document.all.IMG_DOWN_" + i
//
//			form1.MINISTRY_ID
//			form1.MINISTRY_NAME
//			form1.DEPARTMENT_ID
//			form1.DEPARTMENT_NAME
//			form1.search_pv_code
//			กรณี ค้นหาตามกฎหมาย
//					form1.search_org_id
//					form1.search_org_name
//					form1.search_org_id_1
//					form1.search_org_name_1
//					form1.search_org_id_2
//					form1.search_org_name_2
//			กรณี ค้นหาตามมอบหมาย
//					form1.search_org_ass_id
//					form1.search_org_ass_name
//					form1.search_org_ass_id_1
//					form1.search_org_ass_name_1
//					form1.search_org_ass_id_2
//					form1.search_org_ass_name_2

	function call_search_ministry (ctrl_type, sess_usergroup_level) {	
//		var PROVINCE_CODE = "<?=($CTRL_TYPE==2 || $SESS_USERGROUP_LEVEL==2)?'$PROVINCE_CODE':''?>";
		var PROVINCE_CODE = (ctrl_type==2 || sess_usergroup_level==2)?(form1.search_pv_code?form1.search_pv_code.value:""):"";
		parameter = "&OL_CODE=01&PV_CODE=" + PROVINCE_CODE;
		if (form1.select_org_structure) { // ถ้าส่ง parameter เข้ามา
			if(form1.select_org_structure[0].checked) call_file = "search_org_frame.html";
			else if(form1.select_org_structure[1].checked) call_file = "search_org_ass_frame.html";
		} else  call_file = "search_org_frame.html";
		childReturn = window.showModalDialog(call_file + "?MENU_ID_LV0="+form1.MENU_ID_LV0.value+"&MENU_ID_LV1="+form1.MENU_ID_LV1.value+"&MENU_ID_LV2="+form1.MENU_ID_LV2.value+"&MENU_ID_LV3="+form1.MENU_ID_LV3.value+parameter,"","dialogHeight: 600px; dialogWidth: 800px; status: No; resizable: No; help: No; statusbar: No;");
		if(childReturn){
			arrValue = childReturn.split("<::>");
			if (form1.MINISTRY_ID) form1.MINISTRY_ID.value = arrValue[0];
			if (form1.MINISTRY_NAME) form1.MINISTRY_NAME.value = arrValue[1];
			if (form1.DEPARTMENT_ID) form1.DEPARTMENT_ID.value = "";
			if (form1.DEPARTMENT_NAME) form1.DEPARTMENT_NAME.value = "";

			if(form1.select_org_structure){ // ถ้าส่ง parameter เข้ามา
				if(form1.select_org_structure[0].checked){
					if (form1.search_org_id) form1.search_org_id.value = "";
					if (form1.search_org_name) form1.search_org_name.value = "";
					if (form1.search_org_id_1) form1.search_org_id_1.value = "";
					if (form1.search_org_name_1) form1.search_org_name_1.value = "";
					if (form1.search_org_id_2) form1.search_org_id_2.value = "";
					if (form1.search_org_name_2) form1.search_org_name_2.value = "";
				}else if(form1.select_org_structure[1].checked){
					if (form1.search_org_ass_id) form1.search_org_ass_id.value = "";
					if (form1.search_org_ass_name) form1.search_org_ass_name.value = "";
					if (form1.search_org_ass_id_1) form1.search_org_ass_id_1.value = "";
					if (form1.search_org_ass_name_1) form1.search_org_ass_name_1.value = "";
					if (form1.search_org_ass_id_2) form1.search_org_ass_id_2.value = "";
					if (form1.search_org_ass_name_2) form1.search_org_ass_name_2.value = "";
				}
			} else {
				if (form1.search_org_id) form1.search_org_id.value = "";
				if (form1.search_org_name) form1.search_org_name.value = "";
				if (form1.search_org_id_1) form1.search_org_id_1.value = "";
				if (form1.search_org_name_1) form1.search_org_name_1.value = "";
				if (form1.search_org_id_2) form1.search_org_id_2.value = "";
				if (form1.search_org_name_2) form1.search_org_name_2.value = "";
			} // end if
		} // end if
	}

	function call_search_department (ctrl_type, sess_usergroup_level, ministry_alert) {	
		var MINISTRY_ID = ((ctrl_type==3 || sess_usergroup_level==3)?($MINISTRY_ID?$MINISTRY_ID.value:""):"");
		if(MINISTRY_ID=="" && form1.MINISTRY_ID.value)	MINISTRY_ID = form1.MINISTRY_ID.value;	
		var PROVINCE_CODE = (ctrl_type==2 || sess_usergroup_level==2)?(form1.search_pv_code?form1.search_pv_code.value:""):"";
		if(MINISTRY_ID != ""){
			parameter = "&OL_CODE=02&ORG_ID_REF=" + MINISTRY_ID + "&PV_CODE=" + PROVINCE_CODE;
			if (form1.select_org_structure) {
				if(form1.select_org_structure[0].checked) call_file = "search_org_frame.html";
				else if(form1.select_org_structure[1].checked) call_file = "search_org_ass_frame.html";
			} else  call_file = "search_org_frame.html";
			childReturn = window.showModalDialog(call_file + "?MENU_ID_LV0="+form1.MENU_ID_LV0.value+"&MENU_ID_LV1="+form1.MENU_ID_LV1.value+"&MENU_ID_LV2="+form1.MENU_ID_LV2.value+"&MENU_ID_LV3="+form1.MENU_ID_LV3.value+parameter,"","dialogHeight: 600px; dialogWidth: 800px; status: No; resizable: No; help: No; statusbar: No;");
			if(childReturn){
				arrValue = childReturn.split("<::>");
				if (form1.DEPARTMENT_ID) form1.DEPARTMENT_ID.value = arrValue[0];
				if (form1.DEPARTMENT_NAME) form1.DEPARTMENT_NAME.value = arrValue[1];

				if(form1.select_org_structure){ // ถ้าส่ง parameter เข้ามา
					if(form1.select_org_structure[0].checked){
						if (form1.search_org_id) form1.search_org_id.value = "";
						if (form1.search_org_name) form1.search_org_name.value = "";
						if (form1.search_org_id_1) form1.search_org_id_1.value = "";
						if (form1.search_org_name_1) form1.search_org_name_1.value = "";
						if (form1.search_org_id_2) form1.search_org_id_2.value = "";
						if (form1.search_org_name_2) form1.search_org_name_2.value = "";
					}else if(form1.select_org_structure[1].checked){
						if (form1.search_org_ass_id) form1.search_org_ass_id.value = "";
						if (form1.search_org_ass_name) form1.search_org_ass_name.value = "";
						if (form1.search_org_ass_id_1) form1.search_org_ass_id_1.value = "";
						if (form1.search_org_ass_name_1) form1.search_org_ass_name_1.value = "";
						if (form1.search_org_ass_id_2) form1.search_org_ass_id_2.value = "";
						if (form1.search_org_ass_name_2) form1.search_org_ass_name_2.value = "";
					}
				} else {
					if (form1.search_org_id) form1.search_org_id.value = "";
					if (form1.search_org_name) form1.search_org_name.value = "";
					if (form1.search_org_id_1) form1.search_org_id_1.value = "";
					if (form1.search_org_name_1) form1.search_org_name_1.value = "";
					if (form1.search_org_id_2) form1.search_org_id_2.value = "";
					if (form1.search_org_name_2) form1.search_org_name_2.value = "";
				} // end if
			} // end if
		}else{
			if(ctrl_type==3 || sess_usergroup_level==3){
				if (ministry_alert) alert(ministry_alert+' (กำหนดค่าเริ่มต้นหน่วยงาน)');
			}else{
				if (ministry_alert) alert(ministry_alert);
				if (form1.btn_ministry) form1.btn_ministry.focus();
			}
		} // end if
	}

	function call_search_org (ctrl_type, sess_usergroup_level, dept_alert) {	
		var DEPARTMENT_ID = ((ctrl_type==4 || sess_usergroup_level==4)?($DEPARTMENT_ID?$DEPARTMENT_ID:""):"");
		if(DEPARTMENT_ID=="" && form1.DEPARTMENT_ID.value)	DEPARTMENT_ID = form1.DEPARTMENT_ID.value;	
		if(DEPARTMENT_ID != ""){
			parameter = "&OL_CODE=03&ORG_ID_REF=" + DEPARTMENT_ID;
			if (form1.select_org_structure) {
				if(form1.select_org_structure[0].checked) call_file = "search_org_frame.html";
				else if(form1.select_org_structure[1].checked) call_file = "search_org_ass_frame.html";
			} else  call_file = "search_org_frame.html";
			childReturn = window.showModalDialog(call_file + "?MENU_ID_LV0="+form1.MENU_ID_LV0.value+"&MENU_ID_LV1="+form1.MENU_ID_LV1.value+"&MENU_ID_LV2="+form1.MENU_ID_LV2.value+"&MENU_ID_LV3="+form1.MENU_ID_LV3.value+parameter,"","dialogHeight: 600px; dialogWidth: 800px; status: No; resizable: No; help: No; statusbar: No;");
			if(childReturn){
				arrValue = childReturn.split("<::>");

				if(form1.select_org_structure){ // ถ้าส่ง parameter เข้ามา
					if(form1.select_org_structure[0].checked){
						if (form1.search_org_id) form1.search_org_id.value = arrValue[0];
						if (form1.search_org_name) form1.search_org_name.value = arrValue[1];
						if (form1.search_org_id_1) form1.search_org_id_1.value = "";
						if (form1.search_org_name_1) form1.search_org_name_1.value = "";
						if (form1.search_org_id_2) form1.search_org_id_2.value = "";
						if (form1.search_org_name_2) form1.search_org_name_2.value = "";
					}else if(form1.select_org_structure[1].checked){
						if (form1.search_org_ass_id) form1.search_org_ass_id.value = arrValue[0];
						if (form1.search_org_ass_name) form1.search_org_ass_name.value = arrValue[1];
						if (form1.search_org_ass_id_1) form1.search_org_ass_id_1.value = "";
						if (form1.search_org_ass_name_1) form1.search_org_ass_name_1.value = "";
						if (form1.search_org_ass_id_2) form1.search_org_ass_id_2.value = "";
						if (form1.search_org_ass_name_2) form1.search_org_ass_name_2.value = "";
					}
				} else {
					if (form1.search_org_id) form1.search_org_id.value = arrValue[0];
					if (form1.search_org_name) form1.search_org_name.value = arrValue[1];
					if (form1.search_org_id_1) form1.search_org_id_1.value = "";
					if (form1.search_org_name_1) form1.search_org_name_1.value = "";
					if (form1.search_org_id_2) form1.search_org_id_2.value = "";
					if (form1.search_org_name_2) form1.search_org_name_2.value = "";
				} // end if
			} // end if
		}else{
			if(ctrl_type==4 || sess_usergroup_level==4){
				if (dept_alert) alert(dept_alert+' (กำหนดค่าเริ่มต้นหน่วยงาน)');
			}else{
				if (dept_alert) alert(dept_alert);
				if (form1.btn_department) form1.btn_department.focus();
			}
		} // end if
	}

	function call_search_org_1 (org_alert) {	
		org_id = "";
		if (form1.select_org_structure) {
			if(form1.select_org_structure[0].checked)
				org_id = (form1.search_org_id ? form1.search_org_id.value : "");
			else
				org_id = (form1.search_org_ass_id ? form1.search_org_ass_id.value : "");
		} else org_id = (form1.search_org_id ? form1.search_org_id.value : "");
		if (org_id != "") {
			parameter = "&OL_CODE=04&ORG_ID_REF=" + org_id;
			if (form1.select_org_structure)
				if (form1.select_org_structure[0].checked) {
					call_file = "search_org_frame.html";
				} else if(form1.select_org_structure[1].checked) {
					call_file = "search_org_ass_frame.html";
				} else call_file = "search_org_frame.html";
			else call_file = "search_org_frame.html";
			childReturn = window.showModalDialog(call_file + "?MENU_ID_LV0="+form1.MENU_ID_LV0.value+"&MENU_ID_LV1="+form1.MENU_ID_LV1.value+"&MENU_ID_LV2="+form1.MENU_ID_LV2.value+"&MENU_ID_LV3="+form1.MENU_ID_LV3.value+parameter,"","dialogHeight: 600px; dialogWidth: 800px; status: No; resizable: No; help: No; statusbar: No;");
			if(childReturn){
				arrValue = childReturn.split("<::>");
				if(form1.select_org_structure){
					if(form1.select_org_structure[0].checked){
						if (form1.search_org_id_1) form1.search_org_id_1.value = arrValue[0];
						if (form1.search_org_name_1) form1.search_org_name_1.value = arrValue[1];
						if (form1.search_org_id_2) form1.search_org_id_2.value = "";
						if (form1.search_org_name_2) form1.search_org_name_2.value = "";
					}else if(form1.select_org_structure[1].checked){
						if (form1.search_org_ass_id_1) form1.search_org_ass_id_1.value = arrValue[0];
						if (form1.search_org_ass_name_1) form1.search_org_ass_name_1.value = arrValue[1];
						if (form1.search_org_ass_id_2) form1.search_org_ass_id_2.value = "";
						if (form1.search_org_ass_name_2) form1.search_org_ass_name_2.value = "";
					}
				} else {
					if (form1.search_org_id_1) form1.search_org_id_1.value = arrValue[0];
					if (form1.search_org_name_1) form1.search_org_name_1.value = arrValue[1];
					if (form1.search_org_id_2) form1.search_org_id_2.value = "";
					if (form1.search_org_name_2) form1.search_org_name_2.value = "";
				}
				if (form1.list_type) {
					if(form1.list_type[5].checked){
						for(var i=1; i<=total_rpt_order; i++){
							if(eval("document.all.RPTORD_ORDER_" + i + ".value")=="ORG_1"){ 
								eval("document.all.RPTORD_ORDER_" + i + ".disabled = true");
								eval("document.all.RPTORD_ROW_" + i + ".className = 'table_body_3';");
								if(i != 1) eval("document.all.IMG_UP_" + i + ".style.display = 'none';");
								if(i != total_rpt_order) eval("document.all.IMG_DOWN_" + i + ".style.display = 'none';");
							} // end if
						} // end for
					} // end if

					var RPTORD_LIST = "";
					for(var i=1; i<=total_rpt_order; i++){
						if(eval("document.all.RPTORD_ORDER_" + i + ".checked") && !eval("document.all.RPTORD_ORDER_" + i + ".disabled")){
							if(RPTORD_LIST) RPTORD_LIST += "|";
							RPTORD_LIST += eval("document.all.RPTORD_ORDER_" + i + ".value");
						}
					}	
				
					if(RPTORD_LIST == ""){				
						for(var i=1; i<=total_rpt_order; i++){
							if(eval("document.all.RPTORD_ORDER_" + i + ".value")=="ORG_2"){ 
								eval("document.all.RPTORD_ORDER_" + i + ".disabled = false");
								eval("document.all.RPTORD_ORDER_" + i + ".checked = true");
								eval("document.all.RPTORD_ROW_" + i + ".className = 'table_body';");
								if(i != 1) eval("document.all.IMG_UP_" + i + ".style.display = 'block';");
								if(i != total_rpt_order) eval("document.all.IMG_DOWN_" + i + ".style.display = 'block';");
							} // end if
						} // end for
					} // end if
				} // end if
			} // end if (childReturn)
		}else{
			if (org_alert) alert(org_alert);
			if(form1.select_org_structure)
				if(form1.select_org_structure[0].checked)
					if (form1.btn_org) form1.btn_org.focus();
				else
					if (form1.btn_org_ass) form1.btn_org_ass.focus();
		} // end if
	}

	function call_search_org_2 (org_1_alert) {	
		parameter = "";
		if(form1.select_org_structure) {
			if(form1.select_org_structure[0].checked)
				org_id_1 = (form1.search_org_id_1 ? form1.search_org_id_1.value : "");
			else
				org_id_1 = (form1.search_org_ass_id_1 ? form1.search_org_ass_id_1.value : "");
		} else org_id_1 = (form1.search_org_id_1 ? form1.search_org_id_1.value : "");
		if(org_id_1 != ""){
			parameter = "&OL_CODE=05&ORG_ID_REF=" + org_id_1;
			if(form1.select_org_structure)
				if(form1.select_org_structure[0].checked) {
					call_file = "search_org_frame.html";
				}else if(form1.select_org_structure[1].checked) {
					call_file = "search_org_ass_frame.html";
				}else call_file = "search_org_frame.html";
			else call_file = "search_org_frame.html";
			childReturn = window.showModalDialog(call_file + "?MENU_ID_LV0="+form1.MENU_ID_LV0.value+"&MENU_ID_LV1="+form1.MENU_ID_LV1.value+"&MENU_ID_LV2="+form1.MENU_ID_LV2.value+"&MENU_ID_LV3="+form1.MENU_ID_LV3.value+parameter,"","dialogHeight: 600px; dialogWidth: 800px; status: No; resizable: No; help: No; statusbar: No;");
			if(childReturn){
				arrValue = childReturn.split("<::>");
				if(form1.select_org_structure){
					if(form1.select_org_structure[0].checked){
						if (form1.search_org_id_2) form1.search_org_id_2.value = arrValue[0];
						if (form1.search_org_name_2) form1.search_org_name_2.value = arrValue[1];
					} else {
						if (form1.search_org_ass_id_2) form1.search_org_ass_id_2.value = arrValue[0];
						if (form1.search_org_ass_name_2) form1.search_org_ass_name_2.value = arrValue[1];
					}
				} else {
						if (form1.search_org_id_2) form1.search_org_id_2.value = arrValue[0];
						if (form1.search_org_name_2) form1.search_org_name_2.value = arrValue[1];
				}
				if (form1.list_type) {
					if(form1.list_type[5].checked){
						for(var i=1; i<=total_rpt_order; i++){
							if(eval("document.all.RPTORD_ORDER_" + i + ".value")=="ORG_2"){ 
								eval("document.all.RPTORD_ORDER_" + i + ".disabled = true");
								eval("document.all.RPTORD_ROW_" + i + ".className = 'table_body_3';");
								if(i != 1) eval("document.all.IMG_UP_" + i + ".style.display = 'none';");
								if(i != total_rpt_order) eval("document.all.IMG_DOWN_" + i + ".style.display = 'none';");
							} // end if
						} // end for
					} // end if

					var RPTORD_LIST = "";
					for(var i=1; i<=total_rpt_order; i++){
						if(eval("document.all.RPTORD_ORDER_" + i + ".checked") && !eval("document.all.RPTORD_ORDER_" + i + ".disabled")){
							if(RPTORD_LIST) RPTORD_LIST += "|";
							RPTORD_LIST += eval("document.all.RPTORD_ORDER_" + i + ".value");
						}
					}
			
					if(RPTORD_LIST == ""){				
						for(var i=1; i<=total_rpt_order; i++){
							if(eval("document.all.RPTORD_ORDER_" + i + ".value")=="LINE"){ 
								eval("document.all.RPTORD_ORDER_" + i + ".disabled = false");
								eval("document.all.RPTORD_ORDER_" + i + ".checked = true");
								eval("document.all.RPTORD_ROW_" + i + ".className = 'table_body';");
								if(i != 1) eval("document.all.IMG_UP_" + i + ".style.display = 'block';");
								if(i != total_rpt_order) eval("document.all.IMG_DOWN_" + i + ".style.display = 'block';");
							} // end if
						} // end for
					} // end if
				} // end if
			} // end if(childReturn)
		}else{
			if (org_1_alert) alert(org_1_alert);
			if(form1.select_org_structure)
				if(form1.select_org_structure[0].checked)
					if(form1.btn_org_1) form1.btn_org_1.focus();
				else
					if(form1.btn_org_ass_1) form1.btn_org_ass_1.focus();
		} // end if
	}
