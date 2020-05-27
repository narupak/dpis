	function cmenu(field, type, fld_ref) {
		menuHeight = 0;
		if (type==1) {
			menuWidth = 90;
			arr_show = Array("ขึ้นต้น","ตรงกลาง","เท่ากับ")
			arr_action = Array("javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'s%\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'%%\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'=\\\');")
		} else if (type==2) {
			menuWidth = 123;
			var obj = document.getElementById(fld_ref);
//			alert(obj.value);
			if (obj.value) {
				amt1 = (Math.abs(obj.value) + Math.floor(obj.value * 1));
				acon1 = "ถึง " + amt1;
				aact1 = "->" + amt1;
				amt2 = (Math.abs(obj.value) + Math.floor(obj.value * 2));
				acon2 = "ถึง " + amt2;
				aact2 = "->" + amt2;
				amt3 = (Math.abs(obj.value) + Math.floor(obj.value * 4));
				acon3 = "ถึง " + amt3;
				aact3 = "->" + amt3;
				amt4 = (Math.abs(obj.value) + Math.floor(obj.value * 6));
				acon4 = "ถึง " + amt4;
				aact4 = "->" + amt4;
				amt5 = (Math.abs(obj.value) + Math.floor(obj.value * 9));
				acon5 = "ถึง " + amt5;
				aact5 = "->" + amt5;
				arr_show = Array("น้อยกว่าเท่ากับ","มากกว่าเท่ากับ","เท่ากับ",acon1,acon2,acon3,acon4,acon5)
				arr_action = Array("javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'<=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'>=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'==\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'"+aact1+"\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'"+aact2+"\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'"+aact3+"\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'"+aact4+"\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'"+aact5+"\\\');")
			} else {
				arr_show = Array("น้อยกว่าเท่ากับ","มากกว่าเท่ากับ","เท่ากับ")
				arr_action = Array("javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'<=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'>=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'==\\\');")
			}
		} else if (type==3) {
			menuWidth = 123;
			arr_show = Array("น้อยกว่าเท่ากับ","มากกว่าเท่ากับ","เท่ากับ","+1 สัปดาห์","+2 สัปดาห์","+1 เดือน","+3 เดือน","+6 เดือน","+1 ปี")
			arr_action = Array("javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'<=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'>=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'+1w\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'+2w\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'+1M\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'+3M\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'+6M\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'+1Y\\\');")
		} else if (type==4) {
			menuWidth = 123;
			arr_show = Array("น้อยกว่าเท่ากับ","มากกว่าเท่ากับ","เท่ากับ")
			arr_action = Array("javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'s<=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'s>=\\\');", "javascript:call_tranmenu(\\\'"+field.name+"\\\',\\\'"+fld_ref+"\\\',\\\'s=\\\');")
		}
		menuItemNum = 0;
		menuItems = new Array();
		function addMenuItem(text, url){
			menuItems[menuItemNum] = new Array(text, url);
			menuItemNum++;
		}
		for(i=0; i < arr_show.length; i++) {
			addMenuItem(arr_show[i] , arr_action[i]);
			menuHeight+=19;
		}
		menuContent = '<table id="rightMenu" width="0" height="0" cellspacing="0" cellpadding="0" style="font:menu;color:menutext;"><tr height="1"><td style="background:threedlightshadow" colspan="4"></td><td style="background:threeddarkshadow"></td></tr><tr height="1"><td style="background:threedlightshadow"></td><td style="background:threedhighlight" colspan="2"></td><td style="background:threedshadow"></td><td style="background:threeddarkshadow"></td></tr><tr height="10"><td style="background:threedlightshadow"></td><td style="background:threedhighlight"></td><td style="background:threedface"><table cellspacing="0" cellpadding="0" nowrap style="font:menu;color:menutext;cursor:default;">';
		for(m=0; m < menuItems.length; m++){
			if(menuItems[m][0] && menuItems[m][2])
				menuContent += '<tr height="17" onMouseOver="this.style.background=\'highlight\';this.style.color=\'highlighttext\';" onMouseOut="this.style.background=\'threedface\';this.style.color=\'menutext\';" onClick="parent.window.location.href=\'' + menuItems[m][1] + '\'"><td style="background:threedface" width="1" nowrap></td><td width="21" nowrap><td nowrap>' + menuItems[m][0] + '</td><td style="background:threedface" width="1" nowrap></td></tr>';
			else if(menuItems[m][0])
				menuContent += '<tr height="17" onMouseOver="this.style.background=\'highlight\';this.style.color=\'highlighttext\';" onMouseOut="this.style.background=\'threedface\';this.style.color=\'menutext\';" onClick="parent.window.location.href=\'' + menuItems[m][1] + '\'"><td style="background:threedface" width="1" nowrap></td><td width="21" nowrap></td><td nowrap>' + menuItems[m][0] + '</td><td width="21" nowrap></td><td style="background:threedface" width="1" nowrap></td></tr>';
			else
				menuContent += '<tr><td colspan="5" height="4"></td></tr><tr><td colspan="5"><table cellspacing="0"><tr><td width="2" height="1"></td><td width="0" height="1" style="background:threedshadow"></td><td width="2" height="1"></td></tr><tr><td width="2" height="1"></td><td width="100%" height="1" style="background:threedhighlight"></td><td width="2" height="1"></td></tr></table></td></tr><tr><td colspan="5" height="3"></td></tr>';
		}
		menuContent += '</table></td><td style="background:threedshadow"></td><td style="background:threeddarkshadow"></td></tr><tr height="1"><td style="background:threedlightshadow"></td><td style="background:threedhighlight"></td><td style="background:threedface"></td><td style="background:threedshadow"></td><td style="background:threeddarkshadow"></td></tr><tr height="1"><td style="background:threedlightshadow"></td><td style="background:threedshadow" colspan="3"></td><td style="background:threeddarkshadow"></td></tr><tr height="1"><td style="background:threeddarkshadow" colspan="5"></td></tr></table>';
		contextMenu = window.createPopup();
//		alert(menuContent);
		contextMenu.document.body.innerHTML = menuContent;
		menuXP = event.clientX + 3;
		menuYP = event.clientY + 3;
		contextMenu.show(menuXP, menuYP, menuWidth, menuHeight, document.body);
	}
	
	function call_tranmenu(fldname, fldref, act) {
		var ae = document.getElementById(fldname);
		ae.innerHTML = "<small>["+act+"]</small>";
//		alert(document.form1.COND_LIST.value);
		arr_cond = document.form1.COND_LIST.value.split(",");
		idx=-1;
		for(i=0; i < arr_cond.length; i++) {
			arr_sub = arr_cond[i].split(":");
			if (arr_sub[0]==fldref) {
				idx=i; break;
			}
		} // end for i
		if (idx > -1) {
			arr_cond[idx]= fldref+":"+act;
		} else {
			arr_cond.push(fldref+":"+act);
		}
		document.form1.COND_LIST.value = arr_cond.toString();
	}

