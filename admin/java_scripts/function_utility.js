// remove White Space from start and/or end of given string
String.prototype.trim  = _trim;

/**
  * remove White Space from start and/or end of given string
  * White Space is defined as:
  *         - Space
  *        - Carriage Return
  *        - newline
  *        - form feed
  *        - TABs
  *        - Vertical TABs
  **/

function _trim ( ) {
	//   /            open search
	//     ^            beginning of string
	//     \s           find White Space, space, TAB and Carriage Returns
	//     +            one or more
	//   |            logical OR
	//     \s           find White Space, space, TAB and Carriage Returns
	//     $            at end of string
	//   /            close search
	//   g            global search

	return this.replace(/^\s+|\s+$/g, "");
}

function DigitOnly() {
//	alert('0'.charCodeAt()+"<= keyCode ["+event.keyCode+"] <="+'9'.charCodeAt());
	if (event.keyCode>='0'.charCodeAt() && event.keyCode <= '9'.charCodeAt())
		event.returnValue = true;
	else
		event.returnValue = false;
//	alert("return="+event.returnValue);
	return event.returnValue;
}

function NumOnly() {
	if ((event.keyCode>='0'.charCodeAt() && event.keyCode <= '9'.charCodeAt()) ||  event.keyCode == '.'.charCodeAt()) {
		event.returnValue = true;
	} else {
		event.returnValue = false;
	}
//	alert(event.keyCode+':'+event.returnValue);
	return event.returnValue;
}

function DateOnly() {
	if ((event.keyCode>='0'.charCodeAt() && event.keyCode <= '9'.charCodeAt()) ||  event.keyCode == '/'.charCodeAt()){
		event.returnValue = true;
	}else{
		event.returnValue = false;
	}
	return event.returnValue;
}

function StringReverse(str) {
	var retStr = "";
	for(var i=str.length; i>=0; i--) retStr += str.charAt(i);
	return retStr;
		
}

/* This script and many more are available free online at
The JavaScript Source!! http://javascript.internet.com
Created by: Mike Elkins |  */
function selectivecheck(field, myselection) {
  var fieldid;
  var pos;
  var criteria;
  var strng;
  strng = myselection.value;
  for (i=0; i<field.length; i++) {
    if (strng=="all") {
      field[i].checked = true;
    } else {
      fieldid = field[i].id;
      pos = strng.substring(0,1);
      criteria = strng.substring(1,2);  //this gets the information we want to evaluate
      if (fieldid.substring(pos,pos+1)==criteria) {
        field[i].checked = true;
      } else {
        field[i].checked = false; //you could leave this out if you don't want to clear the check boxes
      }
    }
  }
}


//ไม่ให้คลิกขวา
/*if (window.Event)
document.captureEvents(Event.MOUSEUP);*/
function nocontextmenu(){
	event.cancelBubble = true;
	event.returnValue = false;
return false;
}
function norightclick(e){ // This function is used by all others
	if (window.Event){ // again, IE or NAV?
		if (e.which == 2 || e.which == 3)
	return false;
	}else if (event.button == 2 || event.button == 3){
		event.cancelBubble = true;
		event.returnValue = false;
	return false;
	}
}
document.oncontextmenu = nocontextmenu; // for IE5+
document.onmousedown = norightclick; // for all others

function init_fields()
{
//	alert("init_field....");
	var el, els, e, f = 0, form, forms = document.getElementsByTagName('form');
	while (form = forms.item(f++))
	{
		e = 0; els = form.getElementsByTagName('input');
		while (el = els.item(e++))
			if (el.readOnly)
				el.className = 'readonly';
	}
}

	function keyEnter(even,inputN){
//		alert(even.keycode+":"+eval(inputN));
		var keyCode=null;
		if(even.which){
			keyCode = even.which;
		}else if(even.keyCode){
			keyCode = even.keyCode;
		}
		if(keyCode==13 && even.target.type!="textarea"){
//			if (eval(inputN)==undefined) alert("inputN is undefined");
//			else alert("inputN="+eval(inputN));
			if (eval(inputN)!=undefined) eval(inputN).click();
			return false;
		}
	return true;
	}

document.onreadystatechange = init_fields;

var cfile="personal_file.html";

// 	function นี้ ใช้ร่วมกับ create_link_page ซึ่งเป็น php function ถ้าโปรแกรมไหนเรียกใช้  create_link_page ต้อง include แฟ้มนี้เข้าไปด้วย
	function checkPageEnter(keycode, tvalue, all_page) {
		num_ret = NumOnly();
		//if(num_ret || keycode=='13'){  /*เดิม*/
                if(keycode=='13'){ /*Release 5.1.0.7 */
                        //alert(tvalue+'',+tvalue+','+all_page+','+form1.current_page.value);
			if(tvalue>0 && tvalue<=all_page && tvalue!=form1.current_page.value) {
				change_current_page(tvalue);
			} else {
				if(tvalue < 0 || tvalue>all_page) {
					num_ret = false;
				}
			}
		}
		return num_ret; 
	}
        function checkPageDetailEnter(keycode,Valpage,Allpage,formName){
            if(keycode=='13'){ 
                if(Valpage>0 && Valpage<=Allpage && Valpage!=eval(formName).current_page.value) {
                        change_current_page_data(Valpage,formName);
                } else {
                        if(Valpage < 0 || Valpage>Allpage) {
                                return false;
                        }
                }
            }
        }

