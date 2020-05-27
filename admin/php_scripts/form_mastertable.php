<?
step 1 ลบ SQl เดิม (ถ้ามี)
$table = "PER_COUNTRY"; //ตั้งค่าเพื่อส่งข้อมูลในการ เพิ่มข้อมูล
$menu_detail = "สังกัด";  //ส่งค่าให้ insert log
include("php_scripts/master_table_000.php");
	
function checkadd_filed(chk_btn){   //ไว้เปิดฟิลด์ให้เพิ่มข้อมูลได้
		var chk_btn;
		if(chk_btn == 1)
  		form1.command.value='ADD_FILED';
		else 
		form1.command.value='';
		return true;
} // end function 	
function checkadd(f) {
	if(f.<?=$arr_fields[0]?>.value=="") {
		alert("กรุณาระบุ รหัส");
		f.<?=$arr_fields[0]?>.focus();
		return false;
		} 
		else if(f.<?=$arr_fields[1]?>.value=="") {
		alert("กรุณาระบุ ชื่อ");
		f.<?=$arr_fields[1]?>.focus();
		return false;
		}
		else  	
		form1.command.value='ADD';
		return true;
		} // end function
<input name="Submit_add" type="submit" class="button" onClick="return checkadd_filed(1);" value="<?=$ADD_TITLE?>">
<input name="image" type="image" onClick="return checkadd_filed(1);" src="images/save.png" alt="<?=$ADD_TITLE?>" border="0"> 
<?if($command != "ADD_FILED") {?>
<?}else{?>
   <table width="85%" align="center" border="1" cellpadding="1" cellspacing="1" > <!--เริ่ม-->
	<? include("search_000_master.html"); ?>
	</table> <!--เริ่มสิ้นสุด-->
<?}?>
<?=$err_text?>
<? if ($ADD_NEW_FLAG == 1){ ?> 
<?}?>
?>
