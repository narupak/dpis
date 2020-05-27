<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	//echo "<pre>$_POST<hr>$command<br>MASTER_TABLE1 = $MASTER_TABLE1</pre>";
	if($command == "UPDATE" && trim($old_code) && trim($new_code)){
		if ($MASTER_TABLE1=="per_prename") {
			$cmd = " select count(pn_code) as COUNT_DATA from per_prename where pn_code='$old_code' or pn_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_personal set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_personal set pn_code_f='$new_code' where pn_code_f='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_personal set pn_code_m='$new_code' where pn_code_m='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_marrhis set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_namehis set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_namehis set pn_code_new='$new_code' where pn_code_new='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_family set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_child set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_transfer_req set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_scholar set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_senior_excusive set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_competency_assessment set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_prename where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_religion") {
			$cmd = " select count(re_code) as COUNT_DATA from per_religion where re_code='$old_code' or re_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_personal set re_code='$new_code' where re_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_family set re_code='$new_code' where re_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_child set re_code='$new_code' where re_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_religion where re_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_country") {
			$cmd = " select count(ct_code) as COUNT_DATA from per_country where ct_code='$old_code' or ct_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_province set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_institute set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org_ass set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_positionhis set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_educate set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_educate set ct_code_edu='$new_code' where ct_code_edu='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_training set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_training set ct_code_fund='$new_code' where ct_code_fund='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_scholarship set ct_code_own='$new_code' where ct_code_own='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_scholarship set ct_code_go='$new_code' where ct_code_go='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_scholar set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_course set ct_code='$new_code' where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_course set ct_code_fund='$new_code' where ct_code_fund='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_country where ct_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_province") {
			$cmd = " select count(pv_code) as COUNT_DATA from per_province where pv_code='$old_code' or pv_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_amphur set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org_ass set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_personal set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_positionhis set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_marrhis set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_address set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_family set mr_doc_pv_code='$new_code' where mr_doc_pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_family set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_child set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update site_info set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update user_group set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_control set pv_code='$new_code' where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_province where pv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_amphur") {
			$cmd = " select count(ap_code) as COUNT_DATA from per_amphur where ap_code='$old_code' or ap_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_org set ap_code='$new_code' where ap_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org_ass set ap_code='$new_code' where ap_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_positionhis set ap_code='$new_code' where ap_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_address set ap_code='$new_code' where ap_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_amphur where ap_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_heirtype") {
			$cmd = " select count(hr_code) as COUNT_DATA from per_heirtype where hr_code='$old_code' or hr_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_heir set hr_code='$new_code' where hr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_heirtype where hr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_off_type") {
			$cmd = " select count(ot_code) as COUNT_DATA from per_off_type where ot_code='$old_code' or ot_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_position set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_pos_move set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_order_dtl set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req1_dtl1 set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req2_dtl set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req3_dtl set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_personal set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_off_type where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_divorce") {
			$cmd = " select count(dv_code) as COUNT_DATA from per_divorce where dv_code='$old_code' or dv_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_marrhis set dv_code='$new_code' where dv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_divorce where dv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_married") {
			$cmd = " select count(mr_code) as COUNT_DATA from per_married where mr_code='$old_code' or mr_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_personal set mr_code='$new_code' where mr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_marrhis set mr_code='$new_code' where mr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_family set mr_code='$new_code' where mr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_child set mr_code='$new_code' where mr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_married where mr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE1=="per_blood") {
			$cmd = " select count(bl_code) as COUNT_DATA from per_blood where bl_code='$old_code' or bl_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_personal set per_blood='$new_code' where per_blood='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_blood where bl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE2=="per_org_type") {
			$cmd = " select count(ot_code) as COUNT_DATA from per_org_type where ot_code='$old_code' or ot_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_org set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org_ass set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl1 set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl6 set ot_code='$new_code' where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_org_type where ot_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE2=="per_org_level") {
			$cmd = " select count(ol_code) as COUNT_DATA from per_org_level where ol_code='$old_code' or ol_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_org set ol_code='$new_code' where ol_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_org_ass set ol_code='$new_code' where ol_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_org_level where ol_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE3=="per_line") {
			$cmd = " select count(pl_code) as COUNT_DATA from per_line where pl_code='$old_code' or pl_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_skill_group set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_position set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_pos_move set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_assign_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_order_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req1_dtl1 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req2_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req3_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_positionhis set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_actinghis set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_actinghis set pl_code_assign='$new_code' where pl_code_assign='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_transfer_req set pl_code_1='$new_code' where pl_code_1='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_transfer_req set pl_code_2='$new_code' where pl_code_2='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_transfer_req set pl_code_3='$new_code' where pl_code_3='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_comdtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_comdtl set pl_code_assign='$new_code' where pl_code_assign='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_move_req set pl_code_1='$new_code' where pl_code_1='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_move_req set pl_code_2='$new_code' where pl_code_2='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_move_req set pl_code_3='$new_code' where pl_code_3='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl2 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl3 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl8 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl9 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl10 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update pos_des_info set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_line where pl_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE3=="per_mgt") {
			$cmd = " select count(pm_code) as COUNT_DATA from per_mgt where pm_code='$old_code' or pm_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_position set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_pos_move set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_order_dtl set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req1_dtl1 set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req2_dtl set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req3_dtl set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_positionhis set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_actinghis set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_actinghis set pm_code_assign='$new_code' where pm_code_assign='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_comdtl set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl4 set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl5 set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl10 set pm_code='$new_code' where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_mgt where pm_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}

		if ($MASTER_TABLE3=="per_type") {
			$cmd = " select count(pt_code) as COUNT_DATA from per_type where pt_code='$old_code' or pt_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_mgtsalary set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_position set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_pos_move set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_order_dtl set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req1_dtl1 set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req2_dtl set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req3_dtl set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_positionhis set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_sum_dtl2 set pt_code='$new_code' where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_type where pt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE3=="per_condition") {
			$cmd = " select count(pc_code) as COUNT_DATA from per_condition where pc_code='$old_code' or pc_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_position set pc_code='$new_code' where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			
				$cmd = " update per_pos_move set pc_code='$new_code' where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_order_dtl set pc_code='$new_code' where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req1_dtl1 set pc_code='$new_code' where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req2_dtl set pc_code='$new_code' where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_req3_dtl set pc_code='$new_code' where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_condition where pc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE3=="per_level") {
			$cmd = " select count(level_no) as COUNT_DATA from per_level where level_no='$old_code' or level_no='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_layer set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			
				$cmd = " update per_mgtsalary set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_co_level set level_no_min='$new_code' where level_no_min='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_co_level set level_no_max='$new_code' where level_no_max='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " update per_line set level_no_min='$new_code' where level_no_min='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_line set level_no_max='$new_code' where level_no_max='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " update per_layer_new set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_pos_name set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_empser_pos_name set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_temp_pos_name set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " update per_assign set level_no_min='$new_code' where level_no_min='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_assign set level_no_max='$new_code' where level_no_max='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_assign_year set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_assign_s set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_personal set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_positionhis set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_salaryhis set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_actinghis set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_actinghis set level_no_assign='$new_code' where level_no_assign='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set level_no_1='$new_code' where level_no_1='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set level_no_2='$new_code' where level_no_2='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set level_no_3='$new_code' where level_no_3='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_senior_excusive set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_decorcond set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_standard_competence set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_competency_assessment set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_compensation_test_dtl set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_train_project_dtl set level_no_start='$new_code' where level_no_start='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_train_project_dtl set level_no_end='$new_code' where level_no_end='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_train_project_personal set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update pos_des_info set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl4 set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl5 set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl7 set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl8 set level_no='$new_code' where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				$cmd = " delete from per_level where level_no='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE3=="per_pos_group") {
			$cmd = " select count(pg_code) as COUNT_DATA from per_pos_group where pg_code='$old_code' or pg_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_layeremp set pg_code='$new_code' where pg_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			
				$cmd = " update per_layeremp_new set pg_code='$new_code' where pg_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			
				$cmd = " update per_pos_name set pg_code='$new_code' where pg_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " update per_pos_emp set pg_code_salary='$new_code' where pg_code_salary='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " delete from per_pos_group where pg_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}	
		
		if ($MASTER_TABLE3=="per_pos_name") {
			$cmd = " select count(pn_code) as COUNT_DATA from per_pos_name where pn_code='$old_code' or pn_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_pos_emp set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			
				$cmd = " update per_positionhis set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set pn_code_1='$new_code' where pn_code_1='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set pn_code_2='$new_code' where pn_code_2='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set pn_code_3='$new_code' where pn_code_3='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set pn_code='$new_code' where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set pn_code_assign='$new_code' where pn_code_assign='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_move_req set pn_code_1='$new_code' where pn_code_1='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_move_req set pn_code_2='$new_code' where pn_code_2='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_move_req set pn_code_3='$new_code' where pn_code_3='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " delete from per_pos_name where pn_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}	
		
		if ($MASTER_TABLE3=="per_co_level") {
			$cmd = " select count(cl_name) as COUNT_DATA from per_co_level where cl_name='$old_code' or cl_name='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_position set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			
				$cmd = " update per_pos_mov set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_order_dtl set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_req3_dtl set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_req2_dtl set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_req1_dtl1 set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl2 set cl_name='$new_code' where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " delete from per_co_level where cl_name='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}	
		
		if ($MASTER_TABLE3=="per_status") {
			$cmd = " select count(ps_code) as COUNT_DATA from per_status where ps_code='$old_code' or ps_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_mgt set ps_code='$new_code' where ps_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl4 set ps_code='$new_code' where ps_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " delete from per_status where ps_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE3=="per_empser_pos_name") {
			$cmd = " select count(ep_code) as COUNT_DATA from per_empser_pos_name where ep_code='$old_code' or ep_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_pos_empser set ep_code='$new_code' where ep_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_positionhis set ep_code='$new_code' where ep_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set ep_code='$new_code' where ep_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set ep_code_assign='$new_code' where ep_code_assign='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " delete from per_empser_pos_name where ep_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}		
		
		if ($MASTER_TABLE3=="per_movment") {
			$cmd = " select count(mov_code) as COUNT_DATA from per_movment where mov_code='$old_code' or mov_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_personal set mov_code='$new_code' where mov_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_positionhis set mov_code='$new_code' where mov_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_salaryhis set mov_code='$new_code' where mov_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_actinghis set mov_code='$new_code' where mov_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set mov_code='$new_code' where mov_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				
				$cmd = " delete from per_movment where mov_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}		
		
		if ($MASTER_TABLE3=="per_comtype") {
			$cmd = " select count(com_type) as COUNT_DATA from per_comtype where com_type='$old_code' or com_type='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_command set com_type='$new_code' where com_type='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_comtype where com_type='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE4=="per_extratype") {
			$cmd = " select count(ex_code) as COUNT_DATA from per_extratype where ex_code='$old_code' or ex_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_pos_mgtsalary set ex_code='$new_code' where ex_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_salaryhis set ex_code='$new_code' where ex_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_extrahis set ex_code='$new_code' where ex_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_pos_mgtsalaryhis set ex_code='$new_code' where ex_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_extratype where ex_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE=="per_extra_income_type") {	//????
			$cmd = " select count(exin_code) as COUNT_DATA from per_extra_income_type where exin_code='$old_code' or exin_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_extra_incomehis set exin_code='$new_code' where exin_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_extra_income_type where exin_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE5=="per_abilitygrp") {
			$cmd = " select count(al_code) as COUNT_DATA from per_abilitygrp where al_code='$old_code' or al_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_ability set al_code='$new_code' where al_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_abilitygrp where al_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE5=="per_skill_group") {
			$cmd = " select count(sg_code) as COUNT_DATA from per_skill_group where sg_code='$old_code' or sg_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_skill set sg_code='$new_code' where sg_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_skill_group where sg_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE5=="per_skill") {
			$cmd = " select count(skill_code) as COUNT_DATA from per_skill where skill_code='$old_code' or skill_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_position set skill_code='$new_code' where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_pos_move set skill_code='$new_code' where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_order_dtl set skill_code='$new_code' where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_req3_dtl set skill_code='$new_code' where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_req2_dtl set skill_code='$new_code' where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_req1_dtl1 set skill_code='$new_code' where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_skill where skill_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE5=="per_special_skillgrp") {
			$cmd = " select count(ss_code) as COUNT_DATA from per_special_skillgrp where ss_code='$old_code' or ss_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_special_skill set ss_code='$new_code' where ss_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_special_skillgrp where ss_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE6=="per_absenttype") {
			$cmd = " select count(ab_code) as COUNT_DATA from per_absenttype where ab_code='$old_code' or ab_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_absenthis set ab_code='$new_code' where ab_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_absent set ab_code='$new_code' where ab_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_absenttype where ab_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE6=="per_holiday") {
			$cmd = " select count(hol_date) as COUNT_DATA from per_holiday where hol_date='$old_code' or hol_date='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " delete from per_holiday where hol_date='$old_code' ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE7=="per_educlevel") {
			$cmd = " select count(el_code) as COUNT_DATA from per_educlevel where el_code='$old_code' or el_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_educname set el_code='$new_code' where el_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_assign_year set el_code='$new_code' where el_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_educate set el_code='$new_code' where el_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholar set el_code='$new_code' where el_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_sum_dtl9 set el_code='$new_code' where el_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_educlevel where el_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE7=="per_educname") {
			$cmd = " select count(en_code) as COUNT_DATA from per_educname where en_code='$old_code' or en_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_educate set en_code='$new_code' where en_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set en_code='$new_code' where en_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_comdtl set en_code='$new_code' where en_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholarship set en_code='$new_code' where en_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholar set en_code='$new_code' where en_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_educname where en_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE7=="per_educmajor") {
			$cmd = " select count(em_code) as COUNT_DATA from per_educmajor where em_code='$old_code' or em_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_educate set em_code='$new_code' where em_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set em_code='$new_code' where em_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholarship set em_code='$new_code' where em_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholar set em_code='$new_code' where em_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_educmajor where em_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE7=="per_train") {
			$cmd = " select count(tr_code) as COUNT_DATA from per_train where tr_code='$old_code' or tr_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_training set tr_code='$new_code' where tr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_course set tr_code='$new_code' where tr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_train_project_dtl set tr_code='$new_code' where tr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_train_project_payment set tr_code='$new_code' where tr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_train_project_personal set tr_code='$new_code' where tr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_train where tr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE7=="per_institute") {
			$cmd = " select count(ins_code) as COUNT_DATA from per_institute where ins_code='$old_code' or ins_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_educate set ins_code='$new_code' where ins_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_transfer_req set ins_code='$new_code' where ins_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholar set ins_code='$new_code' where ins_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_institute where ins_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE7=="per_scholartype") {
			$cmd = " select count(st_code) as COUNT_DATA from per_scholartype where st_code='$old_code' or st_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_educate set st_code='$new_code' where st_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_scholarship set st_code='$new_code' where st_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_scholartype where st_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE8=="per_service") {
			$cmd = " select count(sv_code) as COUNT_DATA from per_service where sv_code='$old_code' or sv_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				$cmd = " update per_servicetitle set sv_code='$new_code' where sv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_servicehis set sv_code='$new_code' where sv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_service where sv_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
				
		if ($MASTER_TABLE8=="per_servicetitle") {
			$cmd = " select count(srt_code) as COUNT_DATA from per_servicetitle where srt_code='$old_code' or srt_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_servicehis set srt_code='$new_code' where srt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_servicetitle where srt_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE8=="per_decoration") {
			$cmd = " select count(dc_code) as COUNT_DATA from per_decoration where dc_code='$old_code' or dc_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_decoratehis set dc_code='$new_code' where dc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_decorcond set dc_code='$new_code' where dc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_decorcond set dc_code_old='$new_code' where dc_code_old='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_decordtl set dc_code='$new_code' where dc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_decoration where dc_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE8=="per_reward") {
			$cmd = " select count(rew_code) as COUNT_DATA from per_reward where rew_code='$old_code' or rew_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_rewardhis set rew_code='$new_code' where rew_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_reward where rew_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE8=="per_time") {
			$cmd = " select count(time_code) as COUNT_DATA from per_time where time_code='$old_code' or time_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_timehis set time_code='$new_code' where time_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_time where time_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE9=="per_penalty") {
			$cmd = " select count(pen_code) as COUNT_DATA from per_penalty where pen_code='$old_code' or pen_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_punishment set pen_code='$new_code' where pen_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_invest2dtl set pen_code='$new_code' where pen_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_penalty where pen_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE9=="per_crime") {
			$cmd = " select count(cr_code) as COUNT_DATA from per_crime where cr_code='$old_code' or cr_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_crime_dtl set cr_code='$new_code' where cr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_crime where cr_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
		
		if ($MASTER_TABLE9=="per_crime_dtl") {
			$cmd = " select count(crd_code) as COUNT_DATA from per_crime_dtl where crd_code='$old_code' or crd_code='$new_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			if ($COUNT_DATA==2) {
				
				$cmd = " update per_punishment set crd_code='$new_code' where crd_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_invest1 set crd_code='$new_code' where crd_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " update per_invest2dtl set crd_code='$new_code' where crd_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

				$cmd = " delete from per_crime_dtl where crd_code='$old_code' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		}
												
		if ($COUNT_DATA!=2) {
			$error_update_code.= "<tr><td width='6%'></td><td><font color='#FF0000'>ไม่มีรหัส&nbsp;&nbsp;$old_code&nbsp;หรือ&nbsp;$new_code</font></td></tr>";
			$error_update_code="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_update_code."</table>";
		} // end if 		
	} // end if
?>