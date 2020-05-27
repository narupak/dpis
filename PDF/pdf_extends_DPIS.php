<?php
		if (file_exists("function_repgen.php")) include ("function_repgen.php");
		else if (file_exists("php_scripts/function_repgen.php")) include ("php_scripts/function_repgen.php");
		else if (file_exists("../php_scripts/function_repgen.php")) include ("../php_scripts/function_repgen.php");
		else if (file_exists("../../php_scripts/function_repgen.php"))  include ("../../php_scripts/function_repgen.php");
//		if (file_exists("pdf_wordarray_thaicut.php")) include ("pdf_wordarray_thaicut.php");
//		else if (file_exists("php_scripts/pdf_wordarray_thaicut.php")) include ("php_scripts/pdf_wordarray_thaicut.php");
//		else if (file_exists("../php_scripts/pdf_wordarray_thaicut.php")) include ("../php_scripts/pdf_wordarray_thaicut.php");
//		else if (file_exists("../../php_scripts/pdf_wordarray_thaicut.php"))  include ("../../php_scripts/pdf_wordarray_thaicut.php");
//		echo "PHP_SELF PATH=".$_SERVER['PHP_SELF']." file exists::(".file_exists("../php_scripts/pdf_wordarray_thaicut.php").")<br>";

		class PDF extends FPDF  
		{
			
			var $arr_word_thai_cut;
			var $specword;
			var $pvword;
			var $amword;

			function Open()
			{
				global $arr_word_thai_cut;
				// extend from function Open() 
//				echo "pdf_extend word_thai_cut>>".implode(",",$arr_word_thai_cut)."<br>";
				FPDF::Open();
				// Add fonts use in document
				if(FPDF_VERSION > '1.51'){
					$this->AddFont('angsab','','../../PDF/font/angsab.php');
					$this->AddFont('angsa','','../../PDF/font/angsa.php');
					$this->AddFont('cordia','','../../PDF/font/cordia.php');
					$this->AddFont('cordiab','','../../PDF/font/cordiab.php');
					$this->AddFont('thsarabun','','../../PDF/font/thsarabun.php');
					$this->AddFont('thsarabunb','','../../PDF/font/thsarabunb.php');
					$this->AddFont('browallia','','../../PDF/font/browa.php');
					$this->AddFont('browalliab','','../../PDF/font/browab.php');
				}else{
					$this->AddFont('angsab','','../../PDF/font/angsab_old.php');
					$this->AddFont('angsa','','../../PDF/font/angsa_old.php');
					$this->AddFont('cordia','','../../PDF/font/cordia.php');
					$this->AddFont('cordiab','','../../PDF/font/cordiab.php');
					$this->AddFont('thsarabun','','../../PDF/font/thsarabun.php');
					$this->AddFont('thsarabunb','','../../PDF/font/thsarabunb.php');
					$this->AddFont('browallia','','../../PDF/font/browa.php');
					$this->AddFont('browalliab','','../../PDF/font/browab.php');
				}
				$this->specword = array("�Ҫ���", "�Ѱ���", "͸Ժ��", "��к��", "���˹�", "�����","�ѧ��Ѵ","����ͧ","��Ժѵ�","�ѭ��","���˹�ҷ��", "���", "���õ���","��è�", "��ç", "���˹�", "�ç���¹", "��Һѳ�Ե", "�ѳ�Ե", "������", "�ӹҭ", "��ʹ", "��ʵ��", "�Ѳ��", "�Ҿ", "�ѹ���", "�Ţ���", "������", "��º��", "�ѡ��������", "��˹�", "�ѹ", "��͹", "��", "���", "���", "����", "�Ҹ�ó�", "�к�", "�Ѳ��", "��è�", "ʡ��", "���", "ʶҹ�", "ʶҾ�", "ʶһ��", "ʶһ�ԡ", "ʶҺѹ", "�ѡ�", "�ҷԵ��", "�ѹ���", "�ѧ���", "�ظ", "����ʺ��", "�ء��", "�����", "���Ҥ�", "����Ҿѹ��", "�չҤ�", "����¹", "����Ҥ�", "�Զع�¹", "�á�Ҥ�", "�ԧ�Ҥ�", "�ѹ��¹", "���Ҥ�", "��Ȩԡ�¹", "�ѹ�Ҥ�","�˹��","��","���ѹ�͡","���ѹ��","��§","���ҹ","���Ǩ","����","��Һ��","�ҡ��","�����","�ӹҹ","��ú��","�ٸ�","ᾷ��","�ٵ�","��ԭ��","�����","����","�Ң�","��ж�","�Ѹ��","��ԭ��","�֡��","��ا","ǧ��","�����","�ش"   ,"�ѡ","�Ԫҡ��","��Ǩ�ͺ","����","�����","��º��","��������","���","�������","�ع","�س"); // ������੾��������
				$this->pvword = array("��ا෾��ҹ��","��ا෾�","��ا෾","���.","���","��й��","��к��", "�ҭ������", "����Թ���", "��ᾧྪ�", "�͹��", "�ѹ�����", "���ԧ���", "�ź���", "��¹ҷ", "�������", "�����", "��§���", "��§����", "��ѧ", "��Ҵ", "�ҡ", "��ù�¡", "��û��", "��þ��", "����Ҫ����", "�����ո����Ҫ", "������ä�", "�������", "��Ҹ����", "��ҹ", "���������", "�����ҹ�", "��ШǺ���բѹ��", "��Ҩչ����", "�ѵ�ҹ�", "��й�������ظ��", "�����", "�ѧ��", "�ѷ�ا", "�ԨԵ�", "��ɳ��š", "ྪú���", "ྪú�ó�", "���", "����", "�����ä��", "�ء�����", "�����ͧ�͹", "��� ��", "����", "�������", "�йͧ", "���ͧ", "�Ҫ����", "ž����", "�ӻҧ", "�Ӿٹ", "���", "�������", "ʡŹ��", "ʧ���", "ʵ��", "��طû�ҡ��", "��ط�ʧ����", "��ط��Ҥ�", "������", "��к���", "�ԧ�����", "��⢷��", "�ؾ�ó����", "����ɮ��ҹ�", "���Թ���", "˹ͧ���", "˹ͧ�������", "��ҧ�ͧ", "�ӹҨ��ԭ", "�شøҹ�", "�صôԵ��", "�ط�¸ҹ�", "�غ��Ҫ�ҹ�");
				$this->amword = array("����", "����ҧᴧ", "��º���", "����ԭ", "Ǫ�ú����", "��ᴹ", "�����ѡ", "�������", "�����ú���", "���෾", "˹ͧ��", "�֧����ѹ", "���˹��", "�ѧ��", "�Ҥ��", "����֧", "�ǹ���", "���Թ�дǡ", "��ҹ��", "�ҧ�", "⾸����", "�ҡ���", "�Ѵ�ŧ", "��ҹ��", "���¤", "��;���", "������ʴ��", "����С�", "�����ǧ", "�ͧ������", "�ѧ��к���", "����ǹ", "��Ң�ѭ", "��ҹ�Т������", "˹ͧ����", "���¡����", "��ҡ�дҹ", "��ҹ�ǹ", "����ҧ�ҧ�Ǫ", "��ҹ��ҧ", "�ҧ������", "��ջ�Шѹ��", "�͹਴���", "�ͧ����ͧ", "����ء", "���ͧ", "˹ͧ˭���", "��ᾧ�ʹ", "��ê�����", "�͹���", "�ҧ�Ź", "�����ҹ", "�ط�����", "��з���ẹ", "��ҹ���", "�ҧ����", "������", "������", "˹ͧ˭�һ��ͧ", "����", "����ҧ", "��ҹ�Ҵ", "��ҹ����", "�觡�Шҹ", "��º���", "�Ѻ���", "�ҧ�оҹ", "�ҧ�оҹ����", "��ҳ����", "����Թ", "��������ʹ", "��������", "�ҹʡ�", "�ҹ�С�", "��ҧ", "�Իٹ", "�����˭�", "���Ǵ", "�������", "���ʧ", "�Һ͹", "����˭�", "�ҡ��ѧ", "��͹�Ժ����", "�Ԫ�", "����", "�����", "�ҧ�ѹ", "��Ӿ�ó��", "�����ó�", "��о���", "���Ե��", "��ҧ��ҧ", "����������õ�", "�����֧", "�ǹ��ǧ", "�Թ��", "�ǹ���ԡ", "�лҧ", "�Ҿ��", "����ѹ��", "��ͧ����", "�����֡", "�ع", "��§��", "��§��ǹ", "�͡����", "��", "����", "�٫ҧ", "�١�����", "���§���", "��ҹ�Ң��", "������¹", "����", "�����ʹ", "�ѧ�����", "���§", "��ɮ�", "�Ҵ���ҭ", "�����", "�Ҫ��ʹ", "������", "�ǹ��ع", "�ҡ���ٹ", "��պ�þ�", "�����պ�þ�", "��Һ͹", "�ҧ���", "��Ҿ����", "��չ��Թ���", "⤡⾸��", "˹ͧ�ԡ", "�й����", "��§�ͧ", "�ԧ", "�ҹ", "���ᴴ", "���ѹ", "��§�ʹ", "������", "�������", "���§������", "���������", "���§��", "�ع���", "�������ǧ", "������", "���§��§���", "�����ǧ", "�ع���", "���", "��������§", "����ҹ���", "ʺ���", "�ҧ�м��", "���µ��", "�á���", "����ʧ", "˹ͧ��� ", "��þ������", "���������", "�Ҥ��", "��ҵ��", "�����", "����Ф���", "�Ҵ���", "�ҡ���", "���ǧ��", "����Թ", "����Һ�", "���¹�����", "����", "��������", "�Ѿ�ѹ", "���ҧ������", "˹ͧ�ҧ", "˹ͧ�����ҧ", "��ҹ���", "�ҹ�ѡ", "���¤�", "�ç��", "��ͧ�ҹ", "�ҳ����ѡɺ���", "��ͧ��ا", "��ҹ��е���", "�ҹ��к��", "���·ͧ�Ѳ��", "�ҧ���ҷͧ", "�֧���Ѥ��", "�����չ��", "��ҹ�ҡ", "�����", "������Ҵ", "����ͧ�ҧ", "����ʹ", "�����", "�����ҧ", "�ѧ���", "��һ��", "��ҹ��ҹ�ҹ���", "�������", "�������", "����Ѫ�����", "������ç", "���ä�š", "��չ��", "����������", "�����", "�ҵԵ�С��", "�ҧ�С�", "�ҧ��з���", "���������", "�Ѵ�ʶ�", "�ѧ�ͧ��ҧ", "�Թ�л�ҧ", "�ѧ���¾ٹ", "⾸���зѺ��ҧ", "�оҹ�Թ", "�ҧ��Źҡ", "⾷���", "�������", "�Ѻ����", "�ҡ����", "�֧���ҧ", "���ҧᴹ�Թ", "��ͧ���", "��ҧ��", "⤡����ؾ�ó", "��ԭ��Ż�", "⾹�����", "�پҹ", "�ҹù����", "�ش���ͤ�", "��ҹ�ѹ", "��һҡ", "�����෹", "��ҹᾧ", "�ҵؾ��", "���", "�óٹ��", "���ʧ����", "������", "⾹���ä�", "�ҷ�", "�ѧ�ҧ", "�Ԥ��������", "�͹���", "����ǧ", "�Ӫ���", "���ҹ�˭�", "˹ͧ�٧", "����ͧ", "������", "��§���", "�������", "���ᵧ", "������", "����ԧ", "�ҧ", "������", "�����", "�ѹ��ҵͧ", "�ѹ��ᾧ", "�ѹ����", "�ҧ��", "�ʹ", "������", "������", "�����", "���§�˧", "���§��", "�»�ҡ��", "����ҧ", "����͹", "�������", "�����§����", "�ǧ��", "�ǧ��", "�����", "����", "��ҹ���", "���", "�����Ǫ�ҧ", "��ҫҧ", "��ҹ��", "���§˹ͧ��ͧ", "�������", "��Ф�", "��������", "���", "�����", "�ѧ�˹��", "�Թ", "����ԡ", "����", "ʺ��Һ", "��ҧ�ѵ�", "��͹", "��һ��", "��ӻҴ", "�����", "�÷ͧ", "������", "���ѧ", "�о��", "����ҹ", "ີ�", "�ѹ�ѧʵ�", "����", "��觸���", "����", "���ѹ", "�ç�Թѧ", "�ҡ�", "�����", "����", "����", "�������", "����Ҥ�", "���", "�ؤ��Թ", "���˧�-š", "���˧�Ҵ�", "����", "�������ͧ", "�ҧ���", "��й��", "���Ե", "˹ͧ�͡", "�ҧ�ѡ", "�ҧࢹ", "�ҧ�л�", "�����ѹ", "������Һ�ѵ�پ���", "���⢹�", "�չ����", "�Ҵ��кѧ", "�ҹ����", "����ѹ�ǧ��", "����", "������", "�ҧ�͡�˭�", "���¢�ҧ", "��ͧ�ҹ", "���觪ѹ", "�ҧ�͡����", "�ҧ�ع��¹", "������ԭ", "˹ͧ��", "��ɮ���ó�", "�ҧ��Ѵ", "�Թᴧ", "�֧����", "�Ҹ�", "�ҧ����", "��بѡ�", "�ҧ������", "������", "��ͧ��", "�Ҫ���", "�Ҵ�����", "�Ѳ��", "�ҧ�", "��ѡ���", "������", "�ѹ�����", "�оҹ�٧", "�ѧ�ͧ", "��ͧ�����", "�ҧ��", "����Ѳ��", "��觤��", "�ҧ�͹", "��ҹ�����", "�ҧ���", "�ҧ���", "��л��ᴧ", "�����ط�਴���", "�ҧ��Ҹ�", "�ҧ����", "�ҧ�˭�", "�ҧ��Ƿͧ", "�ù���", "�ҡ���", "��ù������", "�ǧ��ҷ", "��ͧ��ǧ", "�ѭ����", "˹ͧ����", "�Ҵ�������", "���١��", "���⤡", "�٤�", "�������", "�����ǧ", "�ҧ��", "�ҧ���", "�ҧ���Թ", "�ҧ���ѹ", "�ҧ���ѹ", "�ѡ���", "�Ҫ�", "�Ҵ�����ǧ", "�ѧ����", "�ʹ�", "�ҧ����", "�ط��", "����Ҫ", "��ҹ�á", "���", "������", "⾸��ͧ", "��ǧ��", "����ɪ�ªҭ", "�����", "�Ѳ�ҹԤ�", "⤡���ç", "��ºҴ��", "������", "��ҹ����", "�����ǧ", "����ʶ�", "⤡��ԭ", "��ʹ��", "˹ͧ��ǧ", "��ҹ���", "�ҧ�Шѹ", "���ºҧ�Шѹ", "��������", "���¾����", "�ӾѴ", "�ӷѺ", "�˹�ͤ�ͧ", "������", "�л�", "�С��Ƿ��", "�С��ǻ��", "���к���", "�Ѻ�ش", "��������ͧ", "�з��", "��ҧ", "��觤�", "�ҭ����ɰ�", "�͹�ѡ", "�������", "��оЧѹ", "���", "��Ҫ��", "�����Ѱ�Ԥ�", "��ҹ�Ңع", "���", "��ҩҧ", "��ҹ�����", "��ҹ�����", "��¹��", "���§���", "����ʧ", "�ع�Թ", "��º���", "����Ǵ�", "��о�ѹ", "������", "��ҹ�͹", "�����", "������", "��к���", "�آ���ҭ", "�����", "�з��", "��ѧ�ǹ", "����", "�����", "������", "���", "��觵��", "ʷԧ���", "�й�", "�ҷ��", "෾�", "�к������", "��⹴", "������Թ��", "������Թ���", "�ѵ����", "����", "�Ҵ�˭�", "�������", "�ǹ��§", "�ҧ����", "�ԧ˹��", "��ͧ�����", "��ҹ���", "�ǹⴹ", "��觤ǹⴹ", "�ǹ���ŧ", "��觷���", "����", "�Ч�", "�������", "�йѧ", "�ѹ�ѧ", "�Ҵ�", "������", "�ѧ������", "��ҹ���ҧ", "��Шѹ����", "������⾸�", "������ʶ", "⤡�պ", "��ѭ�����", "�Ҿ����", "�Ѳ�ҹ��", "��ͧ�Ҵ", "�ҡ���", "��ҹ��", "ͧ��ѡ��", "�ҩ��è�", "⤡�٧", "�ѧ����ó�", "�ú���", "��ԧ�ҧ", "��", "��ҹ�������", "�ҡ���", "��ҹ⤡", "�Ԫ��", "�Ѻ��", "�ͧ�ʹ�ѹ", "��ͧ��ҧ", "�ͧ", "�٧���", "�蹪��", "�ͧ", "�ѧ���", "˹ͧ��ǧ��", "������", "��ҹ��ǧ", "�ҹ���", "���", "���", "����ѧ��", "��", "���§��", "��觪�ҧ", "��§��ҧ", "������", "�ѹ���آ", "�������", "�ͧ��", "����§", "��պح���ͧ", "�ҡ�ҧ", "����ó����", "⹹�ѧ", "��ҹ���", "������", "��", "���ҧ���", "˹ͧ�ʧ", "���٧", "�Ժ�����ѡ��", "������", "��Шѡ����ŻҤ�", "�Ҵ�ǧ", "��§�ҹ", "�ҡ��", "��ҹ����", "������", "������", "������", "�ѧ�оا", "�١�д֧", "����ǧ", "�Ң��", "�����ѳ", "˹ͧ�Թ", "��Һ��", "�֧���", "����ԭ", "⾹�����", "������", "�����§����", "�ѧ��", "ૡ�", "�ҡ�Ҵ", "�֧⢧�ŧ", "�������", "��觤���", "�����", "������", "�ѵ��һ�", "⾸��ҡ", "ᡴ�", "���������", "�ѹ���Ԫ��", "��§�׹", "�ú��", "����͡", "��Ѥ����Ծ����", "�һջ���", "�Ҵٹ", "�ҧ�����Ҫ", "�ش�ѧ", "��蹪�", "��غ", "�ɵ������", "�����ѵ��", "���þѡ�þ��ҹ", "��Ѫ����", "�����", "⾹�ͧ", "⾸����", "˹ͧ�͡", "�������", "����ó����", "⾹����", "�Ҩ����ö", "���Ǵ�", "�������", "�ѧ���", "��§��ѭ", "˹ͧ��", "�������ǧ", "����", "�������", "��ͧ��", "�ةԹ���³�", "��ǧ", "�ҧ��Ҵ", "�������", "���ʢѹ��", "����ǧ", "��Ҥѹ�", "˹ͧ�ا���", "����", "���¼��", "������", "�Ҵ�", "�͹�ҹ", "��ͧ���", "���������", "�ش�ҡ", "��óҹԤ�", "�ѧ⤹", "���Ԫ����", "�Ԥ�����ٹ", "����ٹ", "�ӵҡ���", "��ҹ��ǧ", "�ҡ���ӹ��", "���", "��ǧ����Ժ", "���Թ���Һ", "�ӹҨ��ԭ", "�ʹҧ��Ԥ�", "��ǵоҹ", "�Ժ���ѧ�����", "������", "⾸����", "���ç", "�͹��ᴧ", "���Թ��", "�������ش�", "�����Ҫǧ��", "�����ѡ���", "������", "�ҵ��", "�����������", "���ҧ����ǧ��", "��Ӣ��", "����ó����", "�������", "�ش���", "�����͹���", "��ҵ���", "��Ҫ�Ъ��", "����ѧ", "��ԧ����", "����ԭ", "��ҹ����", "�͹���ä�", "�ɵ�����ó�", "˹ͧ���ᴧ", "�ѵ����", "���˹稳ç��", "˹ͧ��������", "෾ʶԵ", "������", "��ҹ��", "�駤���", "�͹���", "�ѡ�ժ����", "�Թʧ��", "�Ѻ�˭�", "����ҳ��Ѳ��", "��ҹ�������", "�ѧ����", "�Ѻ�", "⤡ྪ�", "���ҧ��ѡ", "��ҹ���", "��������ҹ", "����ٳ", "�ҹ��ҹ", "����ӹҨ", "���ѧ", "��ҹ�ҧ", "����׹", "˹ͧ����", "����", "�ժ���", "��Ӿͧ", "�غ��ѵ��", "��йǹ", "��ҹ��", "���¹���", "��", "�ǧ�˭�", "�ǧ����", "˹ͧ�ͧ��ͧ", "�����§", "�ѭ�Ҥ���", "����", "���ǹ��ҧ", "�ټ���ҹ", "���٧", "⤡⾸����", "˹ͧ�Ҥ�", "��ҹ�δ", "⹹����", "�ش�Ѻ", "˹ͧ��ǫ�", "�����һ�", "⹹���Ҵ", "˹ͧ�ҹ", "��觽�", "���ҹ", "��ոҵ�", "�ѧ������", "��ҹ�ا", "˹ͧ�������", "�ѡ�Ҫ", "⪤���", "��ҹ�ع��", "⹹��", "⹹�٧", "�������ʧ", "����˭�", "��з��", "�ѡ�����", "�����", "�����ŧ", "����ǧ", "�٧�Թ", "���������", "�դ���", "�ҡ��ͧ", "˹ͧ�ح�ҡ", "��ʹ���ҧ", "⹹ᴧ", "�ѧ�������", "෾��ѡ��", "��зͧ��", "�ӷ�������", "������", "�մ�", "�Ф��-��ʧ����", "⹹���", "����ѧ", "�ҧ�ͧ", "˹ͧ���", "���ҹ����", "���⤹���", "��ҹ��Ǵ", "�ط�ʧ", "�ӻ������", "ʵ֡", "�Ф�", "��⾸��", "˹ͧ˧��", "��Ѻ��Ҫ��", "�����Ҫ", "⹹����ó", "�ӹ�", "��ҹ�����¾���", "⹹�Թᴧ", "��ҹ��ҹ", "᤹��", "����ź���", "��ҵ��", "������", "����ҷ", "�Һ�ԧ", "�ѵ�����", "ʹ�", "�բ�����", "�ѧ��", "�Ӵǹ", "���ç�Һ", "���઴", "������ѡ", "��ճç��", "����Թ�Թ���", "⹹����³�", "�ҧ�������", "�ѹ�������", "�ѹ���ѡ��", "�آѹ��", "�ú֧", "��ҧ����", "�ع�ҭ", "�������", "�ط���þ����", "�֧��þ�", "���·Ѻ�ѹ", "⹹�ٳ", "⹹�ٹ", "����ѵ��", "�������§", "�ѧ�Թ", "���ԧ��", "ອ��ѡ��", "�����", "⾸���������ó", "�����Ҵ", "⢧����", "���ͧ�", "���Ұ", "പ�ش�", "�Ҩ�����", "����׹", "�س��ԡ", "��С�þת��", "�ش���ǻ��", "��Ҫ�ҧ", "�Թ������", "������", "�Ѵ�ԧ��", "��þ��", "��ä����", "�ѹ��", "˹ͧ�����", "�Թ���", "�觤��", "˹ͧ�", "�����ᴧ", "˹ͧ᫧", "��ҹ���", "�͹�ش", "˹ͧⴹ", "��оط��ҷ", "������", "�ǡ����", "�ѧ��ǧ", "��ҹ�֧", "˹ͧ�˭�", "�ҧ���ا", "�ҹ�ͧ", "���ʹԤ�", "����Ҫ�", "����ժѧ", "�ѵ�պ", "��ͷͧ", "��Шѹ���", "�ҧ����", "������ѧ", "��ҧ�ҧ", "��ҹ�ҧ", "�ŧ", "�ѧ�ѹ���", "��ҹ����", "��ǡᴧ", "�Ҫ����", "�Ԥ��Ѳ��", "�Һ���", "��ا", "�������", "�觹����͹", "�Т��", "�����ԧ��", "��´��", "���ҧ���", "��������", "�ҤԪ��ٯ", "�Ӿظ", "��ͧ�˭�", "����ԧ", "������", "�����ͺ", "��Сٴ", "��Ъ�ҧ", "�ҧ����", "�ҧ���������", "�ҧ�С�", "��ҹ⾸��", "�����ä��", "�Ҫ����", "ʹ�����", "�ŧ���", "��ҵ���º", "��ͧ���͹", "��Թ������", "�Һѧ", "���§���", "�ؤ��", "����Ҫ���", "�Ҹ�ó���"); // ������੾��������

				$this->arr_word_thai_cut = $arr_word_thai_cut;	//	get_word_thai_cut();
				
				rsort($this->specword);					//	���§�ҡ�ҡ仹��� ����������¤�����ǡ��������͹ �����¡�ä��ҡ�͹
				rsort($this->pvword);						//	���§�ҡ�ҡ仹��� ����������¤�����ǡ��������͹ �����¡�ä��ҡ�͹
				rsort($this->amword);						//	���§�ҡ�ҡ仹��� ����������¤�����ǡ��������͹ �����¡�ä��ҡ�͹
				rsort($this->arr_word_thai_cut);	//	���§�ҡ�ҡ仹��� ����������¤�����ǡ��������͹ �����¡�ä��ҡ�͹
//				if ($this->arr_word_thai_cut)  rsort($this->arr_word_thai_cut);	//	���§�ҡ�ҡ仹��� ����������¤�����ǡ��������͹ �����¡�ä��ҡ�͹
				
//				echo "arr_word_thai_cut:".implode(",",$this->arr_word_thai_cut)."<br>";
//				echo "open font=".$this->FontFamily." size=".$this->FontSizePt."<br>";
				if (!$this->FontFamily)	$this->SetFont('thsarabun','',14);	// ��˹���� default
			}

			function Header()
			{
//				echo "Header font=".$this->FontFamily." size=".$this->FontSizePt."<br>";
				$font = $this->FontFamily;
				$fontstyle = $this->FontStyle;
				$fontsize = $this->FontSizePt;
				
 				$this->SetTextColor(0,0,96);
 			    $this->SetFont($font,$fontstyle,$fontsize);

				if($this->report_title){		
					$t_title = explode("||",$this->report_title);
					if(count($t_title) == 1){      
						 //     �� heading report �� 1 line  -->  ������§ҹ
//						if($this->company_name){ 
//			 			    $this->SetFont('angsa','',10);
//							$this->Cell(20,6,$this->company_name,0,0,'L');
//						} // end if
//						$this->x = $this->lMargin;
						$sub_t_title = explode("^", $t_title[0]);
						if ($sub_t_title[1]) $align = $sub_t_title[1];
						if (!$align) $align = "C";
		 			    $this->SetFont($font,$fontstyle,$fontsize);
						$this->Cell(0,6,$sub_t_title[0],0,1,$align);
//						$this->x = $this-> w -  55;
//						$this->Cell(50,6,$this->report_code,0,1,'R');
					}else{		
						//     �� heading report  > 1 line  -->  �ժ��͡�� ������� ������§ҹ ��� heading ��� �
						for($i=0; $i < count($t_title); $i++ ){
							$align = "";
							$sub_t_title = explode("^", $t_title[$i]);
							if($i == (count($t_title) - 1)){
//								if($this->company_name){ 
//					 			    $this->SetFont('angsa','',10);
//									$this->Cell(20,6,$this->company_name,0,0,'L');
//								} // end if
//								$this->x = $this->lMargin;
								$sub_t_title = explode("^", $t_title[$i]);
								if ($sub_t_title[1]) $align = $sub_t_title[1];
								if (!$align) $align = "C";
				 			    $this->SetFont($font,$fontstyle,$fontsize);
								$this->Cell(0,6,$sub_t_title[0],0,1,$align);
//								$this->x = $this-> w -  55;
//								$this->Cell(50,6,$this->report_code,0,1,'R');
							}else{		
//								$this->SetFont('angsab','',16);
//								$this->SetTextColor(98, 0, 0);
								$sub_t_title = explode("^", $t_title[$i]);
								if ($sub_t_title[1]) $align = $sub_t_title[1];
								if (!$align) $align = "C";
								$this->Cell(0,6,$sub_t_title[0],0,1,$align);
							} // end if
						} // end for
					} // end if
				
//					$this->SetTextColor(108);
					$this->SetTextColor(0,0,0);
					if ($fontsize-4 < 10)
						$this->SetFont($font,'',10); // �ѧ�Ѻ������ �� style
					else
		 			    $this->SetFont($font,'',$fontsize-4); // �ѧ�Ѻ������ �� style
					$this->Cell(0,5, $this->company_name,0,1,'L');
//					$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
				}   //  			if      ($this->report_title)
				
//				if   ($this->heading)
//				{
//						$this->SetTextColor(0, 0, 0);
//						$this->ReportHeader($this->heading,$this->heading_width,$this->heading_align,$this->heading_border);
//						$this->SetTextColor(108);
//						$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
//					}
				$this->SetTextColor(0, 0, 0);
				$this->flag_new_page = 'Y';
			}

			function Footer($at_end_up=10)
			{
				// Footer ����ö��˹��ٻẺ ��ѧ���
				// line 1 part Left text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border&line 1 part Center text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border&line 1 part Right text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border^line 2 part Left text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border^at_end_up
				if	($this->report_title   ||  $this->footer) {
					global $NUMBER_DISPLAY;

					if (trim($this->footer)) {
						$arr_footer = explode("^", trim($this->footer));
						$line_cnt = count($arr_footer)-1;
	
						if ($arr_footer[$line_cnt]) $this->SetY(-((float)$arr_footer[$line_cnt]));	// $arr_footer[$line_cnt] ��ҵ���ش���� ��� ��� at end up
						else $this->SetY(-$at_end_up);
	
						for($i=0; $i < $line_cnt; $i++) {
//							echo "arr_footer [$i]=".$arr_footer[$i]."  ";
							if ($arr_footer[$i]) {
								if (strtolower($arr_footer[$i])=="line")
									$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
								else {
									$arr_footer_col = explode("&",$arr_footer[$i]);
									$col_cnt = count($arr_footer_col);
									for($j=0; $j < $col_cnt; $j++) {
										if (strtolower($arr_footer_col[$j])=="standard") {
//											echo "standard-$j<br>";
											$font = $this->FontFamily;
											$fontstyle = $this->FontStyle;
											$fontsize = $this->FontSizePt - 4;	// 	// �ѧ�Ѻ ��Ҵ ��� ��硡��ҷ���˹���ѡ��� -2
											if ($fontsize < 10) $fontsize = 10;		// ��˹���颹Ҵ����ش������ 10 ����� -4 �з������ font size ������ 8 �ѹ����Թ� 
						
											$fontstyle = "";	// �ѧ�Ѻ������ �� style
					
											if ($j==0) 
												$this->Cell(100,6,($this->report_code?("��§ҹ : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");
											else if ($j==1) {
												$page = "{nb}";
												$page = (($NUMBER_DISPLAY==2)?convert2thaidigit($page):$page);
//												echo "1..page=$page<br>";
												$this->x = ($this->w / 2) - 10;
												$this->Cell(10,6,"˹�ҷ�� : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
											} else {
												$MONTH_TH[] = "��͹";
												$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
												$MONTH_TH[] = "��.�.";		$MONTH_TH[] = "��.�.";
												$MONTH_TH[] = "�.�";		$MONTH_TH[] = "��.�.";
												$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
												$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
												$MONTH_TH[] = "�.�";		$MONTH_TH[] = "�.�.";
							
												$MONTH_EN[] = "Month";
												$MONTH_EN[] = "Jan";		$MONTH_EN[] = "Feb";
												$MONTH_EN[] = "Mar";		$MONTH_EN[] = "Apr";
												$MONTH_EN[] = "May";		$MONTH_EN[] = "Jun";
												$MONTH_EN[] = "Jul";		$MONTH_EN[] = "Aug";
												$MONTH_EN[] = "Sep";		$MONTH_EN[] = "Oct";
												$MONTH_EN[] = "Nov";		$MONTH_EN[] = "Dec";
							
												$today = getdate(); 
												$year = $today['year'];
												if  ($this->lang_code == "TH")
														{		$year = $year + 543; 
																$month = $MONTH_TH[$today['mon']]; 
														}
												else
														{		$month = $MONTH_EN[$today['mon']]; 
														}
												$mday = $today['mday'];
												$time = date('H:i:s');
												$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
												$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
												$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
												$this->x = $this->w - 30;
												$this->Cell(25,6,"�ѹ������� : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
											}
										} else {	// if not standard
											if ($arr_footer_col[$j]) {
//												echo "$j-".$arr_footer_col[$j]."<br>";
												$font = $this->FontFamily;
												$fontstyle = $this->FontStyle;
												$fontsize = $this->FontSizePt - 4;	// 	// �ѧ�Ѻ ��Ҵ ��� ��硡��ҷ���˹���ѡ��� -2
												if ($fontsize < 10) $fontsize = 10;		// ��˹���颹Ҵ����ش������ 10 ����� -4 �з������ font size ������ 8 �ѹ����Թ� 
							
												$fontstyle = "";	// �ѧ�Ѻ������ �� style
						
												$arr_footer_det = explode("|",$arr_footer_col[$j]);
												if ($arr_footer_det[1]) $font = $arr_footer_det[1];	// ��ҡ�˹� ���� font
												if ($arr_footer_det[2]) $fontstyle = $arr_footer_det[2];	// ��ҡ�˹� font style
												if ($arr_footer_det[3]) $fontsize = $arr_footer_det[3];	// ��ҡ�˹� font size
												
												$this->SetFont($font, $fontstyle, $fontsize);
				//								echo "$i--font::$font,$fontstyle,$fontsize--";
												if (strlen($arr_footer_det[4])==6) {
													$r=substr($arr_footer_det[4],0,2);
													$g=substr($arr_footer_det[4],2,2);
													$b=substr($arr_footer_det[4],4,2);
												} else { $r="00"; $g="00"; $b="00"; }
					//							echo "font:$r $g $b<br>";
												$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
									
												if (strlen($arr_footer_det[5])==6) {
													$r=substr($arr_footer_det[5],0,2);
													$g=substr($arr_footer_det[5],2,2);
													$b=substr($arr_footer_det[5],4,2);
													$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
	//												echo "fill:$r $g $b<br>";
												}
	//											echo "$i--".$arr_footer[$i]."<br>";
												$col_w = ($arr_footer_det[6] ? (float)$arr_footer_det[6] : ($this->w-$this->lMargin-$this->rMargin));
												$line_h = ($arr_footer_det[7] ? (float)$arr_footer_det[7] : 7);
												$border = $arr_footer_det[8];
												$align = ($j==0 ? "L" : ($j==1 ? "C" : "R"));
	//											echo "$i>>x:".$this->x.",  y:".$this->y.",  w:$col_w, line_h:$line_h, align:$align, font:$font,$fontstyle,$fontsize<br>";
												$this->Cell($col_w,$line_h,($NUMBER_DISPLAY==2 ? convert2thaidigit($arr_footer_det[0]) : $arr_footer_det[0]),$border,0,$align);
											} // end if $arr_footer_col[$j]
										}
									} // end for loop $j (column loop

									$this->x = $this->lMargin;
									$this->y += $line_h;
								} // end if $line
							} else {  // if not $arr_footer
								$font = $this->FontFamily;
								$fontstyle = $this->FontStyle;
								$fontsize = $this->FontSizePt - 4;	// 	// �ѧ�Ѻ ��Ҵ ��� ��硡��ҷ���˹���ѡ��� -2
								if ($fontsize < 10) $fontsize = 10;		// ��˹���颹Ҵ����ش������ 10 ����� -4 �з������ font size ������ 8 �ѹ����Թ� 
			
								$fontstyle = "";	// �ѧ�Ѻ������ �� style
					
								$MONTH_TH[] = "��͹";
								$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
								$MONTH_TH[] = "��.�.";		$MONTH_TH[] = "��.�.";
								$MONTH_TH[] = "�.�";		$MONTH_TH[] = "��.�.";
								$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
								$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
								$MONTH_TH[] = "�.�";		$MONTH_TH[] = "�.�.";
			
								$MONTH_EN[] = "Month";
								$MONTH_EN[] = "Jan";		$MONTH_EN[] = "Feb";
								$MONTH_EN[] = "Mar";		$MONTH_EN[] = "Apr";
								$MONTH_EN[] = "May";		$MONTH_EN[] = "Jun";
								$MONTH_EN[] = "Jul";		$MONTH_EN[] = "Aug";
								$MONTH_EN[] = "Sep";		$MONTH_EN[] = "Oct";
								$MONTH_EN[] = "Nov";		$MONTH_EN[] = "Dec";
			
								$today = getdate(); 
								$year = $today['year'];
								if  ($this->lang_code == "TH")
										{		$year = $year + 543; 
												$month = $MONTH_TH[$today['mon']]; 
										}
								else
										{		$month = $MONTH_EN[$today['mon']]; 
										}
								$mday = $today['mday'];
								$time = date('H:i:s');
								$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
								$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
								$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
								$page = "{nb}";
								$page = (($NUMBER_DISPLAY==2)?convert2thaidigit($page):$page);
								//Position at 1.0 cm from bottom
//								$this->SetTextColor(0,0,0);
//								$this->SetFont($font,'',$fontsize-4); // �ѧ�Ѻ������ �� style
//								echo "2..page=$page<br>";
								$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
								$this->Cell(100,6,($this->report_code?("��§ҹ : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");		
								$this->x = ($this->w / 2) - 10;
								$this->Cell(10,6,"˹�ҷ�� : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
								$this->x = $this->w - 30;
								$this->Cell(25,6,"�ѹ������� : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
								
								$this->x = $this->lMargin;
								$this->y += 6;
							} // end if ($arr_footer)
						} // end for loop $i ($line)
					} else {	// if not footer but report_title
						$font = $this->FontFamily;
						$fontstyle = $this->FontStyle;
						$fontsize = $this->FontSizePt - 4;	// 	// �ѧ�Ѻ ��Ҵ ��� ��硡��ҷ���˹���ѡ��� -2
						if ($fontsize < 10) $fontsize = 10;		// ��˹���颹Ҵ����ش������ 10 ����� -4 �з������ font size ������ 8 �ѹ����Թ� 
	
						$fontstyle = "";	// �ѧ�Ѻ������ �� style
					
						$MONTH_TH[] = "��͹";
						$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
						$MONTH_TH[] = "��.�.";		$MONTH_TH[] = "��.�.";
						$MONTH_TH[] = "�.�";		$MONTH_TH[] = "��.�.";
						$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
						$MONTH_TH[] = "�.�.";		$MONTH_TH[] = "�.�.";
						$MONTH_TH[] = "�.�";		$MONTH_TH[] = "�.�.";
	
						$MONTH_EN[] = "Month";
						$MONTH_EN[] = "Jan";		$MONTH_EN[] = "Feb";
						$MONTH_EN[] = "Mar";		$MONTH_EN[] = "Apr";
						$MONTH_EN[] = "May";		$MONTH_EN[] = "Jun";
						$MONTH_EN[] = "Jul";		$MONTH_EN[] = "Aug";
						$MONTH_EN[] = "Sep";		$MONTH_EN[] = "Oct";
						$MONTH_EN[] = "Nov";		$MONTH_EN[] = "Dec";
	
						$today = getdate(); 
						$year = $today['year'];
						if  ($this->lang_code == "TH")
								{		$year = $year + 543; 
										$month = $MONTH_TH[$today['mon']]; 
								}
						else
								{		$month = $MONTH_EN[$today['mon']]; 
								}
						$mday = $today['mday'];
						$time = date('H:i:s');
						$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
						$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
						$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
						$page = "{nb}";
//						$page = (($NUMBER_DISPLAY==2)?convert2thaidigit($page):$page);
//						echo "3..page=$page<br>";
						//Position at 1.0 cm from bottom
						$this->SetY(-$at_end_up);
						$this->SetTextColor(0,0,0);
						if ($fontsize-4 < 10)
							$this->SetFont($font,'',10); // �ѧ�Ѻ������ �� style
						else
							$this->SetFont($font,'',$fontsize-4); // �ѧ�Ѻ������ �� style
						$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
						$this->Cell(100,6,($this->report_code?("��§ҹ : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");		
						$this->x = ($this->w / 2) - 10;
						$this->Cell(10,6,"˹�ҷ�� : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
						$this->x = $this->w - 30;
						$this->Cell(25,6,"�ѹ������� : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
					} // end if ($this->footer)
				}   //	if      ($this->report_title)
			}		

			function Text_print_optimize($text , $width)	
			{	
				while   ($this->GetStringWidth($text)  >  $width)  :
					  {   
							 $text = substr($text,0,strlen($text) -1 );
					   }
				endwhile;
				return $text;
			}

			function h_MultiCellThaiCut($w,$h,$txt,$border=0,$align='J',$fill=0)
			{
				//Output text with automatic or explicit line breaks
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$s = $this->thaiCutLinePDF($s, $w, "\n");
//				echo "***$s<br>";
				$sub_s = explode("\n", $s);
				$cell_h = count($sub_s) * $h;
				
				return $cell_h;
			} // end function

			function MultiCellThaiCut($w,$h,$txt,$border=0,$align='J',$fill=0,$f_print=true)
			{
				//Output text with automatic or explicit line breaks
//				$border="TRHBL";
//				$fill = "FF0000";
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$b=0;
				if($border)
				{
					if($border==1)
					{
						$border='LTRB';
						$b='LRT';
						$b2='LR';
					}
					else
					{
						$b2='';
						if(strpos($border,'L')!==false)
							$b2.='L';
						if(strpos($border,'R')!==false)
							$b2.='R';
						$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
					}
				}
//				echo "***$s<br>";
				$s = $this->thaiCutLinePDF($s, $w, "\n");
				$sub_s = explode("\n", $s);
				for($ii = 0; $ii < count($sub_s); $ii++) {
//					echo "$ii-$sub_s[$ii]|<br>";
					if($border && $ii==1)
						$b=$b2;
					if($border && $ii == (count($sub_s)-1) && strpos($border,'B')!==false)
						$b.='B';
					if ($f_print)
						$this->Cell($w,$h,trim($sub_s[$ii]),$b,2,$align,$fill);
					$str_w = $this->GetStringWidth($sub_s[$ii]);
//					echo "MthaiCut sub_s [$ii]=".$sub_s[$ii]." ($w::$str_w) $h,$txt,$border,$align,$fill, ".($f_print?"print":"")."<br>";
				}
				$this->x=$this->lMargin;
				
				return $sub_s;
			} // end function

			// �Ѵ��÷Ѵ����
			function thaiWrapPDF($dataIn, $delim, $w){ // $w �繤������ҧ�ͺࢵ�ͧ ��ͺ�ʴ�����¤
				$cw=&$this->CurrentFont['cw'];
			
				if ($this->GetStringWidth($dataIn) <= $w) {
					$max_pos_i = strlen($dataIn);
				} else {
					$max_pos_i = 0;
					$pos_i = 0;
					$i_ord = 0;
					$ret_len = 0;
					$ch = "";
					$f_match = false;

					$w = $w-$this->GetStringWidth("W");	// Ŵ�������ҧŧ�������ѡ 1 ����ѡ�� W
	
					$max_over_bound_len = 0;
					$select_over_bound_pos = -1;
					
					$arr_match = (array) null;
					$arr_mypatt = (array) null;
					
					$arr_mypatt[] = implode("|", $this->specword);	// �Ӿ����
					$arr_mypatt[] = implode("|", $this->pvword);		// �Ӫ��ͨѧ��Ѵ
					$arr_mypatt[] = implode("|", $this->amword);		// �Ӫ��������
					foreach ($this->arr_word_thai_cut as $key => $value) {
//						echo "key:$key-->value:$value<br>";
						$arr_mypatt[] = $value;
					}
					$arr_mypatt[] = "�([�-�])(�|�|�)?��(��)?";		// ������ ����� ��� ��� ���� ������ ੾��
					$arr_mypatt[] = "��(?:�|�|�á��|���|������|�ԡ)";		// �ҵ����� �ҵ�� �ҵ�ҡ��
					$arr_mypatt[] = "�([�-�])�([�|�|�|�])?§";	 // ��§ ����§ ����§ ����§ 
					$arr_mypatt[] = "�([�-�])�([�|�|�|�])?�([�|�])";		// ���ͧ ���ͧ ����ͧ ���ͧ ����͹ ��͹
					$arr_mypatt[] = "(�|�)([�-�])([����])?([�|�|�|�|�|�|�|�|�])?([�|�|�|�])";	 // ���� ᡧ �Χ �ç ��� �ŧ ᴧ �ǧ ᵧ ���� ��� ὧ ��ŧ ��� ���� Ἱ �ʹ
					$arr_mypatt[] = "�([�-�])([�|�|�|�])�"; // ��� ��� ��� ��� ��� ���� ����
					$arr_mypatt[] = "[�-�]([�|�|�|�])?�[�|�|�|�|�|�]";  // �ͧ �͹ �͹ ��� ��� ��͹ ��͹ ��͹ ��͹ ��͹ ��͹ ��͹ 
					$arr_mypatt[] = "[�-�]([�|�|�|�])?�[�|�|�|�|�|�|�|�|�]";  // ��� �ҧ �ҹ ��� ��ҧ ��ҧ ��ҧ ���� ����
					$arr_mypatt[] = "[�-�]([�|�|�|�])?�[�|�|�|�]";  // ��� �ǹ ��ǹ ��ǹ ��ǹ �ǹ
					$arr_mypatt[] = "[�-�][�|�|�|�|�]?([�|�])([�|�|�|�])?";  // ���� ��� �� ��� ��� �� ��� ���
					$arr_mypatt[] = "[�-�][�|�]([�|�|�|�])([�-�])?";  // �� �ٴ �� �ا
					$arr_mypatt[] = "[�-�](?:�|ѹ|��)"; // ��� �ѹ ���
					$arr_mypatt[] = "[�-�]�([�|�|�|�])?�"; // ���� ���� ��� ���� ���� ���� ��� ����
					$arr_mypatt[] = "[�-�]�([�|�|�|�])?([�|�|�|�|�|�|�|�])"; // ��� ��� ��� ��� �ѡ �ѡ ��� ���� ��� ��� �Ѻ ��� �ѹ �Ѵ �Ѵ �Ѵ
					$arr_mypatt[] = "�[�-�]([�|�|�|�])?([�|�|�|�|�])";	 // ⡧ �� ⴴ ⩴ ⪤ ��� �� �� �� �� ��
					$arr_mypatt[] = "[�-�]([�|�|�])(�)([�|�|�|�|�|�|�|�|�])";	 // ��ҧ ����� ���� ���� ���� ���� ��ҧ ��Һ ��Һ
					$arr_mypatt[] = "[�|�][�-�]([�|�|�|�])?([�|�|�|�|�])";	 // ��,�,�..

					foreach ($arr_mypatt as $key => $mypatt) {
						$stpos=0;
//						echo "mypatt=$mypatt<br>";
						while($stpos >= 0) {
							if (preg_match("/($mypatt)/",$dataIn,$match,PREG_OFFSET_CAPTURE,$stpos)) {
								$f_match = true;
								$pos_i = $match[0][1];	// match position
								$len = strlen($match[0][0]);	// word match
//								echo "......match [".$match[0][0]."] pos_i=$pos_i , len=$len<br>";
								$stpos = $pos_i + $len + 1;		// ���˹� ���ҶѴ�
								$match_pos_w = $this->GetStringWidth(substr($dataIn,0,$pos_i));
								$match_len_w = $this->GetStringWidth(substr($dataIn,0,$pos_i+$len));
								if ($match_pos_w < $w && $w < $match_len_w) {	// ��Ҥӷ���ͤҺ��鹢ͺ���ҧ
									if ($max_over_bound_len < $len) {
										$max_over_bound_len = $len;
										$select_over_bound_pos = $pos_i;
										$msg = "over max-".$match[0][0]."($pos_i,$len)";
									} else $msg = "over-".$match[0][0]."($pos_i,$len)";
								} else if ($match_len_w <= $w) {	// ��Ҥӷ���͹��¡�����ҡѺ���蹢ͺ���ҧ
									$pos = "";
									if (($pos_i+$len) > $max_pos_i) {
										$max_pos_i = $pos_i+$len;
										$ret_len = $len;
										$pos = $max_pos_i;
										$msg = "max-".$match[0][0]."($pos_i,$len)";
									} else $msg = "";
								}
								if ($msg)	$arr_match[] = "$key-".$msg;
							} else {
								$stpos = -1;
							} // end if (preg_match
						}  // end while loop
					}	// end foreach ($arr_mypatt as $mypatt)

					if ($select_over_bound_pos >= 0) {	// ����դӷ��Һ��鹢ͺ���ҧ �еѴ���鹤ӹ�����
						$max_pos_i = $select_over_bound_pos;
					} else {
						if (preg_match("/[0-9]/",$dataIn,$match)) { // �繵���Ţ
							$f_match = true;
							$pos_i = strpos($dataIn, $match[0]);
							$len = 0;
							for($i=$pos_i; $i < strlen($dataIn); $i++) {
								$ch = substr($dataIn,$i,1);
								if (($ch >= "0" && $ch <= "9") || $ch == "." || $ch == ",") {	// �繪ش����Ţ
									$len++;
								} else break;
							}
							if ($len > 0) {
								$num_match = substr($dataIn,0,$pos_i+$len);
								$match_w = $this->GetStringWidth($num_match);
								$pos = "";
								if (($pos_i+$len) > $max_pos_i && $match_w < $w) {
									$max_pos_i = $pos_i+$len;
									$ret_len = $len;
									$pos = $max_pos_i;
								}
								$arr_match[] = "".($key+1)."-".$num_match."(match_w=$match_w)(pos=$pos)";
							}
						}

						$spec_ch = array("{", "(", "[","*");	// �ѭ���ѡɳ� �Ѵ 任���¤��ѧ
						$max_spec_ch = -1;
						for($jj = 0; $jj < count($spec_ch); $jj++) {
							$spec_ch_i = strpos($dataIn, $spec_ch[$jj]);
							if ($spec_ch_i !== false) {
								$match_w = $this->GetStringWidth(substr($dataIn,0,$spec_ch_i));
//								echo "****... (sub-22 $jj spec_ch_i=$spec_ch_i, spec_ch[$jj]=".$spec_ch[$jj]." [".substr($dataIn,0,$spec_ch_i)."($match_w)]) ...****";
								if ($spec_ch_i > $max_spec_ch && $match_w < $w) {
									$max_spec_ch = $spec_ch_i;
								}
							}
						}
						if ($max_spec_ch >= 0) {
							$pos = "";
							if ($max_spec_ch > $max_pos_i) {
								$max_pos_i=$max_spec_ch;
								$ret_len = $max_spec_ch;
								$pos = $max_pos_i;
							}
							$match_w = $this->GetStringWidth(substr($dataIn,0,$max_pos_i));
							$arr_match[] = "".($key+2)."-".substr($dataIn,0,$max_pos_i)."(match_w=$match_w)(pos=$pos)";
						}

						$spec_ch = array(" ", ".", ",", ")", "/", "\\","@","$","%","&","#","!","<",">","]");	// �Ѵ���Դ仡Ѻ����¤˹��
						$max_spec_ch = -1;
						for($jj = 0; $jj < count($spec_ch); $jj++) {
							$spec_ch_i = strpos($dataIn, $spec_ch[$jj]);
							if ($spec_ch_i !== false) {
								$match_w = $this->GetStringWidth(substr($dataIn,0,$spec_ch_i+1));
								if ($spec_ch_i+1 > $max_spec_ch && $match_w < $w) {
									$max_spec_ch = $spec_ch_i+1;
								}
							}
						}
						if ($max_spec_ch >= 0) {
							$pos = "";
							if ($max_spec_ch > $max_pos_i) {
								$max_pos_i=$max_spec_ch;
								$ret_len = $max_spec_ch;
								$pos = $max_pos_i;
							}
							$match_w = $this->GetStringWidth(substr($dataIn,0,$max_pos_i));
							$arr_match[] = "".($key+3)."-".substr($dataIn,0,$max_pos_i)."(match_w=$match_w)(pos=$pos)";
						}
					}
//					echo "match.(dataIn=$dataIn, w=$w)..[";
//					echo "".implode("|",$arr_match)."";
//					echo "] (max_pos_i=$max_pos_i)<br>";
			
	//				if ($max_pos_i==0 && !$f_match) {
	/*
					if ($max_pos_i==0) {
						$max_i = 0;
						for($i=0; $i < strlen($dataIn); $i++) {
							$match_w = $this->GetStringWidth(substr($dataIn,0,$i+1));
							if ($match_w > $w) { $max_i = $i; break; }
						}
	//					echo "[$dataIn($max_i)] ���������͹�� �<br>";
						if ($max_i) {
							$max_pos_i = $max_i;
							$ret_len = $max_i;
						} else {
							$max_pos_i = 5;
							$ret_len = 5;
						}
					}
	*/				
	//				$ch = substr($dataIn,$max_pos_i+$ret_len,1);
					$ch = substr($dataIn,$max_pos_i,1);
					if (strpos("���������������",$ch) !== false) { // ��ҶѴ� �繵�Ƿ���ͧ���ѡ�ù� ���ǡ仴���
						$ret_len++;
						$max_pos_i++;
					}
				} // end if first check
				
//				return $max_pos_i+$ret_len;
				return $max_pos_i;
			} // end function

			function thaiWrapPDF_old($dataIn, $delim){
				$specword = array("�Ҫ���", "�Ѱ���", "͸Ժ��", "��к��", "���˹�", "�����","�ѧ��Ѵ","����ͧ","��Ժѵ�","�ѭ��","���˹�ҷ��", "���", "���õ���","��è�", "��ç", "���˹�", "�ç���¹", "��Һѳ�Ե", "�ѳ�Ե", "������", "�ӹҭ", "��ʹ", "��ʵ��", "�Ѳ��", "�Ҿ", "�ѹ���", "�Ţ���", "������", "��º��", "�ѡ��������", "��˹�", "�ѹ", "��͹", "��", "���", "���", "����", "�Ҹ�ó�", "�к�", "�Ѳ��", "��è�", "ʡ��", "���", "ʶҹ�", "ʶҾ�", "ʶһ��", "ʶһ�ԡ", "ʶҺѹ", "�ѡ�", "�ҷԵ��", "�ѹ���", "�ѧ���", "�ظ", "����ʺ��", "�ء��", "�����", "���Ҥ�", "����Ҿѹ��", "�չҤ�", "����¹", "����Ҥ�", "�Զع�¹", "�á�Ҥ�", "�ԧ�Ҥ�", "�ѹ��¹", "���Ҥ�", "��Ȩԡ�¹", "�ѹ�Ҥ�","�˹��","��","���ѹ�͡","���ѹ��","��§","���ҹ","���Ǩ","����","��Һ��","�ҡ��","�����","�ӹҹ","��ú��","�ٸ�","ᾷ��","�ٵ�","��ԭ��","�����","����","�Ң�","��ж�","�Ѹ��","��ԭ��","�֡��","��ا"); // ������੾��������
				$pvword = array("��ا෾��ҹ��","��ا෾�","��ا෾","���.","���","��й��","��к��", "�ҭ������", "����Թ���", "��ᾧྪ�", "�͹��", "�ѹ�����", "���ԧ���", "�ź���", "��¹ҷ", "�������", "�����", "��§���", "��§����", "��ѧ", "��Ҵ", "�ҡ", "��ù�¡", "��û��", "��þ��", "����Ҫ����", "�����ո����Ҫ", "������ä�", "�������", "��Ҹ����", "��ҹ", "���������", "�����ҹ�", "��ШǺ���բѹ��", "��Ҩչ����", "�ѵ�ҹ�", "��й�������ظ��", "�����", "�ѧ��", "�ѷ�ا", "�ԨԵ�", "��ɳ��š", "ྪú���", "ྪú�ó�", "���", "����", "�����ä��", "�ء�����", "�����ͧ�͹", "��ʸ�", "����", "�������", "�йͧ", "���ͧ", "�Ҫ����", "ž����", "�ӻҧ", "�Ӿٹ", "���", "�������", "ʡŹ��", "ʧ���", "ʵ��", "��طû�ҡ��", "��ط�ʧ����", "��ط��Ҥ�", "������", "��к���", "�ԧ�����", "��⢷��", "�ؾ�ó����", "����ɮ��ҹ�", "���Թ���", "˹ͧ���", "˹ͧ�������", "��ҧ�ͧ", "�ӹҨ��ԭ", "�شøҹ�", "�صôԵ��", "�ط�¸ҹ�", "�غ��Ҫ�ҹ�");
				$amword = array("����", "����ҧᴧ", "��º���", "����ԭ", "Ǫ�ú����", "��ᴹ", "�����ѡ", "�������", "�����ú���", "���෾", "˹ͧ��", "�֧����ѹ", "���˹��", "�ѧ��", "�Ҥ��", "����֧", "�ǹ���", "���Թ�дǡ", "��ҹ��", "�ҧ�", "⾸����", "�ҡ���", "�Ѵ�ŧ", "��ҹ��", "���¤", "��;���", "������ʴ��", "����С�", "�����ǧ", "�ͧ������", "�ѧ��к���", "����ǹ", "��Ң�ѭ", "��ҹ�Т������", "˹ͧ����", "���¡����", "��ҡ�дҹ", "��ҹ�ǹ", "����ҧ�ҧ�Ǫ", "��ҹ��ҧ", "�ҧ������", "��ջ�Шѹ��", "�͹਴���", "�ͧ����ͧ", "����ء", "���ͧ", "˹ͧ˭���", "��ᾧ�ʹ", "��ê�����", "�͹���", "�ҧ�Ź", "�����ҹ", "�ط�����", "��з���ẹ", "��ҹ���", "�ҧ����", "������", "������", "˹ͧ˭�һ��ͧ", "����", "����ҧ", "��ҹ�Ҵ", "��ҹ����", "�觡�Шҹ", "��º���", "�Ѻ���", "�ҧ�оҹ", "�ҧ�оҹ����", "��ҳ����", "����Թ", "��������ʹ", "��������", "�ҹʡ�", "�ҹ�С�", "��ҧ", "�Իٹ", "�����˭�", "���Ǵ", "�������", "���ʧ", "�Һ͹", "����˭�", "�ҡ��ѧ", "��͹�Ժ����", "�Ԫ�", "����", "�����", "�ҧ�ѹ", "��Ӿ�ó��", "�����ó�", "��о���", "���Ե��", "��ҧ��ҧ", "����������õ�", "�����֧", "�ǹ��ǧ", "�Թ��", "�ǹ���ԡ", "�лҧ", "�Ҿ��", "����ѹ��", "��ͧ����", "�����֡", "�ع", "��§��", "��§��ǹ", "�͡����", "��", "����", "�٫ҧ", "�١�����", "���§���", "��ҹ�Ң��", "������¹", "����", "�����ʹ", "�ѧ�����", "���§", "��ɮ�", "�Ҵ���ҭ", "�����", "�Ҫ��ʹ", "������", "�ǹ��ع", "�ҡ���ٹ", "��պ�þ�", "�����պ�þ�", "��Һ͹", "�ҧ���", "��Ҿ����", "��չ��Թ���", "⤡⾸��", "˹ͧ�ԡ", "�й����", "��§�ͧ", "�ԧ", "�ҹ", "���ᴴ", "���ѹ", "��§�ʹ", "������", "�������", "���§������", "���������", "���§��", "�ع���", "�������ǧ", "������", "���§��§���", "�����ǧ", "�ع���", "���", "��������§", "����ҹ���", "ʺ���", "�ҧ�м��", "���µ��", "�á���", "����ʧ", "˹ͧ��� ", "��þ������", "���������", "�Ҥ��", "��ҵ��", "�����", "����Ф���", "�Ҵ���", "�ҡ���", "���ǧ��", "����Թ", "����Һ�", "���¹�����", "����", "��������", "�Ѿ�ѹ", "���ҧ������", "˹ͧ�ҧ", "˹ͧ�����ҧ", "��ҹ���", "�ҹ�ѡ", "���¤�", "�ç��", "��ͧ�ҹ", "�ҳ����ѡɺ���", "��ͧ��ا", "��ҹ��е���", "�ҹ��к��", "���·ͧ�Ѳ��", "�ҧ���ҷͧ", "�֧���Ѥ��", "�����չ��", "��ҹ�ҡ", "�����", "������Ҵ", "����ͧ�ҧ", "����ʹ", "�����", "�����ҧ", "�ѧ���", "��һ��", "��ҹ��ҹ�ҹ���", "�������", "�������", "����Ѫ�����", "������ç", "���ä�š", "��չ��", "����������", "�����", "�ҵԵ�С��", "�ҧ�С�", "�ҧ��з���", "���������", "�Ѵ�ʶ�", "�ѧ�ͧ", "�Թ�л�ҧ", "�ѧ���¾ٹ", "⾸���зѺ��ҧ", "�оҹ�Թ", "�ҧ��Źҡ", "⾷���", "�������", "�Ѻ����", "�ҡ����", "�֧���ҧ", "���ҧᴹ�Թ", "��ͧ���", "��ҧ��", "⤡����ؾ�ó", "��ԭ��Ż�", "⾹�����", "�پҹ", "�ҹù����", "�ش���ͤ�", "��ҹ�ѹ", "��һҡ", "�����෹", "��ҹᾧ", "�ҵؾ��", "���", "�óٹ��", "���ʧ����", "������", "⾹���ä�", "�ҷ�", "�ѧ�ҧ", "�Ԥ��������", "�͹���", "����ǧ", "�Ӫ���", "���ҹ�˭�", "˹ͧ�٧", "����ͧ", "������", "��§���", "�������", "���ᵧ", "������", "����ԧ", "�ҧ", "������", "�����", "�ѹ��ҵͧ", "�ѹ��ᾧ", "�ѹ����", "�ҧ��", "�ʹ", "������", "������", "�����", "���§�˧", "���§��", "�»�ҡ��", "����ҧ", "����͹", "�������", "�����§����", "�ǧ��", "�ǧ��", "�����", "����", "��ҹ���", "���", "�����Ǫ�ҧ", "��ҫҧ", "��ҹ��", "���§˹ͧ��ͧ", "�������", "��Ф�", "��������", "���", "�����", "�ѧ�˹��", "�Թ", "����ԡ", "����", "ʺ��Һ", "��ҧ�ѵ�", "��͹", "��һ��", "��ӻҴ", "�����", "�÷ͧ", "������", "���ѧ", "�о��", "����ҹ", "ີ�", "�ѹ�ѧʵ�", "����", "��觸���", "����", "���ѹ", "�ç�Թѧ", "�ҡ�", "�����", "����", "����", "�������", "����Ҥ�", "���", "�ؤ��Թ", "���˧�-š", "���˧�Ҵ�", "����", "�������ͧ", "�ҧ���", "��й��", "���Ե", "˹ͧ�͡", "�ҧ�ѡ", "�ҧࢹ", "�ҧ�л�", "�����ѹ", "������Һ�ѵ�پ���", "���⢹�", "�չ����", "�Ҵ��кѧ", "�ҹ����", "����ѹ�ǧ��", "����", "������", "�ҧ�͡�˭�", "���¢�ҧ", "��ͧ�ҹ", "���觪ѹ", "�ҧ�͡����", "�ҧ�ع��¹", "������ԭ", "˹ͧ��", "��ɮ���ó�", "�ҧ��Ѵ", "�Թᴧ", "�֧����", "�Ҹ�", "�ҧ����", "��بѡ�", "�ҧ������", "������", "��ͧ��", "�Ҫ���", "�Ҵ�����", "�Ѳ��", "�ҧ�", "��ѡ���", "������", "�ѹ�����", "�оҹ�٧", "�ѧ�ͧ��ҧ", "��ͧ�����", "�ҧ��", "����Ѳ��", "��觤��", "�ҧ�͹", "��ҹ�����", "�ҧ���", "�ҧ���", "��л��ᴧ", "�����ط�਴���", "�ҧ��Ҹ�", "�ҧ����", "�ҧ�˭�", "�ҧ��Ƿͧ", "�ù���", "�ҡ���", "��ù������", "�ǧ��ҷ", "��ͧ��ǧ", "�ѭ����", "˹ͧ����", "�Ҵ�������", "���١��", "���⤡", "�٤�", "�������", "�����ǧ", "�ҧ��", "�ҧ���", "�ҧ���Թ", "�ҧ���ѹ", "�ҧ���ѹ", "�ѡ���", "�Ҫ�", "�Ҵ�����ǧ", "�ѧ����", "�ʹ�", "�ҧ����", "�ط��", "����Ҫ", "��ҹ�á", "���", "������", "⾸��ͧ", "��ǧ��", "����ɪ�ªҭ", "�����", "�Ѳ�ҹԤ�", "⤡���ç", "��ºҴ��", "������", "��ҹ����", "�����ǧ", "����ʶ�", "⤡��ԭ", "��ʹ��", "˹ͧ��ǧ", "��ҹ���", "�ҧ�Шѹ", "���ºҧ�Шѹ", "��������", "���¾����", "�ӾѴ", "�ӷѺ", "�˹�ͤ�ͧ", "������", "�л�", "�С��Ƿ��", "�С��ǻ��", "���к���", "�Ѻ�ش", "��������ͧ", "�з��", "��ҧ", "��觤�", "�ҭ����ɰ�", "�͹�ѡ", "�������", "��оЧѹ", "���", "��Ҫ��", "�����Ѱ�Ԥ�", "��ҹ�Ңع", "���", "��ҩҧ", "��ҹ�����", "��ҹ�����", "��¹��", "���§���", "����ʧ", "�ع�Թ", "��º���", "����Ǵ�", "��о�ѹ", "������", "��ҹ�͹", "�����", "������", "��к���", "�آ���ҭ", "�����", "�з��", "��ѧ�ǹ", "����", "�����", "������", "���", "��觵��", "ʷԧ���", "�й�", "�ҷ��", "෾�", "�к������", "��⹴", "������Թ��", "������Թ���", "�ѵ����", "����", "�Ҵ�˭�", "�������", "�ǹ��§", "�ҧ����", "�ԧ˹��", "��ͧ�����", "��ҹ���", "�ǹⴹ", "��觤ǹⴹ", "�ǹ���ŧ", "��觷���", "����", "�Ч�", "�������", "�йѧ", "�ѹ�ѧ", "�Ҵ�", "������", "�ѧ������", "��ҹ���ҧ", "��Шѹ����", "������⾸�", "������ʶ", "⤡�պ", "��ѭ�����", "�Ҿ����", "�Ѳ�ҹ��", "��ͧ�Ҵ", "�ҡ���", "��ҹ��", "ͧ��ѡ��", "�ҩ��è�", "⤡�٧", "�ѧ����ó�", "�ú���", "��ԧ�ҧ", "��", "��ҹ�������", "�ҡ���", "��ҹ⤡", "�Ԫ��", "�Ѻ��", "�ͧ�ʹ�ѹ", "��ͧ��ҧ", "�ͧ", "�٧���", "�蹪��", "�ͧ", "�ѧ���", "˹ͧ��ǧ��", "������", "��ҹ��ǧ", "�ҹ���", "���", "���", "����ѧ��", "��", "���§��", "��觪�ҧ", "��§��ҧ", "������", "�ѹ���آ", "�������", "�ͧ��", "����§", "��պح���ͧ", "�ҡ�ҧ", "����ó����", "⹹�ѧ", "��ҹ���", "������", "��", "���ҧ���", "˹ͧ�ʧ", "���٧", "�Ժ�����ѡ��", "������", "��Шѡ����ŻҤ�", "�Ҵ�ǧ", "��§�ҹ", "�ҡ��", "��ҹ����", "������", "������", "������", "�ѧ�оا", "�١�д֧", "����ǧ", "�Ң��", "�����ѳ", "˹ͧ�Թ", "��Һ��", "�֧���", "����ԭ", "⾹�����", "������", "�����§����", "�ѧ��", "ૡ�", "�ҡ�Ҵ", "�֧⢧�ŧ", "�������", "��觤���", "�����", "������", "�ѵ��һ�", "⾸��ҡ", "ᡴ�", "���������", "�ѹ���Ԫ��", "��§�׹", "�ú��", "����͡", "��Ѥ����Ծ����", "�һջ���", "�Ҵٹ", "�ҧ�����Ҫ", "�ش�ѧ", "��蹪�", "��غ", "�ɵ������", "�����ѵ��", "���þѡ�þ��ҹ", "��Ѫ����", "�����", "⾹�ͧ", "⾸����", "˹ͧ�͡", "�������", "����ó����", "⾹����", "�Ҩ����ö", "���Ǵ�", "�������", "�ѧ���", "��§��ѭ", "˹ͧ��", "�������ǧ", "����", "�������", "��ͧ��", "�ةԹ���³�", "��ǧ", "�ҧ��Ҵ", "�������", "���ʢѹ��", "����ǧ", "��Ҥѹ�", "˹ͧ�ا���", "����", "���¼��", "������", "�Ҵ�", "�͹�ҹ", "��ͧ���", "���������", "�ش�ҡ", "��óҹԤ�", "�ѧ⤹", "���Ԫ����", "�Ԥ�����ٹ", "����ٹ", "�ӵҡ���", "��ҹ��ǧ", "�ҡ���ӹ��", "���", "��ǧ����Ժ", "���Թ���Һ", "�ӹҨ��ԭ", "�ʹҧ��Ԥ�", "��ǵоҹ", "�Ժ���ѧ�����", "������", "⾸����", "���ç", "�͹��ᴧ", "���Թ��", "�������ش�", "�����Ҫǧ��", "�����ѡ���", "������", "�ҵ��", "�����������", "���ҧ����ǧ��", "��Ӣ��", "����ó����", "�������", "�ش���", "�����͹���", "��ҵ���", "��Ҫ�Ъ��", "����ѧ", "��ԧ����", "����ԭ", "��ҹ����", "�͹���ä�", "�ɵ�����ó�", "˹ͧ���ᴧ", "�ѵ����", "���˹稳ç��", "˹ͧ��������", "෾ʶԵ", "������", "��ҹ��", "�駤���", "�͹���", "�ѡ�ժ����", "�Թʧ��", "�Ѻ�˭�", "����ҳ��Ѳ��", "��ҹ�������", "�ѧ����", "�Ѻ�", "⤡ྪ�", "���ҧ��ѡ", "��ҹ���", "��������ҹ", "����ٳ", "�ҹ��ҹ", "����ӹҨ", "���ѧ", "��ҹ�ҧ", "����׹", "˹ͧ����", "����", "�ժ���", "��Ӿͧ", "�غ��ѵ��", "��йǹ", "��ҹ��", "���¹���", "��", "�ǧ�˭�", "�ǧ����", "˹ͧ�ͧ��ͧ", "�����§", "�ѭ�Ҥ���", "����", "���ǹ��ҧ", "�ټ���ҹ", "���٧", "⤡⾸����", "˹ͧ�Ҥ�", "��ҹ�δ", "⹹����", "�ش�Ѻ", "˹ͧ��ǫ�", "�����һ�", "⹹���Ҵ", "˹ͧ�ҹ", "��觽�", "���ҹ", "��ոҵ�", "�ѧ������", "��ҹ�ا", "˹ͧ�������", "�ѡ�Ҫ", "⪤���", "��ҹ�ع��", "⹹��", "⹹�٧", "�������ʧ", "����˭�", "��з��", "�ѡ�����", "�����", "�����ŧ", "����ǧ", "�٧�Թ", "���������", "�դ���", "�ҡ��ͧ", "˹ͧ�ح�ҡ", "��ʹ���ҧ", "⹹ᴧ", "�ѧ�������", "෾��ѡ��", "��зͧ��", "�ӷ�������", "������", "�մ�", "�Ф��-��ʧ����", "⹹���", "����ѧ", "�ҧ�ͧ", "˹ͧ���", "���ҹ����", "���⤹���", "��ҹ��Ǵ", "�ط�ʧ", "�ӻ������", "ʵ֡", "�Ф�", "��⾸��", "˹ͧ˧��", "��Ѻ��Ҫ��", "�����Ҫ", "⹹����ó", "�ӹ�", "��ҹ�����¾���", "⹹�Թᴧ", "��ҹ��ҹ", "᤹��", "����ź���", "��ҵ��", "������", "����ҷ", "�Һ�ԧ", "�ѵ�����", "ʹ�", "�բ�����", "�ѧ��", "�Ӵǹ", "���ç�Һ", "���઴", "������ѡ", "��ճç��", "����Թ�Թ���", "⹹����³�", "�ҧ�������", "�ѹ�������", "�ѹ���ѡ��", "�آѹ��", "�ú֧", "��ҧ����", "�ع�ҭ", "�������", "�ط���þ����", "�֧��þ�", "���·Ѻ�ѹ", "⹹�ٳ", "⹹�ٹ", "����ѵ��", "�������§", "�ѧ�Թ", "���ԧ��", "ອ��ѡ��", "�����", "⾸���������ó", "�����Ҵ", "⢧����", "���ͧ�", "���Ұ", "പ�ش�", "�Ҩ�����", "����׹", "�س��ԡ", "��С�þת��", "�ش���ǻ��", "��Ҫ�ҧ", "�Թ������", "������", "�Ѵ�ԧ��", "��þ��", "��ä����", "�ѹ��", "˹ͧ�����", "�Թ���", "�觤��", "˹ͧ�", "�����ᴧ", "˹ͧ᫧", "��ҹ���", "�͹�ش", "˹ͧⴹ", "��оط��ҷ", "������", "�ǡ����", "�ѧ��ǧ", "��ҹ�֧", "˹ͧ�˭�", "�ҧ���ا", "�ҹ�ͧ", "���ʹԤ�", "����Ҫ�", "����ժѧ", "�ѵ�պ", "��ͷͧ", "��Шѹ���", "�ҧ����", "������ѧ", "��ҧ�ҧ", "��ҹ�ҧ", "�ŧ", "�ѧ�ѹ���", "��ҹ����", "��ǡᴧ", "�Ҫ����", "�Ԥ��Ѳ��", "�Һ���", "��ا", "�������", "�觹����͹", "�Т��", "�����ԧ��", "��´��", "���ҧ���", "��������", "�ҤԪ��ٯ", "�Ӿظ", "��ͧ�˭�", "����ԧ", "������", "�����ͺ", "��Сٴ", "��Ъ�ҧ", "�ҧ����", "�ҧ���������", "�ҧ�С�", "��ҹ⾸��", "�����ä��", "�Ҫ����", "ʹ�����", "�ŧ���", "��ҵ���º", "��ͧ���͹", "��Թ������", "�Һѧ", "���§���", "�ؤ��", "����Ҫ���", "�Ҹ�ó���"); // ������੾��������
//				$deptword = array("�ӹѡ�ҹ�Ţҹء����ҡ�ا෾��ҹ��", "�ӹѡ�ҹ�Ţҹء�ü������Ҫ��á�ا෾��ҹ��", "�ӹѡ�ҹ��С�����â���Ҫ��á�ا෾��ҹ��", "�ӹѡ��Ѵ��ا෾��ҹ��", "�ӹѡ���ᾷ��", "�ӹѡ͹����", "�ӹѡ����֡��", "�ӹѡ����¸�", "�ӹѡ����к�¹��", "�ӹѡ��ä�ѧ", "�ӹѡ�ȡԨ", "�ӹѡ��è�Ҩ���Т���", "�ӹѡ�ѧ���ͧ", "�ӹѡ��ͧ�ѹ��к�����Ҹ�ó���", "�ӹѡ������ҳ��ا෾��ҹ��", "�ӹѡ�ط���ʵ����л����Թ��", "�ӹѡ����Ǵ����", "�ӹѡ�Ѳ����� ���� ��С�÷�ͧ�����", "�ӹѡ�Ѳ���ѧ��", "�ٹ���Ѻ����ͧ��ͧ���¹", "�ӹѡ�ҹࢵ��й��", "�ӹѡ�ҹࢵ������Һ�ѵ�پ���", "�ӹѡ�ҹࢵ����ѹ�ǧ��", "�ӹѡ�ҹࢵ�ҧ�ѡ", "�ӹѡ�ҹࢵ�����ѹ", "�ӹѡ�ҹࢵ�ҹ����", "�ӹѡ�ҹࢵ���Ե", "�ӹѡ�ҹࢵ����", "�ӹѡ�ҹࢵ���¢�ҧ", "�ӹѡ�ҹࢵ���⢹�", "�ӹѡ�ҹࢵ�ҧ�л�", "�ӹѡ�ҹࢵ�ҧࢹ", "�ӹѡ�ҹࢵ�չ����", "�ӹѡ�ҹࢵ�Ҵ��кѧ", "�ӹѡ�ҹࢵ˹ͧ�͡", "�ӹѡ�ҹࢵ������", "�ӹѡ�ҹࢵ��ͧ�ҹ", "�ӹѡ�ҹࢵ�ҧ�͡�˭�", "�ӹѡ�ҹࢵ�ҧ�͡����", "�ӹѡ�ҹࢵ���觪ѹ", "�ӹѡ�ҹࢵ������ԭ", "�ӹѡ�ҹࢵ˹ͧ��", "�ӹѡ�ҹࢵ�ҧ�ع��¹", "�ӹѡ�ҹࢵ��ɮ���ó�", "�ӹѡ�ҹࢵ�͹���ͧ", "�ӹѡ�ҹࢵ��بѡ�", "�ӹѡ�ҹࢵ�Ҵ�����", "�ӹѡ�ҹࢵ�֧����", "�ӹѡ�ҹࢵ�ҷ�", "�ӹѡ�ҹࢵ�ҧ������", "�ӹѡ�ҹࢵ�ҧ����", "�ӹѡ�ҹࢵ�Ҫ���", "�ӹѡ�ҹࢵ��ͧ��", "�ӹѡ�ҹࢵ������", "�ӹѡ�ҹࢵ�ҧ��Ѵ", "�ӹѡ�ҹࢵ����ͧ", "�ӹѡ�ҹࢵ�Թᴧ", "�ӹѡ�ҹࢵ�ǹ��ǧ", "�ӹѡ�ҹࢵ�Ѳ��", "�ӹѡ�ҹࢵ�ҧ�", "�ӹѡ�ҹࢵ��ѡ���", "�ӹѡ�ҹࢵ������", "�ӹѡ�ҹࢵ�ѹ�����", "�ӹѡ�ҹࢵ�оҹ�٧", "�ӹѡ�ҹࢵ�ѧ�ͧ��ҧ", "�ӹѡ�ҹࢵ��ͧ�����", "�ӹѡ�ҹࢵ�ҧ��", "�ӹѡ�ҹࢵ����Ѳ��", "�ӹѡ�ҹࢵ��觤��", "�ӹѡ�ҹࢵ�ҧ�͹");

				$min_pos_i = strlen($dataIn);
				$pos_i = 0;
				$i_ord = 0;
				$ret_len = strlen($dataIn);
				$ch = "";
				
				$mypatt = implode("|", $specword);
				if (preg_match("/($mypatt)/",$dataIn,$match)) {
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
//						if (preg_match("/(�([�-�])(�|�|�)?��(��)?)/",$dataIn,$match)) 
//							echo "1.������-->$dataIn ($ret_len) ($pos_i < $min_pos_i)<br>";
					}
				} 
				$mypatt = implode("|", $pvword);
				if (preg_match("/($mypatt)/",$dataIn,$match)) {
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				$mypatt = implode("|", $amword);
				if (preg_match("/($mypatt)/",$dataIn,$match)) {
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 

				if ($this->arr_word_thai_cut) {	// $arr_word_thai_cut ��ҹ�����ҡ pdf_wordarray_thaicut.php
					foreach ($this->arr_word_thai_cut as $key => $value) {
//						echo "key:$key-->value:$value<br>";
						$mypatt = $value;
						if (preg_match("/($mypatt)/",$dataIn,$match)) {
							$pos_i = strpos($dataIn, $match[0]);
							if ($pos_i < $min_pos_i) {
								$min_pos_i = $pos_i;
								$ret_len = strlen($match[0]);
							}
//							echo "***** dataIn($dataIn)->".substr($dataIn, $pos_i, $ret_len)." , match[0]=".$match[0]."<br>";
						} 
					}
				}

				if (preg_match("/(�([�-�])(�|�|�)?��(��)?)/",$dataIn,$match)) { // // ������ ����� ��� ��� ���� ������ ੾��
					$pos_i = strpos($dataIn, $match[0]);
//					echo "2.������-->$dataIn ($ret_len) ($pos_i < $min_pos_i)<br>";
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(��(?:�|�|�á��|���|������|�ԡ))/",$dataIn,$match)) { // 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/(�([�-�])�([�|�|�|�])?§)/",$dataIn,$match)) { // ��§ ����§ ����§ ����§ 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/(�([�-�])�([�|�|�|�])?�([�|�]))/",$dataIn,$match)) { // ���ͧ ���ͧ ����ͧ ���ͧ ����͹ ��͹
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/((�|�)([�-�])([����])?([�|�|�|�|�|�|�|�|�])?([�|�|�|�]))/",$dataIn,$match)) { // ���� ᡧ �Χ �ç ��� �ŧ ᴧ �ǧ ᵧ ���� ��� ὧ ��ŧ ��� ����
																																													// Ἱ �ʹ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(�([�-�])([�|�|�|�])�)/",$dataIn,$match)) { // ��� ��� ��� ��� ��� ���� ����
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([�-�][�|�|�|�])?�[�|�|�|�|�|�]/",$dataIn,$match)) {  // �ͧ �͹ �͹ ��� ��� ��͹ ��͹ ��͹ ��͹ ��͹ ��͹ ��͹ 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/([�-�][�|�|�|�])?�[�|�|�|�|�|�|�|�|�]/",$dataIn,$match)) {  // ��� �ҧ �ҹ ��� ��ҧ ��ҧ ��ҧ ���� ����
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/([�-�][�|�|�|�])?�[�|�|�|�]/",$dataIn,$match)) {  // ��� �ǹ ��ǹ ��ǹ ��ǹ �ǹ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/([�-�])([�|�|�|�|�])?([�|�])([�|�|�|�])?/",$dataIn,$match)) {  // ���� ��� �� ��� ��� �� ��� ���
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(�(?:�|ѹ|��))/",$dataIn,$match)) { // ��� �ѹ ���
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([�-�]�([�|�|�|�])?�)/",$dataIn,$match)) { // ���� ���� ��� ���� ���� ���� ��� ����
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([�-�]�([�|�|�|�])?([�|�|�|�|�|�|�|�]))/",$dataIn,$match)) { // ��� ��� ��� ��� �ѡ �ѡ ��� ���� ��� ��� �Ѻ ��� �ѹ �Ѵ �Ѵ �Ѵ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/(�[�-�]([�|�|�|�])?([�|�|�|�|�]))/",$dataIn,$match)) { // ⡧ �� ⴴ ⩴ ⪤ ��� �� �� �� �� ��
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([�-�]([�|�|�])(�)([�|�|�|�|�|�|�|�|�]))/",$dataIn,$match)) { // ��ҧ ����� ���� ���� ���� ���� ��ҧ ��Һ ��Һ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}

//				if (preg_match("/(�([�-�])(�|�|�)?��(��)?)/",$dataIn,$match)) 
//					echo "3.������-->$dataIn ($ret_len) ($pos_i < $min_pos_i)<br>";						
				if ($min_pos_i==$ret_len && $ret_len==strlen($dataIn)) {
					$min_pos_i = 0;
				}
				$ch = substr($dataIn,$min_pos_i+$ret_len,1);
				if (strpos("���������������",$ch) !== false) { // ��ҶѴ� �繵�Ƿ���ͧ���ѡ�ù� ���ǡ仴���
					$ret_len++;
//				} else {
//					$ch = substr($dataIn,$min_pos_i+$ret_len+1,1);
//					if (strpos("�����",$ch) !== false) { // ��ҵ�ǶѴ��ա 1 ��� �繵�ǹ� ��ҵ�ǡ�͹˹��仴���
//						$ret_len++;
//					}
				}
//				if (preg_match("/(�([�-�])(�|�|�)?��(��)?)/",$dataIn,$match)) 
//					echo "4.������-->$dataIn ($ret_len) ($pos_i < $min_pos_i) ($ch)<br>";
//				$spec_ch = array("|", " ", ".", ",", ")", "/", "\\","(","@","$","%","&","*","#","!","<",">","{","[","]");
				$spec_ch = array("|", " ", ".", ",", ")", "/", "\\","@","$","%","&","*","#","!","<",">","]");
				$min_spec_ch = 999999;
				$seltext = substr($dataIn,0,$min_pos_i+$ret_len);
//				echo "seltext=[$seltext]<br>";
				for($jj = 0; $jj < count($spec_ch); $jj++) {
					$spec_ch_i = strpos($seltext, $spec_ch[$jj]);
//					echo "spec_ch[$spec_ch_i] < min_spec_ch[$min_spec_ch]<br>";
					if ($spec_ch_i !== false && $spec_ch_i < $min_spec_ch) {
						$min_spec_ch = $spec_ch_i;
					}
				}
				if ($min_spec_ch == 999999)  $min_spec_ch = -1;
				if ($min_spec_ch >= 0) {
//					echo "min_spec_ch[$min_spec_ch] < min_pos_i[$min_pos_i] + ret_len[$ret_len]<br>";
					if ($min_spec_ch < $min_pos_i + $ret_len) {
						$min_pos_i=0;
						$ret_len = $min_spec_ch+1;
					}
				}
//				echo "cut text = [".substr($dataIn,$min_pos_i,$ret_len)."]<br>";
//				echo "cut text = [".substr($dataIn,$min_pos_i,$ret_len)."]($min_pos_i,$ret_len)<br>";
				return $min_pos_i+$ret_len;
			} // end function

			function thaiCutLinePDF($dataIn, $w, $delim, $f_reject_newline=true){
				$cw=&$this->CurrentFont['cw'];
//				$delim = "@";
				$vowels = array("<br>", "<BR>", "<Br>", "<br />", "<BR />", "<Br />", "<br/>", "<BR/>", "<Br/>", "\n");
				$enter = array("*enter*", "*Enter*", "*ENTER*");
//				for($k=0; $k < strlen($dataIn); $k++) echo "ch $k ={".substr($dataIn,$k,1)."} {".ord(substr($dataIn,$k,1))."}<br>";
//				echo "---->1>$dataIn|<br>";
				if ($f_reject_newline)	// true ��� ���ʹ new line ����ҡѺ text ���
					$tempdataIn = str_replace($vowels, " ", $dataIn); // ����ͧ��ҧ᷹ ���ǹ�仵Ѵ������ ���ʹ new line �ͧ���
				else		// ����¹ new line ����ҡѺ text ��� ����� | ����仵Ѵ�ӡ�͹
					$tempdataIn = str_replace($vowels, "|", $dataIn); // ���Ǥ������ $delim ��᷹����ѧ ���͵Ѵ�ѭ�� \n ���Ѻ�ӹǹ� string
				$tempdataIn = str_replace($enter,"|",$tempdataIn);
//				if (strpos($dataIn, $enter[0])!==false) echo "0-dataIn=$dataIn, tempdataIn=$tempdataIn<br>";
//				else if (strpos($dataIn, $enter[1])!==false) echo "1-dataIn=$dataIn, tempdataIn=$tempdataIn<br>";
//				else if (strpos($dataIn, $enter[2])!==false) echo "2-dataIn=$dataIn, tempdataIn=$tempdataIn<br>";
//				if ($dataIn=="��ҹ/¡��ԡ")
//					echo "---->1>tempdataIn=$tempdataIn|<br>";
//				$tempdataIn = trim($tempdataIn);
				if (substr($tempdataIn,strlen($tempdataIn)-1)=="|") 
					$tempdataIn = substr($tempdataIn,0,strlen($tempdataIn)-1);
//				echo "---->2>$tempdataIn|<br>";
				$arr_tempdataIn = explode("|", $tempdataIn);
				$arr_out = (array) null;
				foreach($arr_tempdataIn as $idx => $tempdataIn) {
					$midch_cnt=0;
					$i = 0;
					$ctext = "";
					$out = "";
					$loop = 0;
					while (strlen($tempdataIn) > 0) {
						$ch = substr($tempdataIn,$i,1);
						if (strpos( "���������������", $ch) === false) { // ������ѡ���ǡ�ҧ (������ѡ��¡   �  �  �  �  �  �  �  �  �  �  �  �  �)
							$rest_w = $w - $this->GetStringWidth("$ctext");
							$mylen = $this->thaiWrapPDF($tempdataIn, $delim, $rest_w);
//							echo "ctext=$ctext, rest_w=$rest_w, tempdataIn=$tempdataIn, mylen=$mylen<br>";
							if ($mylen > 0) {
								$loop = 0;
								$text1 = substr($tempdataIn,0,$mylen);
	//							if ($dataIn=="��ҹ/¡��ԡ")
	//								echo "after cut ($ch)=$tempdataIn--[$text1]<br>";
								$cnt_up=0;
								for($ii=0; $ii < strlen($text1); $ii++) {
									$ch1=substr($text1,$ii,1);
									if (strpos( "���������������", $ch1) > -1) { // ���ѡ��¡   �  �  �  �  �  �  �  �  �  �  �  �  �
										$cnt_up++;
									}
								}
								$mylen1 = $mylen - $cnt_up;
								$str_w = $this->GetStringWidth("$ctext"."$text1");
								if ($str_w > $w - 1) {
									// ����Թ w �� ����Ǣ�� �֧����¤��͹˹��
									if (strlen($ctext) > 0)
										if (trim($text1)=="|")
											$out = "$out".$ctext;
										else
											$out = "$out".$ctext."$delim";
									$ctext=$text1;		// ��鹺�÷Ѵ������� ����¤����Թ��
									$tempdataIn = substr($tempdataIn,$mylen);
									$midch_cnt=$mylen1;
								} else { // <= $w - 1	����Թ��ͧ���ҧ����˹�
									$delim_pos = strpos($text1,"|");
									if ($delim_pos !== false) {	// �� ��ǵѴ line | � text1
	//									echo "found | in $text1<br>";
										$ctext = "$ctext"."$text1";
										$out = "$out".$ctext;				// ��� text1 ��������㹺�÷Ѵ���
										$ctext="";		// ��鹺�÷Ѵ���� Ẻ��ҧ �
										$tempdataIn = substr($tempdataIn,$mylen);
										$midch_cnt=0;
									} else {
										$ctext = "$ctext"."$text1";
										$tempdataIn = substr($tempdataIn,$mylen);
										$midch_cnt = $midch_cnt + $mylen1;
									}
								}
							} else { // else if ($mylen > 0)
								// ��� $mylen == 0 ����� ����ա�õѴ����¤�������ͺ��� 
								if ($rest_w < $w) { // ����ͺ���Ѵ�������������ͤ������ҧ��������鹺�÷Ѵ���������ͧ�Ѵ����
									$out = "$out".$ctext."$delim";
									$ctext = "";
									$midch_cnt=0;
								} else {	// ��� �������ҧ��� ($w) �����ѧ�Ѵ�������Ƿ��������ҧ�ͧ�������ҡ���� ���ͧ�Ѵ��� $w
									$tdata_w = $this->GetStringWidth("$tempdataIn");
									if ($tdata_w > $w) {	// �Ѵ����������ҧ $w ���ҧ����
										$len = 0;
										for($ii=0; $ii < strlen($tempdataIn); $ii++) {
											$str_w = $this->GetStringWidth(substr($tempdataIn,0,$ii));
											if ($str_w > $w) {
												$len= $ii-1;
												break;
											}
										}
										if ($len) {
											$ctext = substr($tempdataIn,0,$len);
											$out = "$out".$ctext."$delim";
											$ctext = "";
											$tempdataIn = substr($tempdataIn,$len);
										} else {
											$ctext = $tempdataIn;
											$tempdataIn = "";
										}
									} else {
										$ctext = $tempdataIn;
										$tempdataIn = "";
									}
								}
							}
							$i=-1;
						} // end if ch
						$i++;
					} // end loop while
	//				if ($dataIn=="��ҹ/¡��ԡ")
	//					echo "****3.$ctext($out)<br>";
	//				echo "****3.$ctext ($out)<br>";
	//				$out = "$out".trim((substr($ctext,strlen($ctext)-1)=="|"?substr($ctext,0,strlen($ctext)-1):$ctext));
					$out = "$out".(substr($ctext,strlen($ctext)-1)=="|"?substr($ctext,0,strlen($ctext)-1):$ctext);
					$arr_out[] = $out;
				} // end foreach arr_tempdataIn
				
				$output = implode("|", $arr_out);
				$output = str_replace("|",$delim,$output);
				return $output;
			} // end function

			function GetFont()
			{
				//Select a font; size given in points
				global $fpdf_charwidths;

				$arr_fonts = (array) null;
				foreach($this->fonts as $key => $value)
						$arr_fonts[] = $key;
				foreach($this->CoreFonts as $key => $value) {
						if(strpos($key,'helvetica')!==false)	 $key=str_replace('helvetica','arial',$key);
						$arr_fonts[] = $key;
				}
				foreach($fpdf_charwidths as $key => $value)
						$arr_fonts[] = $key;
			
				sort($arr_fonts);
				$arr_fonts1 = (array) null;
				$afont = "";
				for($i=0; $i < count($arr_fonts); $i++) {
					if (strpos($arr_fonts[$i],"angsa")!==false || strpos($arr_fonts[$i],"cordia")!==false) {
						$arr_fonts1[] = $arr_fonts[$i];
					} else {
						if (!$afont)
							$afont = $arr_fonts[$i];
						else
 							if ($afont != substr($arr_fonts[$i],0,strlen($afont))) {
 								$arr_fonts1[] = $afont;
								$afont = $arr_fonts[$i];
							}
					}
					if ($i == count($arr_fonts)-1)
							$arr_fonts1[] = $afont;
				}	// end for loop
			
				$font_list = implode(",",$arr_fonts1);
			
				return $font_list;
			}	// end function GetFont()
			
			function open_tab($head_text, $head_width, $head_line_height=7, $head_border="TRHBL", $head_align="C", $font, $font_size="14", $font_style="b", $font_color=0, $fill_color=0, $COLUMN_FORMAT, $func_aggreg, $f_nofirsthead=false, $head_text_rotate)
			{
				if (!$font) $font=$this->FontFamily;
//				if (!$font_style) $font_style=$this->FontStyle;	// �������դ���Ҩ���¶֧ ��� bold ��� italic ��� underline ��������¶֧ ����դ�� �ѧ�������ͧ move ��� default
				if (!$font_size) $font_size=$this->FontSizePt;
				$this->f_table = 1;
				$this->arr_func_aggreg = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
				$this->arr_tab_head = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
				$this->arr_head_width = explode(",", $head_width);	// array tab head width
//				echo "$this->arr_head_width::".implode(",",$this->arr_head_width)."<br>";
				$this->head_fill_color = explode(",", $fill_color);			//  table head fill color format RRGGBB
				$this->head_font_name = explode(",", $font);					//  table head font name ex  "AngsanaUPC" "cordia"
				$this->head_font_size = explode(",", $font_size);			//  table head font size
				$this->head_font_style = explode(",", $font_style);			//  table head font style "B" bold "I" italic "U" underline
				$this->head_font_color = explode(",", $font_color);		//  table head font color  format RRGGBB
				$this->head_border = explode(",", $head_border);		//  table head border
				$this->arr_head_align = explode(",", $head_align);		//  table head align
				$this->head_line_height = $head_line_height;		//  table head line height

				$this->tab_head_title = "";		//  table head title

				$this->arr_column_map = (array) null;
				$this->arr_column_sel = (array) null;
				$this->arr_column_width = (array) null;
				$this->arr_column_align = (array) null;
				if (!$COLUMN_FORMAT) {	// ����觤�ҡ�û�Ѻ�� ��§ҹ�����
					for($i=0; $i < count($this->arr_head_width); $i++) {
						$this->arr_column_map[] = $i;		// link index �ͧ head 
						$this->arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   
					}
					$this->arr_column_width = explode(",",$head_width);	// �������ҧ
					$this->arr_column_align = explode(",",$head_align);		// align ��˹�����͹ ���ԧ � ��������� align �ͧ head �� �й����� add_data_tab �� align �ͧ data
					$COLUMN_FORMAT = implode(",",$this->arr_column_map)."|".implode(",",$this->arr_column_sel)."|".implode(",",$this->arr_column_width)."|".implode(",",$this->arr_column_align);
				} else {
					$arrbuff = explode("|",$COLUMN_FORMAT);
					$this->arr_column_map = explode(",",$arrbuff[0]);		// index �ͧ head �������
					$this->arr_column_sel = explode(",",$arrbuff[1]);			// 1=�ʴ�	0=����ʴ�
					$this->arr_column_align = explode(",",$arrbuff[3]);		// align
					if ($head_width != $arrbuff[2]) {
						$this->arr_column_width = explode(",",$head_width);		// �������ҧ
						$COLUMN_FORMAT = implode(",",$this->arr_column_map)."|".implode(",",$this->arr_column_sel)."|".implode(",",$this->arr_column_width)."|".implode(",",$this->arr_column_align);
					} else {
						$this->arr_column_width = explode(",",$arrbuff[2]);		// �������ҧ
					}
				}
				// ����� TNUM ������������ $func_aggreg head ����� TNUM ��������ѵ��ѵ�
				if (strpos($func_aggreg, "TNUM") !== false) $this->f_head_tnum = true;
				else $this->f_head_tnum = false;
//				echo "map=".implode(",",$this->arr_column_map)."  sel=".implode(",",$this->arr_column_sel)."  width=".implode(",",$this->arr_column_width)."  align=".implode(",",$this->arr_column_align)." [$COLUMN_FORMAT]<br>";

//				echo "head font=".implode(",",$this->head_font_name).", size=".implode(",",$this->head_font_size)."<br>";

//				$this->SetFont($this->head_font_name,$this->head_font_style,$this->head_font_size);
				
				$this->merge_rows_h = (array) null;
				for($i = 0; $i < $cnt_column_width; $i++) {
					$this->merge_rows_h[]=0;
				}

				if (strlen(trim($head_text))==0) {
					$cnt_column_head = 0;
//					echo "1..head text = 0<br>";
				} else {
					$buff_arr_tab_head = explode(",", $head_text);
					$cnt_column_head = count($buff_arr_tab_head);
//					echo "2..head text = $cnt_column_head<br>";
				}	// end if (strlen(trim($head_text))==0)
				$cnt_column_width = count($this->arr_head_width);
//				echo "cnt_column_head=$cnt_column_head<br>";
				if ($cnt_column_head==0) {
					$this->f_no_head = true; 
					$this->arr_tab_head = (array) null;
					for($i = 0; $i < $cnt_column_width; $i++) {
						$this->arr_tab_head[]="";
					}
					$cnt_column_head = $cnt_column_width;
//					echo "3..head width = $cnt_column_head<br>";
				} else {	// else if ($cnt_column_head==0)
//					echo "4..head text = $cnt_column_head<br>";
					// �Ѵ��áѺ function aggregate
					if ($func_aggreg) {
//						echo "func_aggreg=$func_aggreg<br>";
						$this->arr_func_aggreg = explode(",", $func_aggreg);
					} // end if ($func_aggreg)
					// ����èѴ��áѺ function aggregate

					$founded = false;
					$max_line = 0;
					$line_idx = 0;
					$new_sub_head = (array) null;
					$looped = true;
					while ($looped) { // loop �����÷Ѵ��͹
						$numline = 0; // ��㹡���Ҥ�Һ�÷Ѵ������
						$grptext = "";
						$realtext = "";
						$sumw = 0; $start_i = -1;
						for($i = 0; $i < $cnt_column_head; $i++) {
							if ($this->arr_column_sel[$i]==1) {	// ੾�з�����͡
								if (strlen(trim($buff_arr_tab_head[$i])) > 0) $founded = true;
								$sub_tab_head = explode("|", trim($buff_arr_tab_head[$i]));
//								echo "sub_tab_head[$line_idx]=".$sub_tab_head[$line_idx]."<br>";
								$arr_font_name = explode("|", ($this->head_font_name[$i] ? $this->head_font_name[$i] : $this->head_font_name[0]) );
								$arr_font_style = explode("|", ($this->head_font_style[$i] ? $this->head_font_style[$i] : $this->head_font_style[0]) );	// 	�ó� style ��ҨС�˹��������դ�� ��� none
								$arr_font_size = explode("|",  ($this->head_font_size[$i] ? $this->head_font_size[$i] : $this->head_font_size[0]) );
								$arr_font_color = explode("|",  ($this->head_font_color[$i] ? $this->head_font_color[$i] : $this->head_font_color[0]) );	// default �յ���ѡ�� �մ�
								$arr_head_border = explode("|",  ($this->head_border[$i] ? $this->head_border[$i] : (count($this->head_border) > 1 ? "" : $this->head_border[0])) );
								$arr_head_fill_color = explode("|",  ($this->head_fill_color[$i] ? $this->head_fill_color[$i] : $this->head_fill_color[0]) );	// default �վ�� �բ��
								$t_head_font_name = ($arr_font_name[$line_idx] ? $arr_font_name[$line_idx] : $arr_font_name[0]);
								$buff = ($arr_font_style[$line_idx] ? $arr_font_style[$line_idx] : $arr_font_style[0]);
								$t_head_font_style = "";
								if (strpos(strtolower($buff),"b")!==false) {
									$t_head_font_style .= "b";
									$buff = str_replace("b","",$buff);
									$buff = str_replace("B","",$buff);
								}
								if (strpos(strtolower($buff),"U")!==false) {
									$t_head_font_style .= "u";
									$buff = str_replace("u","",$buff);
									$buff = str_replace("U","",$buff);
								}
								if (strpos(strtolower($buff),"I")!==false) {
									$t_head_font_style .= "i";
									$buff = str_replace("i","",$buff);
									$buff = str_replace("I","",$buff);
								}
								if (strpos(strtolower($buff),"r")!==false) {
									$buff = str_replace("r","",$buff);
									$buff = str_replace("R","",$buff);
									$t_head_rotate_angle = ($buff ? $buff : "90");
								} else {
									$t_head_rotate_angle = "";
								}
								if ($t_head_rotate_angle)	$this->Rotate((int)$t_head_rotate_angle);  else $this->Rotate(0);
								$t_head_font_size = ($arr_font_size[$line_idx] ? $arr_font_size[$line_idx] : $arr_font_size[0]);
								$t_head_font_color = ($arr_font_color[$line_idx] ? $arr_font_color[$line_idx] : $arr_font_color[0]);
								$t_head_border = ($arr_head_border[$line_idx] ? $arr_head_border[$line_idx] : (count($arr_head_border) > 1 ? "" : $arr_head_border[0]));
								$t_head_fill_color = ($arr_head_fill_color[$line_idx] ? $arr_head_fill_color[$line_idx] : $arr_head_fill_color[0]);
//								echo "head font=".$this->head_font_name.", font=$font<br>";
								$this->SetFont($t_head_font_name,$t_head_font_style,$t_head_font_size);
								$c1 = strpos($sub_tab_head[$line_idx], "<**");
								if ($c1 !== false) {
									$c2 = strpos($sub_tab_head[$line_idx], "**>");
//									echo "grptext=".$grptext.", sub_tab_head[$line_idx]=".$sub_tab_head[$line_idx]." substr=".substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3)."<br>";
									if ($grptext != substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3)) {
										if ($sumw > 0) {
//											echo "1..thai cut w:$sumw, text=$realtext<br>";
//											$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
											if ($this->f_head_tnum) $realtext = convert2thaidigit($realtext); // ����ա�÷� TNUM  ����¹�������� ����Ţ�¡�͹�Ѵ��
											$arr_b = $this->MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false);
//											echo "1..head-($i)>:".implode("|",$arr_b).", grp=$grptext, sumw=$sumw<br>";
											for($k = 0; $k < count($arr_b); $k++) {
//												echo "1.2..arr_b[$k]=".$arr_b[$k]."<br>";
												if ($k > 0) $grptext1 = str_replace("**>","^**>",$grptext); else $grptext1 = $grptext;
												$arr_b[$k] = $grptext1.$arr_b[$k];
//												echo "1.2..arr_b[$k]=".$arr_b[$k]."<br>";
//												$numline++;
											}
											$newtext = implode("|", $arr_b);
											for($k = $start_i; $k < $i; $k++) {
												$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
												$sub_tab_head1[$line_idx] = $newtext;
												$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
												$numline = count(explode("|", $buff_arr_tab_head[$k]));
												if ($numline > $max_line) $max_line = $numline;
											}
//											echo "1..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
										}
										$start_i = $i; 
										$grptext = substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3);
										$realtext = substr($sub_tab_head[$line_idx], $c2+3);
//										echo "after newtext --> grptext=".$grptext.", realtext=".$realtext." ($c2 - $c1)<br>";
										$sumw = $this->arr_head_width[$i];
									} else {
										$sumw += $this->arr_head_width[$i];
//										echo "($i) in grp $grptext sumw=$sumw<br>";
									}
								} else {	// else if ($c1 !== false) --> �������ա���� head
									if ($sumw > 0) {
//										echo "2..thai cut w:$sumw, text=$realtext<br>";
//										$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
										if ($this->f_head_tnum) $realtext = convert2thaidigit($realtext); // ����ա�÷� TNUM  ����¹�������� ����Ţ�¡�͹�Ѵ��
										$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false);
//										echo "2..head-($i)>:".implode("|",$arr_b).", grp=$grptext, sumw=$sumw<br>";
										for($k = 0; $k < count($arr_b); $k++) {
//											echo "2.1..arr_b[$k]=".$arr_b[$k]."<br>";
											if ($k > 0) $grptext1 = str_replace("**>","^**>",$grptext); else $grptext1 = $grptext;
											$arr_b[$k] = $grptext1.$arr_b[$k];
//											echo "2.2..arr_b[$k]=".$arr_b[$k]."<br>";
//											$numline++;
										}
										$newtext = implode("|", $arr_b);
										for($k = $start_i; $k < $i; $k++) {
											$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
											$sub_tab_head1[$line_idx] = $newtext;
											$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
											$numline = count(explode("|", $buff_arr_tab_head[$k]));
											if ($numline > $max_line) $max_line = $numline;
										}
//										echo "2..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
										$start_i = -1; 
										$grptext = "";
										$realtext = "";
//										echo "realtext=$realtext ($c2 - $c1)<br>";
										$newtext="";
										$sumw = 0;
									}
									if (trim($sub_tab_head[$line_idx])) {
//										echo "3..thai cut w:".$this->arr_head_width[$i].", text=".trim($sub_tab_head[$line_idx])."<br>";
//										$arr_b = $this->line_MultiCellThaiCut($this->arr_head_width[$i], $this->head_line_height, trim($sub_tab_head[$line_idx]), $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
										if ($this->f_head_tnum) $realtext = convert2thaidigit(trim($sub_tab_head[$line_idx])); else $realtext = trim($sub_tab_head[$line_idx]); 
										// ����ա�÷� TNUM  ����¹�������� ����Ţ�¡�͹�Ѵ��
										$arr_b = $this->MultiCellThaiCut($this->arr_head_width[$i], $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false);
//										echo "3..head-($i)>:".implode("|",$arr_b)." head_w=".$this->arr_head_width[$i]."<br>";
										for($k = 0; $k < count($arr_b); $k++) {
//											echo "arr_b[$k]=".$arr_b[$k]."<br>";
											$arr_b[$k] = $arr_b[$k];
//											echo "3.2..arr_b[$k]=".$arr_b[$k]."<br>";
//											$numline++;
										}
										$newtext = implode("|", $arr_b);
										$sub_tab_head[$line_idx] = $newtext;
										$buff_arr_tab_head[$i] = implode("|", $sub_tab_head);
										$numline = count(explode("|", $buff_arr_tab_head[$i]));
										if ($numline > $max_line) $max_line = $numline;
//										echo "3..line_idx=$line_idx, newtext=$newtext, buff_arr_tab_head[$i]=".$buff_arr_tab_head[$i].", numline=$numline, max_line=$max_line<br>";
									}
								}
							} // end if ($this->arr_column_sel[$i]==1)
						} // end for $i loop
						if ($sumw > 0) {
//.							echo "4..thai cut w:$sumw, text=$realtext<br>";
//							$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
							if ($this->f_head_tnum) $realtext = convert2thaidigit($realtext); // ����ա�÷� TNUM  ����¹�������� ����Ţ�¡�͹�Ѵ��
							$arr_b = $this->MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false); 
							// $t_head_border �Ѻ $t_head_fill_color ��ͧ��˹�������� ���ж�� $sumw > 0 �ѹ��ͧ��ҹ loop for ��ҧ�����ҧ���� 1 ����
//							echo "4..head-($i)>:".implode("|",$arr_b).", grp=$grptext, sumw=$sumw<br>";
							for($k = 0; $k < count($arr_b); $k++) {
//								echo "4.1..arr_b[$k]=".$arr_b[$k]."<br>";
								if ($k > 0) $grptext1 = str_replace("**>","^**>",$grptext); else $grptext1 = $grptext;
								$arr_b[$k] = $grptext1.$arr_b[$k];
//								echo "4.2..arr_b[$k]=".$arr_b[$k]."<br>";
//								$numline++;
							}
							$newtext = implode("|", $arr_b);
							for($k = $start_i; $k < $i; $k++) {
								$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
								$sub_tab_head1[$line_idx] = $newtext;
								$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
								$numline = count(explode("|", $buff_arr_tab_head[$k]));
								if ($numline > $max_line) $max_line = $numline;
							}
//							echo "4..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
							$start_i = -1; 
							$grptext = "";
							$realtext = "";
//							echo "realtext=$realtext ($c2 - $c1)<br>";
							$newtext="";
							$sumw = 0;
						}
						$line_idx++;
						if (($line_idx >= $max_line && $i >= $cnt_column_head)) $looped = false;
					} // end while loop
//					echo "arr_tab_head==>".implode(",", $buff_arr_tab_head).", founded=$founded<br>";
					if ($founded)	{
						for($i = 0; $i < $cnt_column_head; $i++) {
//							echo "new_sub_head[$i]=".$new_sub_head[$i]." head_text[$i]=".$buff_arr_tab_head[$i]." max_line=$max_line<br>";
							$arr_sub = explode("|", $buff_arr_tab_head[$i]);
//							echo "cnt_sub[$i]=".count($arr_sub).", max_line=$max_line<br>";
							if (count($arr_sub) < $max_line) {
								for($j = count($arr_sub); $j < $max_line; $j++) {	
									array_push($arr_sub, "");
//									$new_sub_head[$i] .= "|";
								}
								$this->arr_tab_head[] = implode("|", $arr_sub);
//								echo "1..arr_tab_head[$i]=".implode("|", $arr_sub)."<br>";
							} else {
								$this->arr_tab_head[] = $buff_arr_tab_head[$i];
//								echo "2..arr_tab_head[$i]=".$buff_arr_tab_head[$i]."<br>";
							}
						}
						$n_line = $max_line;
						for($ln = 0; $ln < $n_line; $ln++) {
							$have_val=false;
							for($i = 0; $i < $cnt_column_head; $i++) {
								$arr_sub = explode("|", $this->arr_tab_head[$i]);
								if (trim($arr_sub[$ln])) $have_val = true;
							}
//							echo "line=$ln-->have_val=$have_val<br>";
							// ��� ��ͧ��ҧ����Ѻ��÷Ѵ����� column element �����ҡѹ ������͡ѹ
							if (!$have_val) {
								for($i = 0; $i < $cnt_column_head; $i++) {
									$arr_sub = explode("|", $this->arr_tab_head[$i]);
//									echo "bf remove space - count=".count($arr_sub)."<br>";
									array_splice($arr_sub , $ln, 1);
//									echo "af remove space - count=".count($arr_sub)."<br>";
									$this->arr_tab_head[$i] = implode("|", $arr_sub);
								}
								$n_line = count(explode("|", $this->arr_tab_head[0]));
							}
						} // end for $ln
					} else {
						$this->arr_tab_head = explode(",", $head_text);
					} // end if $founded
					// ��� �� head ����դ�� ����� column ���� ��ж����� �о���� head
					if ($founded)	
						$this->f_no_head = false;
					else
						$this->f_no_head = true;
				}	// end if ($cnt_column_head==0)
//				echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
				if ($cnt_column_head != $cnt_column_width) {	// ��� �觨ӹǹ column ��������١��ͧ return error
					return false; 
				} else {
//					echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
					// $f_nofirsthead �Ǻ�����þ������Ǥ����á ��������ѡ � �óշ���ա�þ�������
					return ($f_nofirsthead ? true : $this->print_tab_header());
				}
			}
			
			function print_tab_header()
			{
				$res = true;
				// ��� open table ���� ��� ��� no head �� print head
//				echo "tab_header->f_table=".$this->f_table.", f_no_head=".$this->f_no_head."<br>";
				if ($this->f_table==1 && (!$this->f_no_head)) {
				
					$start_x = $this->x;			$start_y = $this->y;				$max_y = $this->y;
					$first_y = $this->y;

					$cnt_line_head = count(explode("|", $this->arr_tab_head[0]));
					// check ˹������͹������÷Ѵ����
					$cnt_column_head = count($this->arr_tab_head);

//					echo "cnt_line_head=$cnt_line_head, cnt_column_head=$cnt_column_head  (f_no_head=".$this->f_no_head.")<br>";
					if ($cnt_column_head > 0) {	// ��� �դ�� column ����Ҷ١��ͧ �ӧҹ���
						$cnt_column_show = 0;
						$head_t_w = 0;
						for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
							$arr_setborder[$jjj] = ""; 	// use for adjust L R Border
							if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { 
								$cnt_column_show++; $head_t_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; 
							}
						}

/* 				$this->tab_head_title = 
						text1^font^size^style^color^fill^align^border||text2^font^size^style^color^fill^align^border||text3^font^size^style^color^fill^align^border */
//						echo "bf...start_y=$start_y , first_y=$first_y , this->y=$this->y , max_y=$max_y<br>";
						if ($this->tab_head_title) {	// �óշ���դ�Ңͧ table head title ��� space ���������繺�÷Ѵ��ҧ
							$a_text = explode("||",$this->tab_head_title);
//							echo "this->tab_head_title=$this->tab_head_title<br>";
							for($ii = 0; $ii < count($a_text); $ii++) {
								$a_sub_text = explode("^",$a_text[$ii]);
								$font = ($a_sub_text[1] ? $a_sub_text[1] : "");
								$border = ($a_sub_text[7] ? $a_sub_text[7] : "");
								$align = ($a_sub_text[6] ? $a_sub_text[6] : "C");
								$size = ($a_sub_text[2] ? $a_sub_text[2] : "14");
								$style = ($a_sub_text[3] ? $a_sub_text[3] : "");
								$color = ($a_sub_text[4] ? $a_sub_text[4] : 0);
								$fill_co = ($a_sub_text[5] ? $a_sub_text[5] : 0);
//								echo "a_sub_text [0]=".$a_sub_text[0]." , font=".$font." , border=$border , align=$align<br>";
//			add_text_line($text, $line_height=7, $border="TRHBL", $align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
								$this->add_text_line($a_sub_text[0], 7, $border, $align, $font, $size, $style, $color, $fill_co, 10);
							}
							$start_y = $this->y;
						}

						$columngrp="";
						for($cntline = 0; $cntline < $cnt_line_head; $cntline++) {
							$text_merge = "";
							$head_align = "";
							$sum_merge = 0;
							$cnt_sel_i = 0;	// �Ѻ੾����¡�÷�����͡
							for($iii = 0; $iii < $cnt_column_head; $iii++) {
								if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = �ʴ� column ���
									$arr_font_name = explode("|", ($this->head_font_name[$this->arr_column_map[$iii]] ? $this->head_font_name[$this->arr_column_map[$iii]] : $this->head_font_name[0]) );
									$arr_font_style = explode("|", ($this->head_font_style[$this->arr_column_map[$iii]] ? $this->head_font_style[$this->arr_column_map[$iii]] : $this->head_font_style[0]) ); 
									// 	�ó� style ��ҡ�˹��������դ�� ����� none
									$arr_font_size = explode("|",  ($this->head_font_size[$this->arr_column_map[$iii]] ? $this->head_font_size[$this->arr_column_map[$iii]] : $this->head_font_size[0]) );
									$arr_font_color = explode("|",  ($this->head_font_color[$this->arr_column_map[$iii]] ? $this->head_font_color[$this->arr_column_map[$iii]] : $this->head_font_color[0]) );	// default �յ���ѡ�� �մ�
									$arr_head_border = explode("|",  ($this->head_border[$this->arr_column_map[$iii]] ? $this->head_border[$this->arr_column_map[$iii]] : (count($this->head_border) > 1 ? "" : $this->head_border[0])) );
									$arr_head_fill_color = explode("|",  ($this->head_fill_color[$this->arr_column_map[$iii]] ? $this->head_fill_color[$this->arr_column_map[$iii]] : $this->head_fill_color[0]) );	// default �վ�� �բ��
									$t_head_font_name = ($arr_font_name[$cntline] ? $arr_font_name[$cntline] : $arr_font_name[0]);
//									$t_head_font_style = ($arr_font_style[$cntline] ? $arr_font_style[$cntline] : $arr_font_style[0]);	// 	�ó� style �������դ�� ���¶֧����繤�һ���
									$buff = ($arr_font_style[$cntline] ? $arr_font_style[$cntline] : $arr_font_style[0]);
									$t_head_font_style = "";
									if (strpos(strtolower($buff),"b")!==false) {
										$t_head_font_style .= "b";
										$buff = str_replace("b","",$buff);
										$buff = str_replace("B","",$buff);
									}
									if (strpos(strtolower($buff),"U")!==false) {
										$t_head_font_style .= "u";
										$buff = str_replace("u","",$buff);
										$buff = str_replace("U","",$buff);
									}
									if (strpos(strtolower($buff),"I")!==false) {
										$t_head_font_style .= "i";
										$buff = str_replace("i","",$buff);
										$buff = str_replace("I","",$buff);
									}
									if (strpos(strtolower($buff),"r")!==false) {
										$buff = str_replace("r","",$buff);
										$buff = str_replace("R","",$buff);
										$t_head_rotate_angle = ($buff ? $buff : "90");
									} else {
										$t_head_rotate_angle = "";
									}
									if ($t_head_rotate_angle)	$this->Rotate((int)$t_head_rotate_angle);  else $this->Rotate(0);
									$t_head_font_size = ($arr_font_size[$cntline] ? $arr_font_size[$cntline] : $arr_font_size[0]);
									$t_head_font_color = ($arr_font_color[$cntline] ? $arr_font_color[$cntline] : $arr_font_color[0]);		// default �յ���ѡ�� �մ�
									$t_head_border = ($arr_head_border[$cntline] ? $arr_head_border[$cntline] : $arr_head_border[0]);
									$t_head_fill_color = ($arr_head_fill_color[$cntline] ? $arr_head_fill_color[$cntline] : $arr_head_fill_color[0]);	// default �վ�� �բ��

									$border = $t_head_border;
									$head_align = $this->arr_head_align[$this->arr_column_map[$iii]];

									$this->SetFont($t_head_font_name,$t_head_font_style,$t_head_font_size);
									if (strlen($t_head_font_color)==6) {
										$r=substr($t_head_font_color,0,2);
										$g=substr($t_head_font_color,2,2);
										$b=substr($t_head_font_color,4,2);
									} else { $r="00"; $g="00"; $b="00"; }
//									echo "font:$r $g $b<br>";
									$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
				
									if (strlen($t_head_fill_color)==6) {
										$r=substr($t_head_fill_color,0,2);
										$g=substr($t_head_fill_color,2,2);
										$b=substr($t_head_fill_color,4,2);
										$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//										echo "fill:$r $g $b<br>";
									}
//									echo "af...start_y=$start_y , first_y=$first_y , this->y=$this->y , max_y=$max_y<br>";

									$nline = explode("|", $this->arr_tab_head[$this->arr_column_map[$iii]]);	// $this->arr_tab_head[$iii]);
//									echo "headtext->".$this->arr_tab_head[$this->arr_column_map[$iii]]."<br>";
									$sum_w = 0;
									for($jjj = 0; $jjj <= $iii; $jjj++)
										if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) $sum_w+=(float)$this->arr_column_width[$this->arr_column_map[$jjj]];		// (float)$this->arr_head_width[$jjj];
//									echo "line:$cntline column:$iii text:".$nline[$cntline]."<br>";
									$chk_merge = strpos($nline[$cntline],"<**");
									if ($chk_merge!==false) {	// ����� column merge
										$c = strpos($nline[$cntline],"**>");
										if ($c!==false) {
											$buff_colgrp = substr($nline[$cntline], $chk_merge+3, $c-($chk_merge+3));
											if (strpos($buff_colgrp,"^")!==false) { 
												$f_mergeup = 1;	// 
												$buff_colgrp = substr($buff_colgrp,0,strlen($buff_colgrp)-1);
											} else $f_mergeup = 0;
											if (!$text_merge)  $text_merge = substr($nline[$cntline], $c+3);
//											echo "columngrp($columngrp)==buff_colgrp($buff_colgrp) || text_merge ($text_merge) ,  f_mergeup=$f_mergeup,  tmp_f_mergeup=$tmp_f_mergeup<br>";
//											$chk_up_merge = false;
//											if ($cntline > 0)	 $chk_up_merge = strpos($nline[$cntline-1],"<**");
											if ($columngrp==$buff_colgrp) {
												// $f_mergeup merge �Ѻ cell ��
//												echo "tmp_f_mergeup=$tmp_f_mergeup , border=$border<br>";
												$tmp_border = ($tmp_f_mergeup==1 ? "" : (strpos($border,"T")===false ? "" : "T")); 	// ��� merge �Ѻ cell �� ����ͧ draw top border 
																																																// ������ �� ������˹���� draw top ��� draw top ���ҡ�˹� ��� draw top	
												if ($cntline == $cnt_line_head-1) $tmp_border .= (strpos($border,"B")===false ? "" : "B");	// ����繺�÷Ѵ�ش���� ��� ��ҡ�˹� "B" ����� draw 'B'
//												echo "before add new sum = $sum_merge<br>";
												$sum_merge+=(float)$this->arr_column_width[$this->arr_column_map[$iii]];	// (float)$this->arr_head_width[$iii];
//												echo "check last record grp($columngrp) $iii == $cnt_column_head-1 == $cnt_column_show-1<br>";
												if ($cnt_sel_i == $cnt_column_show-1) { // ����ش���·���ʴ��� merge group column
//													echo "3. sum_merge=$sum_merge, text_merge=$text_merge<br>";
													if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);
//													echo "1.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w."<br>";
													$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $tmp_border, $head_align, 1);	//
													if($this->y > $max_y) $max_y = $this->y;
													$this->x = $start_x + $sum_w - (float)$this->arr_column_width[$this->arr_column_map[$iii]];	// (float)$this->arr_head_width[$iii];
													$this->y = $start_y;
													$sum_merge = 0;
													$arr_setborder[$iii] = (strpos($border,"R")===false ? "" : "R");	// �Դ��ѧ����ش���� ���� border ��˹������ R ���� ���������˹�������ͧ��
													$columngrp="";
													$tmp_f_mergeup=0;
													$text_merge = "";
//												} else {
//													echo "5.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w.", columngrp=$columngrp<br>";
												}
											} else {	// else $columngrp!=$buff_colgrp
												if ($sum_merge > 0) {
													// $f_mergeup merge �Ѻ cell ��
													$tmp_border = ($tmp_f_mergeup==1 ? "" : (strpos($border,"T")===false ? "" : "T")); 	// ��� merge �Ѻ cell �� ����ͧ draw top border 
																																																	// ������ �� ������˹���� draw top ��� draw top ���ҡ�˹� ��� draw top	
													if ($cntline == $cnt_line_head-1) $tmp_border .= (strpos($border,"B")===false ? "" : "B");	// ����繺�÷Ѵ�ش���� ��� ��ҡ�˹� "B" ����� draw 'B'
//													echo "1. sum_merge=$sum_merge, text_merge=$text_merge<br>";
													if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);
//													echo "2.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w."<br>";
													$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $tmp_border, $head_align, 1);
													if($this->y > $max_y) $max_y = $this->y;
													$this->x = $start_x + $sum_w - (float)$this->arr_column_width[$this->arr_column_map[$iii]];	// (float)$this->arr_head_width[$iii];
													$this->y = $start_y;
													$sum_merge = 0;
													$arr_setborder[$last_iii] = (strpos($border,"R")===false ? "" : "R");	// �Դ�����
													$text_merge = "";
												}
												$head_align = ($this->arr_head_align[$this->arr_column_map[$iii]] ? $this->arr_head_align[$this->arr_column_map[$iii]] : "C");
//												echo ">>>$iii>>head_align=$head_align, arr_head_align [".$this->arr_column_map[$iii]."]=".$this->arr_head_align[$this->arr_column_map[$iii]]."<br>";
												$sum_merge = (float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
//												echo "1.. text ($cntline)-->".$nline[$cntline]."<br>";
												$text_merge = substr($nline[$cntline], $c+3);
												$columngrp=$buff_colgrp;
												$tmp_f_mergeup = $f_mergeup;
												$arr_setborder[$iii] = (strpos($border,"L")===false ? "" : "L");	// �Դ�����
											}
										} else {
											$ret = false;
										} // end if ($c!==false)
									} else {
//										echo "sum_merge=$sum_merge<br>";
										if ($sum_merge > 0) {
//											echo "2. sum_merge=$sum_merge, text_merge=$text_merge<br>";
											if ($cntline > 0)	 {
												$chk_up_merge = strpos($nline[$cntline-1],"<**");
												$tmp_border = ($chk_up_merge === false ? "" : (strpos($border,"T")===false ? "" : "T"));	// 
											} else $tmp_border =  (strpos($border,"T")===false ? "" : "T");
											if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);
//											echo "3.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w."<br>";
											$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $tmp_border, $head_align, 1);
											if($this->y > $max_y) $max_y = $this->y;
											$this->x = $start_x + $sum_w - (float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
											$this->y = $start_y;
											$sum_merge = 0;
											$arr_setborder[$last_iii] = (strpos($border,"R")===false ? "" : "R");	// �Դ�����
											$tmp_f_mergeup=0;
											$text_merge = "";
										}
										$arr_setborder[$iii] = (strpos($border,"L")===false ? "" : "L").(strpos($border,"R")===false ? "" : "R");		// column �������繡���� ��ҡ�˹������ L ������� R �������
										if ($cntline > 0)	 {
											$chk_up_merge = strpos($nline[$cntline-1],"<**");
											$tmp_border = ($chk_up_merge === false ? "" : (strpos($border,"T")===false ? "" : "T")); 	// ��� ��Ǣ�ҧ���繡���� �����ҧ����繡���� ��մ�ͺ�� T
										} else $tmp_border =  (strpos($border,"T")===false ? "" : "T");
										if ($cntline == $cnt_line_head-1) $tmp_border .= (strpos($border,"B")===false ? "" : "B");
//										echo "4.. $iii - w:".$this->arr_head_width[$this->arr_column_map[$iii]]." text (line:$cntline):".$nline[$cntline].", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$this->arr_head_align[$this->arr_column_map[$iii]].",  sum_w:".$sum_w."<br>";
//										$this->MultiCellThaiCut($this->arr_head_width[$iii], (float)$this->head_line_height, $nline[$cntline], $tmp_border, $this->arr_head_align[$iii],1);
										if ($this->f_head_tnum) $text_merge = convert2thaidigit($nline[$cntline]); else $text_merge = $nline[$cntline];
//										echo "2.. text ($cntline)-->".$nline[$cntline].", text_merge=$text_merge<br>";
										$this->MultiCellThaiCut($this->arr_column_width[$this->arr_column_map[$iii]], (float)$this->head_line_height, $text_merge, $tmp_border, $this->arr_head_align[$this->arr_column_map[$iii]],1);
										if($this->y > $max_y) $max_y = $this->y;
										$this->x = $start_x + $sum_w;
										$this->y = $start_y;
										$columngrp="";
									} // end if ($chk_merge!==false)
									$last_iii = $iii;
									$cnt_sel_i++;
								}	// end if ($this->arr_column_sel[$iii]==1)
							} //// end for $iii loop
							// DRAW BORDER
							$line_start_y = $start_y;			$line_end_y = $max_y;
							$line_start_x = $start_x;			$line_end_x = $start_x;
//							echo "ss....<br>";
							for($iii = 0; $iii < $cnt_column_head; $iii++) {
								if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = �ʴ� column ���
									//================= Draw Horicental (L)Border Line ====================
									$t_head_border = ($this->head_border[$this->arr_column_map[$iii]] ? $this->head_border[$this->arr_column_map[$iii]] : $this->head_border[0]);
//									echo "t_head_border=$t_head_border<br>";
									$have_l = strpos(strtoupper($t_head_border), "L");
									$have_r = strpos(strtoupper($t_head_border), "R");
									$have_h = strpos(strtoupper($t_head_border), "H");
									$line_start_y = $start_y;  $line_end_y = $max_y;
									$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //�մ�
//									if ($have_l !== false) {
									if (strpos(strtoupper($arr_setborder[$iii]),"L")!==false) {
										$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ����á
//										echo "��鹢�ҧ����á (L)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
									}

									//================= Draw verticle Border Line ====================
									$line_end_x += (float)$this->arr_column_width[$this->arr_column_map[$iii]];
									$have_t = strpos(strtoupper($t_head_border), "T");
									if ($have_t !== false) {						
										$line_start_y = $start_y;	$line_end_y = $start_y;	
//										$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹹͹��
//										echo "��鹹͹�� (T)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
									}
									$have_b = strpos(strtoupper($t_head_border), "B");						
									if ($have_b !== false) {
										$line_start_y = $max_y;	$line_end_y = $max_y;
//										$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹹͹��ҧ
//										echo "��鹹͹��ҧ (B)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
									}

									//================= Draw Horicental (R)Border Line ====================
									$line_start_y = $start_y;
									$line_start_x += (float)$this->arr_column_width[$this->arr_column_map[$iii]];
									if ($iii == $cnt_column_show-1 && $have_r !== false) {
										if (strpos(strtoupper($arr_setborder[$iii]),"R")!==false) { // ੾�� �ѹ�������� cell merge
											$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ��������
//											echo "��鹢�ҧ�����ѧ 1(R)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
										}
									} elseif ($have_h !== false) {
										if (strpos(strtoupper($arr_setborder[$iii]),"R")!==false) { // ੾�� �ѹ�������� cell mergetmp_f_mergeup
											$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ��������
//											echo "��鹢�ҧ�����ѧ 2(R)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
										}
									}
								} // end if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1)
							} // end for loop $iii ���駷�� 2
							//====================================================
							$this->x = $start_x;			$this->y = $max_y;
							$start_y = $max_y;
						}	// end for $cntline loop
					} else {
						$ret = false;
					} // end if ($cnt_column_head > 0)
						
					$this->SetFont($this->tabdata_font_name,$this->tabdata_font_style,$this->tabdata_font_size);
					if (strlen($this->tabdata_font_color)==6) {
						$r=substr($this->tabdata_font_color,0,2);
						$g=substr($this->tabdata_font_color,2,2);
						$b=substr($this->tabdata_font_color,4,2);
					} else { $r="00"; $g="00"; $b="00"; }
//					echo "font:$r $g $b<br>";
					$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));

					if (strlen($this->tabdata_fill_color)==6) {
						$r=substr($this->tabdata_fill_color,0,2);
						$g=substr($this->tabdata_fill_color,2,2);
						$b=substr($this->tabdata_fill_color,4,2);
						$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//						echo "fill:$r $g $b<br>";
					}
					if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle); 

				} // end if ($this->f_table==1)

				return $res;
			}	// end function print_tab_header

			function set_tab_head_title($title)
			{
				$this->tab_head_title = $title;
			}

			function add_data_tab($arr_data, $line_height=7, $border="TRHBL", $data_align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
			{
				$ret = true;
				if ($this->f_table==1) {

					$this->arr_column_align = $data_align;		// align for data (data_align 㹷�����ѧ������Ѻ��� map index �ѧ��� � function ��� ��ͧ��ͺ map index)
																								//		����� ੾��� function ��� ��� $this->arr_column_align ��� map ���� �е�ͧ��
																								//	 	��ҹ��  $this->arr_column_align[$this->arr_column_map[$iii]]

					if (!$font) $font=$this->FontFamily;
//					if (!$font_style) $font_style=$this->FontStyle;	// �������դ���Ҩ���¶֧ ��� bold ��� italic ��� underline ��������¶֧ ����դ�� �ѧ�������ͧ move ��� default
					if (!$font_size) $font_size=$this->FontSizePt;
//					echo "font=".$font." -- ".implode("|",$arr_data)."<br>";

					if (gettype($fill_color)=="array")		$this->arr_tabdata_fill_color = $fill_color;		else	{	$this->arr_tabdata_fill_color = (array) null;	 	$this->arr_tabdata_fill_color[] = $fill_color; 	}
					if (gettype($font)=="array")				$this->arr_tabdata_font_name = $font;			else	{	$this->arr_tabdata_font_name = (array) null; $this->arr_tabdata_font_name[] = $font; 	}
					if (gettype($font_size)=="array")	$this->arr_tabdata_font_size = $font_size;		else	{	$this->arr_tabdata_font_size = (array) null;	$this->arr_tabdata_font_size[] = $font_size; 	}
					if (gettype($font_style)=="array")	$this->arr_tabdata_font_style = $font_style;	else	{	$this->arr_tabdata_font_style = (array) null;	$this->arr_tabdata_font_style[] = $font_style; 	}
					if (gettype($font_color)=="array")	$this->arr_tabdata_font_color = $font_color;	else	{	$this->arr_tabdata_font_color = (array) null;	$this->arr_tabdata_font_color[] = $font_color; 	}
					if (gettype($border)=="array")		$this->arr_tabdata_border = $border;				else	{	$this->arr_tabdata_border = (array) null;		$this->arr_tabdata_border[] = $border; 	}

//					echo "arr data=".implode(",",$arr_data).", font_style=".implode(",",$this->arr_tabdata_font_style)."<br>";
					
					$d_align = "C";
					$max_line = 0;
					$arr_sub_data = (array) null;
					$arr_merge_grp = (array) null;
					$arr_msum_grp = (array) null;
					
					$cnt_column_head = count($this->arr_tab_head);
					for($iii = 0; $iii < $cnt_column_head; $iii++) 	$arr_msum_grp[]=0;

					$cnt_column = count($arr_data);
					$cnt_column_show = 0;
					for($jjj = 0; $jjj <= count($this->arr_tab_head); $jjj++)  if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) $cnt_column_show++;	
					
					if ($this->merge_rows) {
						$arr_merge_rows = explode(",", $this->merge_rows);
						$arr_rows_image = explode(",", $this->rows_image);
					} else {
						$arr_merge_rows = (array) null;
						$arr_rows_image = (array) null;
						for($iii = 0; $iii < $cnt_column_head; $iii++) {
							$arr_merge_rows[]="";
							$arr_rows_image[]="";
						}
					}

					$col_aggreg = $this->do_aggregate($arr_data);
					$arr_column_aggreg = explode("|", $col_aggreg);
//					echo ">>".implode(",",$arr_data).">>$col_aggreg (cnt_column_head=$cnt_column_head)<br>";
//					echo "col_aggreg=$col_aggreg<br>";

					$grpidx = "";
					$sum_merge = 0;
					$arr_text = (array) null;
					$arr_col_width = (array) null;
					$arr_col_align = (array) null;
					$arr_fontname = (array) null;
					$arr_fontstyle = (array) null;
					$arr_fontrotate = (array) null;
					$arr_fontsize = (array) null;
					$arr_fontcolor = (array) null;
					$arr_fborder = (array) null;
					$arr_ffill = (array) null;
					$cnt=0;
					$img_idx = 0;
					for($iii = 0; $iii < $cnt_column_head; $iii++) {
//						echo "column loop iii=$iii<br>";
						if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = �ʴ� column ���
							$nline = $arr_data[$this->arr_column_map[$iii]];
//							echo "aggregate at $iii-(".$this->arr_column_map[$iii].")->$nline==aggreg->".$arr_column_aggreg[$iii]."<br>";
							if (trim($arr_column_aggreg[$iii])) {	// agregate �ҡ do_agregate map �� ���º��������
								$nline = $arr_column_aggreg[$iii];
//								echo "aggregate at $iii-(".$this->arr_column_map[$iii].")->$nline==aggreg->".$arr_column_aggreg[$iii]."<br>";
							}

							$this->tabdata_font_name	= ($this->arr_tabdata_font_name[$this->arr_column_map[$iii]]	? $this->arr_tabdata_font_name[$this->arr_column_map[$iii]]	: $this->arr_tabdata_font_name[0]);
//							$this->tabdata_font_style	= ($this->arr_tabdata_font_style[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_style[$this->arr_column_map[$iii]]	: "");
							$buff = ($this->arr_tabdata_font_style[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_style[$this->arr_column_map[$iii]]	: "");
							$this->tabdata_font_style = "";
							if (strpos(strtolower($buff),"b")!==false) {
								$this->tabdata_font_style .= "b";
								$buff = str_replace("b","",$buff);
								$buff = str_replace("B","",$buff);
							}
							if (strpos(strtolower($buff),"U")!==false) {
								$this->tabdata_font_style .= "u";
								$buff = str_replace("u","",$buff);
								$buff = str_replace("U","",$buff);
							}
							if (strpos(strtolower($buff),"I")!==false) {
								$this->tabdata_font_style .= "i";
								$buff = str_replace("i","",$buff);
								$buff = str_replace("I","",$buff);
							}
							if (strpos(strtolower($buff),"r")!==false) {
								$buff = str_replace("r","",$buff);
								$buff = str_replace("R","",$buff);
								$this->tabdata_rotate_angle = ($buff ? $buff : "90");
							} else {
								$this->tabdata_rotate_angle = "";
							}
							if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle);  else $this->Rotate(0);
							$this->tabdata_font_size	= ($this->arr_tabdata_font_size[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_size[$this->arr_column_map[$iii]]		: $this->arr_tabdata_font_size[0]);
							$this->tabdata_font_color	= ($this->arr_tabdata_font_color[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_color[$this->arr_column_map[$iii]]	: $this->arr_tabdata_font_color[0]);
							$this->tabdata_border		= ($this->arr_tabdata_border[$this->arr_column_map[$iii]]			? $this->arr_tabdata_border[$this->arr_column_map[$iii]]			: (count($this->arr_tabdata_border) > 1 ? "" : $this->arr_tabdata_border[0]));
							$this->tabdata_fill_color		= ($this->arr_tabdata_fill_color[$this->arr_column_map[$iii]]		? $this->arr_tabdata_fill_color[$this->arr_column_map[$iii]]		: $this->arr_tabdata_fill_color[0]);

							$f_fill = 0;
							if (strlen($this->tabdata_fill_color)==6) {
								$f_fill = 1;
							}

							$border = $this->tabdata_border;
//							echo "$iii-".$nline."|<br>";
							$chk_merge = strpos($nline,"<**");
							if ($chk_merge!==false) {
								$c = strpos($nline,"**>");
								if ($c!==false) {
									$mgrp = substr($nline, $chk_merge+3, $c-($chk_merge+3));
//									echo "[$nline]..group>>".$mgrp."==".$grpidx."<br>"; 
									if ($mgrp != $grpidx) {	// �ա������¹����� merge column �Դ�ѹ
										if ($grpidx) $arr_merge_grp[$last_iii] = "end";
										$grpidx = $mgrp;
										$arr_merge_grp[$iii] = $grpidx;
//										echo "1..iii=$iii -> merge(".$arr_merge_grp[$iii].") , last_iii=$last_iii -> merge(".$arr_merge_grp[$last_iii].")<br>";
										$arr_msum_grp[$grpidx]=(float)$this->arr_column_width[$this->arr_column_map[$iii]];		//	(float)$this->arr_head_width[$iii];
										$sum_merge=(float)$this->arr_column_width[$this->arr_column_map[$iii]];		//	(float)$this->arr_head_width[$iii];
									} else {	// ������繡�������ǡѹ
										$arr_merge_grp[$iii] = $grpidx;
//										echo "2..iii=$iii -> merge(".$arr_merge_grp[$iii].")<br>";
										$arr_msum_grp[$grpidx]+=(float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
										$sum_merge+=(float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
									}
									$ntext = substr($nline, 0, $chk_merge)." ".substr($nline, $c+3);
								} else {	// �ٻẺ�Դ��Ҵ ��ǻԴ����������
									$ret = false;
								}
							} else {	// ��� column �������� �����
								if ($grpidx)  $arr_merge_grp[$last_iii] = "end";	// �Դ������ش����
								$arr_merge_grp[$iii] = "";
//								echo "3..iii=$iii -> merge(".$arr_merge_grp[$iii].") , last_iii=$last_iii -> merge(".$arr_merge_grp[$last_iii].")<br>";
								$ntext = $nline;
								$grpidx = "";
							} // end if ($chk_merge!==false)
//							if ($arr_merge_grp) echo "grp:".implode(",",$arr_merge_grp)."<br>";
//							echo "bf row-->$ntext<br>";
							$chk_mrow = strpos($ntext,"<&&row&&>");
							if ($chk_mrow!==false) {
								if ($arr_merge_rows[$iii]=="top")
									$arr_merge_rows[$iii] = "middle";
								elseif ($arr_merge_rows[$iii]=="")
									$arr_merge_rows[$iii] = "top";
								$this->merge_rows_h[$iii] += $line_height;
								$ntext = str_replace("<&&row&&>", " ", $ntext);
							} else {
								$chk_mrow = strpos($ntext,"<&&end&&>");
								if ($chk_mrow!==false) {
									$this->merge_rows_h[$iii] += $line_height;
									$arr_merge_rows[$iii] = "endmerge";  // �͡��������һԴ merge row
									$ntext = str_replace("<&&end&&>", " ", $ntext);
								} elseif ($arr_merge_rows[$iii]=="top" || $arr_merge_rows[$iii]=="middle") {
									$arr_merge_rows[$iii] = "endmerge";  // �͡��������һԴ merge row
									$this->merge_rows_h[$iii] += $line_height;
								} else
									$arr_merge_rows[$iii] = "";
							}
//							if ($arr_merge_rows) echo "row:".implode(",",$arr_merge_rows)."<br>";
//							echo "bf img-->$ntext<br>";
							$chk_image = strpos($ntext,"<*img*");
							if ($chk_image!==false) {
									$c = strpos($ntext,"*img*>");
									if ($c!==false) {
										$ntext1 = $arr_data[$this->arr_column_map[$iii]];
										$c1 = strpos($ntext1,"<*img*");
										$c2 = strpos($ntext1,"*img*>");
										$arr_rows_image[$img_idx] = substr($ntext1, $c1+6, $c2-($c1+6));
//										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill, false);
										$ntext = substr($ntext, 0, $chk_image)."~".substr($ntext, $c+6);
//										echo "img..$ntext ,$chk_image, $c ($ntext1) arr_rows-image [$iii]=".$arr_rows_image[$iii]."<br>";
//										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $arr_column_align[$this->arr_column_map[$iii]],$f_fill, false);
										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $ntext, $border, $arr_column_align[$this->arr_column_map[$iii]],$f_fill, false);
//										echo "$iii - image name=".$arr_rows_image[$iii].", text=$ntext , arr_text=".$arr_text[$iii]."<br>";
									} else {
										$ret = false;
									}
							} else {
									$arr_rows_image[$iii] = "";
							}
							$arr_text[] = $ntext;
							$arr_col_width[] = $this->arr_column_width[$this->arr_column_map[$iii]];
							$arr_col_align[] = $this->arr_column_align[$this->arr_column_map[$iii]];
							$arr_fontname[] = $this->tabdata_font_name;
							$arr_fontstyle[] = $this->tabdata_font_style;
							$arr_fontrotate[] = $this->tabdata_rotate_angle;
							$arr_fontsize[] = $this->tabdata_font_size;
							$arr_fontcolor[] = $this->tabdata_font_color;
							$arr_fborder[] = $this->tabdata_border;
							$arr_ffill[] = $this->tabdata_fill_color	;

							$cnt++;
							$img_idx++;
//							echo "just show ($cnt)-$ntext | ".$this->arr_column_width[$iii]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]."<br>";
							$last_iii = $iii;
						} // if ($this->arr_column_sel[$iii]==1) {	// 1 = �ʴ� column ���
					} // end for $iii (���� column)
					if ($ret && strlen($arr_merge_grp[$iii-1]) > 0) $arr_merge_grp[$iii-1] = "end"; // ��Ǩ�ͺ�ó� merge column ����ش���·���ش�ʹ�
					
					$have_img = 0;
					if ($ret) {
						$grpidx = "";
						for($iii = 0; $iii < $cnt_column_show; $iii++) {	// ���͡੾����¡�÷�� �ʴ�
							$this->tabdata_font_name	= $arr_fontname[$iii];
							$this->tabdata_font_style	= $arr_fontstyle[$iii];
							$this->tabdata_rotate_angle = $arr_fontrotate[$iii];
							$this->tabdata_font_size	= $arr_fontsize[$iii];
							$this->tabdata_font_color	= $arr_fontcolor[$iii];
							$this->tabdata_border		= $arr_fborder[$iii];
							$this->tabdata_fill_color		= $arr_ffill[$iii];

							if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle);  else $this->Rotate(0);

							$this->SetFont($this->tabdata_font_name, $this->tabdata_font_style, $this->tabdata_font_size);
		
							$f_fill = 0;
							if (strlen($this->tabdata_fill_color)==6) {
								$f_fill = 1;
							}

							$border = $this->tabdata_border;
							
//							echo "arr_text [$iii]=".$arr_text[$iii]."<br>";
							if ($arr_merge_grp[$iii] == "end") {
//							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
//								echo "end grp ($grpidx)-->$iii w:".$arr_msum_grp[$grpidx]." text:".$arr_text[$iii]." border:".$border." align:".$arr_col_align[$iii]."<br>";
							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							} elseif ($arr_merge_grp[$iii]) {
								$grpidx = $arr_merge_grp[$iii];
//							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
//								echo "grp-($grpidx)->$iii w:".$arr_msum_grp[$grpidx]." text:".$arr_text[$iii]." border:".$border." align:".$arr_col_align[$iii]."<br>";
							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							} else {
								$grpidx="";
//								$arr_b = $this->MultiCellThaiCut($this->arr_head_width[$iii], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
//								echo "not grp->$iii w:".$arr_col_width[$iii]." text:".$arr_text[$iii]." border:".$border." align:".$arr_col_align[$iii]."<br>";
								$arr_b = $this->MultiCellThaiCut($arr_col_width[$iii], (float)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							}
							
						 	$arr_sub_data[] = $arr_b;
//							echo "$iii - ".$arr_merge_grp[$iii]." sub_data:".implode(",", $arr_sub_data[$iii])."<br>";
							$this_line = count($arr_sub_data[$iii]);
							if ($arr_rows_image[$iii]) {	// �óշ�� �觤�� �ӹǹ ��÷Ѵ �ҡѺ image ����
								$img = explode(",",$arr_rows_image[$iii]);
								if ($img[1]) $this_line = (int)$img[1];
								$have_img = 1;
							}
							if($this_line > $max_line) $max_line = $this_line;
//							if (strlen($arr_merge_rows[$iii]) > 0) echo "ntext=$ntext|".$this_line.":".$max_line."<br>";
						}
					}
					
					$head_t_w = 0;
					if ($ret) {
//						echo "arr_tab_head>>".implode(",",$this->arr_tab_head)."<br>";
//						echo "cnt_line_head=$cnt_line_head, cnt_column_head=$cnt_column_head<br>";
						for($jjj = 0; $jjj < $cnt_column_show; $jjj++) {
							$head_t_w+=$arr_col_width[$jjj];		// $this->arr_head_width[$jjj];
						}
					} // end if $ret

					if ($ret) {
						$start_x = $this->x;			$start_y = $this->y;				$max_y = $this->y;
						$first_y = $this->y;
					
						$columngrp="";

						$sum_merge = 0;
						$text_merge = "";
						$d_align = "";

						if ($have_img && ($this->y + ($max_line * $line_height)) > ($this->h - $tab_PageBreakTrigger)) {	// check line ���
							$old_y = $this->y;
							$this->y += ($max_line * $line_height);	// �� $this->y ��Ф�� $old_y ����� check ˹������ nohead = false
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger, false, $old_y);
//							echo "have_img=$have_img<br>";
						}
						for($cntline = 0; $cntline < $max_line; $cntline++) {
							// check ˹������͹������÷Ѵ����
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger);

							$sum_merge = 0;
							for($iii = 0; $iii < $cnt_column_show; $iii++) {
								$this->tabdata_font_name	= $arr_fontname[$iii];
								$this->tabdata_font_style	= $arr_fontstyle[$iii];
								$this->tabdata_rotate_angle = $arr_fontrotate[$iii];
								$this->tabdata_font_size	= $arr_fontsize[$iii];
								$this->tabdata_font_color	= $arr_fontcolor[$iii];
								$this->tabdata_border		= $arr_fborder[$iii];
								$this->tabdata_fill_color		= $arr_ffill[$iii];

								if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle);  else $this->Rotate(0);

								$this->SetFont($this->tabdata_font_name, $this->tabdata_font_style, $this->tabdata_font_size);
								if (strlen($this->tabdata_font_color)==6) {
									$r=substr($this->tabdata_font_color,0,2);
									$g=substr($this->tabdata_font_color,2,2);
									$b=substr($this->tabdata_font_color,4,2);
								} else { $r="00"; $g="00"; $b="00"; }
	//							echo "font:$r $g $b<br>";
								$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
			
								$f_fill = 0;
								if (strlen($this->tabdata_fill_color)==6) {
									$r=substr($this->tabdata_fill_color,0,2);
									$g=substr($this->tabdata_fill_color,2,2);
									$b=substr($this->tabdata_fill_color,4,2);
									$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
	//								echo "fill:$r $g $b<br>";
									$f_fill = 1;
								}
	
								$border = $this->tabdata_border;

								$nline = $arr_sub_data[$iii][$cntline];
								$sum_w = 0;
								for($jjj = 0; $jjj <= $iii; $jjj++)  $sum_w+=(float)$arr_col_width[$jjj];		// 	(float)$this->arr_head_width[$jjj];

//								echo "$iii - max_line=$max_line, cntline=$cntline, border=$t_border, merge:".$arr_merge_rows[$iii]."<br>";
								$t_border = strtoupper($border);
								$mid_hline = (strpos($t_border, "H")!==false ? true : false);  // H ᷹��鹢�ҧ��ҧ ���������� �ִ��鹢�ҧ�ç��ҧ
								$t_border = str_replace("H", "", $t_border);	// ��˹���� H ��� $mid_hline ���� ������͡�ҡ $t_border
								if ($max_line > 1) { // �ó� �� cell ���º�÷Ѵ
									if ($cntline==0) { // ��÷Ѵ�á�ͧ cell ��������ҧ�͡ ������ cell ��ҧ�ͧ merge row �������鹺��͡����
										if ($arr_merge_rows[$iii]=="middle") $t_border = str_replace("T", "", $t_border);
										$t_border = str_replace("B", "", $t_border);
									} elseif ($cntline==$max_line-1) {
										if ($arr_merge_rows[$iii]=="middle") $t_border = str_replace("B", "", $t_border);
										$t_border = str_replace("T", "", $t_border);
									} else  { 
										$t_border = str_replace("B", "", $t_border); 
										$t_border = str_replace("T", "", $t_border); 
									}
								} elseif ($max_line == 1) {
									if ($arr_merge_rows[$iii]=="top") {
										$t_border = str_replace("B", "", $t_border);
									} elseif ($arr_merge_rows[$iii]=="middle") {
										$t_border = str_replace("T", "", $t_border);
										$t_border = str_replace("B", "", $t_border);
									} elseif ($arr_merge_rows[$iii]=="endmerge") {
										$t_border = str_replace("T", "", $t_border);
									}
								}
//								echo "$iii - max_line=$max_line, cntline=$cntline, border=$t_border, merge:".$arr_merge_rows[$iii]."<br>";
//								echo "col width [$iii] = ".$arr_col_width[$iii]."<br>";

								if ($iii == $cnt_column_show-1) $next_line = 1; else $next_line = 0;
								if ($arr_merge_grp[$iii]) {
//									echo "arr_merge_grp [$iii]=".$arr_merge_grp[$iii]."<br>";
									if ($iii > 0) {
//										echo "sum_merge=$sum_merge , grp=".$arr_merge_grp[$iii]."<br>";
										if ($sum_merge > 0 && strpos($arr_merge_grp[$iii], "end") !== false) { // ��� �� merge group
											$sum_merge+=(float)$arr_col_width[$iii];	//  (float)$this->arr_head_width[$iii];
//											echo "1) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (!$mid_hline) { // �������˹���� H � border �������ҡ��鹢�ҧ
												if ($mergecell_first != 0) {  // ��� merge cell ��������������� cell �á ��� L �͡ ���� cell �ش���� ����� R
													$t_border = str_replace("L", "", $t_border);
												}
											}
//											echo "1..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
//													echo "1..$iii..".$arr_rows_image[$iii].", text=".$text_merge."<br>";
													$this->print_column_image($sum_merge, $merge_rows_h, $text_merge, $line_height, $arr_rows_image[$iii], $t_border, $d_align, $f_fill, $next_line);
													$arr_rows_image[$iii] = "";
												} else { // ��� �� text ��ǹ �
													$this->Cell($sum_merge, $line_height, $text_merge, $t_border, $next_line, $d_align, $f_fill);
												} // end if ($arr_rows_image[$iii])
												$arr_merge_rows[$iii]="";
												$arr_rows_image[$iii]="";
												$this->merge_rows_h[$iii] = 0;
												$merge_rows_h = 0;
											} else	// ��� ��� end merge row
												if (strlen(trim($arr_merge_rows[$iii])) > 0)	// ����ѧ����� merge row
													$this->Cell($sum_merge, $line_height, "", $t_border, $next_line, $d_align, $f_fill);
												else
													$this->Cell($sum_merge, $line_height, $text_merge, $t_border, $next_line, $d_align, $f_fill);
//											echo "sum_merge=$sum_merge, line_height=$line_height, text_merge=$text_merge<br>";
											$sum_merge = 0;
											$text_merge = "";
										} else if (strpos($arr_merge_grp[$iii], "end") !== false) { // ��� �� merge group
											$sum_merge+=(float)$arr_col_width[$iii];	//  (float)$this->arr_head_width[$iii];
//											echo "1.1) $iii-grp:".$arr_merge_grp[$iii]." nline=$nline sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (!$mid_hline) { // �������˹���� H � border �������ҡ��鹢�ҧ
												if ($mergecell_first != 0) {  // ��� merge cell ��������������� cell �á ��� L �͡ ���� cell �ش���� ����� R
													$t_border = str_replace("L", "", $t_border);
												}
											}
//											echo "1..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
//													echo "1..$iii..".$arr_rows_image[$iii].", text=".$text_merge."<br>";
													$this->print_column_image($sum_merge, $merge_rows_h, $nline, $line_height, $arr_rows_image[$iii], $t_border, $d_align, $f_fill, $next_line);
													$arr_rows_image[$iii] = "";
												} else { // ��� �� text ��ǹ �
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
												} // end if ($arr_rows_image[$iii])
												$arr_merge_rows[$iii]="";
												$arr_rows_image[$iii]="";
												$this->merge_rows_h[$iii] = 0;
												$merge_rows_h = 0;
											} else	// ��� ��� end merge row
												if (strlen(trim($arr_merge_rows[$iii])) > 0)	// ����ѧ����� merge row
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//													$this->Cell($sum_merge, $line_height, "", $t_border, $next_line, $d_align, $f_fill);
												else
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//											echo "sum_merge=$sum_merge, line_height=$line_height, nline=$nline<br>";
											$sum_merge = 0;
											$text_merge = "";
										} else {	// ����ѧ����� merge group ���ѧ��診 merge group
											if  ($sum_merge == 0) { // ����� merge group ����
												$d_align = ($arr_col_align[$iii] ? $arr_col_align[$iii] : "C");
												$mergecell_first = $iii;
												if (!$text_merge) $text_merge = $nline;
											}
											$sum_merge+=(float)$arr_col_width[$iii];	//	(float)$this->arr_head_width[$iii];
//											echo "2) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (strlen(trim($arr_merge_rows[$iii])) > 0) // ����� merge row �á
												$merge_rows_h = $this->merge_rows_h[$iii];
										} //  end if ($sum_merge > 0 && strpos($arr_merge_grp[$iii], "end") !== false)
									} else { // $iii == 0 �� merge cell  �á
										if (strpos($arr_merge_grp[$iii], "end") !== false) { // ��� �� merge cell �á��� end ���
											$sum_merge = (float)$arr_col_width[$iii];	//  (float)$this->arr_head_width[$iii];
//											echo "3.1) $iii-grp:".$arr_merge_grp[$iii]." nline=$nline sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (!$mid_hline) { // �������˹���� H � border �������ҡ��鹢�ҧ
												if ($mergecell_first != 0) {  // ��� merge cell ��������������� cell �á ��� L �͡ ���� cell �ش���� ����� R
													$t_border = str_replace("L", "", $t_border);
												}
											}
//											echo "1..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
//													echo "1..$iii..".$arr_rows_image[$iii].", text=".$text_merge."<br>";
													$this->print_column_image($sum_merge, $merge_rows_h, $nline, $line_height, $arr_rows_image[$iii], $t_border, $d_align, $f_fill, $next_line);
													$arr_rows_image[$iii] = "";
												} else { // ��� �� text ��ǹ �
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
												} // end if ($arr_rows_image[$iii])
												$arr_merge_rows[$iii]="";
												$arr_rows_image[$iii]="";
												$this->merge_rows_h[$iii] = 0;
												$merge_rows_h = 0;
											} else	// ��� ��� end merge row
												if (strlen(trim($arr_merge_rows[$iii])) > 0)	// ����ѧ����� merge row
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//													$this->Cell($sum_merge, $line_height, "", $t_border, $next_line, $d_align, $f_fill);
												else
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//											echo "sum_merge=$sum_merge, line_height=$line_height, nline=$nline<br>";
											$sum_merge = 0;
											$text_merge = "";
										} else {
											// ����� merge cell ������ѹ�á ��˹���� merge �á
											$sum_merge+=(float)$arr_col_width[$iii];	//	(float)$this->arr_head_width[$iii];
//											$d_align = ($data_align[$iii] ? $data_align[$iii] : "C");
											$d_align = ($arr_col_align[$iii] ? $arr_col_align[$iii] : "C");
											if (!$text_merge) $text_merge = $nline;
											$mergecell_first = $iii;
											if (strlen(trim($arr_merge_rows[$iii])) > 0) // ����� merge row �á
												$merge_rows_h = $this->merge_rows_h[$iii];
	//										echo "3) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
										} // if (strpos($arr_merge_grp[$iii], "end") !== false)
									} // end if ($iii > 0)
								} else { //  not if ($arr_merge_grp[$iii]
//									echo "sum_merge=$sum_merge<br>";
									if ($sum_merge > 0) { // ��ҵ�ǵ�������� merge cell ��������ǡѹ ��зӡ�ûԴ merge cell
										$col_w = $sum_merge;
									} else {
										$col_w = $arr_col_width[$iii];		// $this->arr_head_width[$iii];
									}
									if (!$mid_hline) { // �������˹���� H � border �������ҡ��鹢�ҧ
										if ($iii != 0)  $t_border = str_replace("L", "", $t_border);
										if ($next_line != 1)  $t_border = str_replace("R", "", $t_border);
									}
//									echo "2..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
									if ($arr_merge_rows[$iii]=="endmerge") {
										if ($arr_rows_image[$iii]) {
//											$this->print_column_image($col_w, $this->merge_rows_h[$iii], $nline, $line_height, $arr_rows_image[$iii], $t_border, $data_align[$iii], $f_fill, $next_line);
//											echo "2..$iii..".$arr_rows_image[$iii].", text=$nline<br>";
											$this->print_column_image($col_w, $this->merge_rows_h[$iii], $nline, $line_height, $arr_rows_image[$iii], $t_border, $arr_col_align[$iii], $f_fill, $next_line);
											$arr_rows_image[$iii] = "";
										} else {	// ���������ٻ
											$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
										}
										$arr_merge_rows[$iii]="";
										$arr_rows_image[$iii]="";
										$this->merge_rows_h[$iii] = 0;
//										echo "merge $iii-".$arr_merge_rows[$iii]." next_line=$next_line [$nline] x:y=".$this->x.":".$this->y."<br>";
//										echo "4) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
									} else {  // �������� endmerge
//										if (!$arr_merge_rows[$iii]) echo "1..next_line=$next_line [$nline] x:y=".$this->x.":".$this->y." h=$line_height<br>";
//										echo "3..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
										if (strlen(trim($arr_merge_rows[$iii])) > 0) {
//											$this->Cell($col_w, $line_height, "", $t_border, $next_line, $data_align[$iii], $f_fill);
											$this->Cell($col_w, $line_height, "", $t_border, $next_line, $arr_col_align[$iii], $f_fill);
//											echo "merge row - $iii - x:y=".$this->x.":".$this->y." - line_h=$line_height<br>";
										} else { // ���������� merge row
											if ($arr_rows_image[$iii]) { // ������ٻ
//												$this->print_column_image($col_w, $line_height, $nline, $line_height, $arr_rows_image[$iii], $t_border, $data_align[$iii], $f_fill, $next_line);
//												$this->print_column_image($col_w, $line_height, $nline, $line_height, $arr_rows_image[$iii], $t_border, $arr_col_align[$iii], $f_fill, $next_line);
												$f_one_line = 1;
												$img = explode(",",$arr_rows_image[$iii]);
//												echo "1..x::".$this->x.",y::".$this->y."<br>";
												if ($img[1]=="fixsize") {
													$f_one_line = 2;
												} else {
													if ((int)$img[1] > $max_line) $max_line = (int)$img[1]; 	// ��ҡ�˹� max_line ���͵���Ţ��ѧ comma <*img*name,c*img*> c ��� max_line
												}
//												if (strpos($arr_rows_image[$iii],"3100600640837")!==false) 
//												if (strpos($arr_rows_image[$iii],"3101400135641")!==false)  
//													echo "3..$iii..".$arr_rows_image[$iii].",  text=$nline, h=line_height($line_height) * max_line($max_line) (f_one_line=$f_one_line)<br>";
												$keep_y = $this->y;
												$this->print_column_image($col_w, $line_height*$max_line, $nline, $line_height, $img[0], $t_border, $arr_col_align[$iii], $f_fill, $next_line, $f_one_line);
												if ($this->y > $keep_y)
													$this->y = $keep_y+$line_height;	// ��ҵ��˹� y ������� ���˹����� 1 ��÷Ѵ
//												echo "2..x::".$this->x.",y::".$this->y." (".($this->y-$keep_y).")<br>";
												$arr_rows_image[$iii] = "";
											} else { // ���������ٻ
//												$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $data_align[$iii], $f_fill);
												$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $arr_col_align[$iii], $f_fill);
//												echo "4..$iii..$nline, $t_border, $d_align (".$arr_col_align[$iii].")<br>";
//												if (!$arr_merge_rows[$iii]) echo "$iii..next_line=$next_line [$nline] x:y=".$this->x.":".$this->y." h=$line_height,  col_w=$col_w<br>";
											} // end if ($arr_rows_image[$iii])
										} // end if (strlen(trim($arr_merge_rows[$iii]))
//										echo "5) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
									} // if ($arr_merge_rows[$iii]=="endmerge")
								} // end if ($chk_merge!==false)
							} //// end for $iii loop
						} // end for $cntline
					} // end if ($ret)
					$this->merge_rows = implode(",", $arr_merge_rows);
					$this->rows_image = implode(",", $arr_rows_image);
				} // end if ($this->f_table==1)
				return $ret;
			}	// end function add_data_tab

			function add_text_line($text, $line_height=7, $border="TRHBL", $align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
			{
				$ret = true;
				if ($this->f_table==1) {

					if (!$font) $font=$this->FontFamily;
//					if (!$font_style) $font_style=$this->FontStyle;	// �������դ���Ҩ���¶֧ ��� bold ��� italic ��� underline ��������¶֧ ����դ�� �ѧ�������ͧ move ��� default
					if (!$font_size) $font_size=$this->FontSizePt;
	//				echo "font=".$font."<br>";
	
					$this->tabdata_fill_color = $fill_color;
					$this->tabdata_font_name = $font;
					$this->tabdata_font_size = $font_size;
					$this->tabdata_font_style = $font_style;
					$this->tabdata_font_color = $font_color;

					// $align 㹷���� ���ͧ�ҡ��觾���� text ������� �֧�� align �Ҥ������
					$d_align = $align;
					$this->SetFont($font,$font_style,$font_size);
					if (strlen($font_color)==6) {
						$r=substr($font_color,0,2);
						$g=substr($font_color,2,2);
						$b=substr($font_color,4,2);
					} else { $r="00"; $g="00"; $b="00"; }
//					echo "font:$r $g $b<br>";
					$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));

					$f_fill = 0;
					if (strlen($fill_color)==6) {
						$r=substr($fill_color,0,2);
						$g=substr($fill_color,2,2);
						$b=substr($fill_color,4,2);
						$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//						echo "fill:$r $g $b<br>";
						$f_fill = 1;
					}

					$cnt_column_head = count($this->arr_tab_head);
					$cnt_column_show = 0;
					$sum_w = 0;
					for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
						if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { $cnt_column_show++;	 $sum_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; }
					}

				 	$arr_b = $this->MultiCellThaiCut($sum_w, (float)$line_height, $text, $border, $d_align,$f_fill,false);

					$next_line = 1; 
					for($iii = 0; $iii < count($arr_b); $iii++) {
						if ($arr_b[$iii]) {
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger);
							if ($this->f_head_tnum) $realtext = convert2thaidigit($arr_b[$iii]); // ����ա�÷� TNUM  ����¹�������� ����Ţ�¡�͹�Ѵ��
							else $realtext = $arr_b[$iii];
							$this->Cell($sum_w, $line_height, $realtext, $border, $next_line, $d_align, $f_fill);
						}
					}
				} // end if ($this->f_table==1)
				return $ret;
			}	// end function add_text_line

			function print_column_image0($w, $h, $text, $text_h, $imgfile, $border, $align, $f_fill, $next_line) {
					$d_align = ($align ? $align : "C");
					$save_y = $this->y;  $save_x = $this->x;  
//					echo "begin $save_x:$save_y, h=$h, text_h=$text_h, imgfile=$imgfile<br>";
					$this->Cell($w, $text_h, "", $border, $next_line, $align, $f_fill);
					$this->y = $save_y - $h + $text_h;
					$this->x = $save_x;
					$arr_buff = explode("~", $text);
					if (strlen(trim($arr_buff[0])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[0], $border, 1, $align, $f_fill);
						$this->x = $save_x;  
					}
//					echo "image ($imgfile) exist : [".file_exists($imgfile)."]<br>";
					if (file_exists($imgfile)) {
						list($width, $height, $type, $attr) = getimagesize($imgfile);
						if ($width > $w-4) {
							$image_w = 	$w-4;
							$image_h = floor($image_w / $width * $height);
							if ($image_h > $h-4) {
								$buff_h = 	$h-4;
								$image_w = floor($buff_h / $image_h * $image_w);
								$image_h = $buff_h;
							}
						} elseif ($height > $h-4) {
							$image_h = 	$h-4;
							$image_w = floor($image_h / $height * $width);
							if ($image_w > $w-4) {
								$buff_w = 	$w-4;
								$image_h = floor($buff_w / $image_w * $image_h);
								$image_w = $buff_w;
							}
						} else {
							$image_h = $height;
							$image_w = $width;
						}
						if ($align=="L")
							$posx = $this->x+2;
						elseif ($align=="C")
							$posx = $this->x+($w/2)-($image_w/2);
						else
							$posx = $this->x+$w-2-$image_w;
						$posy = $this->y+2;
//						echo "0..x:y-".$this->x.":".$this->y." w:h-$width:$height, $image_w:$image_h |".$this->merge_rows_h[$iii]." (w=$w:h=$h) (posx:$posx, posy:$posy)<br>";
						$this->Image($imgfile, $posx, $posy, $image_w, $image_h);
						$this->x = $save_x;  
						$this->y += $image_h+4;
//						echo "1..image x=".$this->x.",y=".$this->y."<br>";
					} else {
						$this->x = $save_x;  
						$this->y += $h;
//						echo "2..image x=".$this->x.",y=".$this->y."<br>";
					}
					
					if (strlen(trim($arr_buff[1])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[1], $border, 1, $align, $f_fill);
					}
					if ($next_line==1) {
						$this->x = $this->lMargin;  
//						echo "1. x:y=".$this->x.":".$this->y."<br>";
					} else {
						$this->x = $save_x+$w;  
						$this->y = $save_y;
//						echo "2. x:y=".$this->x.":".$this->y."<br>";
					}
			} // end function print_column_image

			function print_column_image($w, $h, $text, $text_h, $imgfile, $border, $align, $f_fill, $next_line, $f_one_line=0) {
					$pix_border_w = $w;
					$d_align = ($align ? $align : "C");
					$save_y = $this->y;  $save_x = $this->x;  
//					echo "begin $save_x:$save_y, h=$h, text_h=$text_h, imgfile=$imgfile (x=".$this->x." , y=".$this->y.")<br>";
					$this->Cell($w, $text_h, "", $border, $next_line, $align, $f_fill);
//					echo "0.................x:y-".$this->x.":".$this->y." , save_y=$save_y , h=$h , text_h=$text_h ($text)<br>";
					if ($f_one_line==2) {
						$img_h = $text_h;	// ����繻����� one line Ẻ��� 2 ��� image �բ�Ҵ �� ��÷Ѵ����
					} else {
						$img_h = $h;		// ������ one line �����٧�ͧ�Ҿ ���� ��� �����٧�ͧ column
					}
//					if ($h > $text_h) $f_one_line = 0;
					
					if (!$f_one_line) {
						$this->y = $save_y - $h + $text_h;
//						echo "a...........$save_y,  $h, $text_h (".$this->y.")<br>";
//					} else if ($f_one_line==2) {
//						$this->y = $save_y - $text_h;
//						echo "b...........$save_y,  $h, $text_h (".$this->y.")<br>";
					} else {
						$this->y = $save_y;
//						echo "c...........$save_y,  $h, $text_h (".$this->y.")<br>";
					}
//					$this->y = $save_y;
					$this->x = $save_x;
					$arr_buff = explode("~", $text);
					$line_minus = 0;
					if (strlen(trim($arr_buff[0])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[0], $border, 1, $align, $f_fill);
//						echo "1111<br>";
						$this->x = $save_x;  
						$line_minus++;
					}
					if (strlen(trim($arr_buff[1])) > 0) {
						$line_minus++;
					}
//					echo "1.................x:y-".$this->x.":".$this->y."  line_munus=$line_minus<br>";
//					echo "image ($imgfile) exist : [".file_exists($imgfile)."]<br>";
					if (file_exists($imgfile)) {
						list($width, $height, $type, $attr) = getimagesize($imgfile);
						$img_h -= ($text_h * $line_minus);	// ź�����٧�ͧ�ٻ�ó�����ǹ����繺�÷Ѵ�ͧ text
						if ($width > $pix_border_w) {
							$image_w = 	$pix_border_w-2;
							$image_h = floor($image_w / $width * $height);
							if ($image_h > $img_h) {
								$buff_h = 	$img_h-2;
								$image_w = floor($buff_h / $image_h * $image_w);
								$image_h = $buff_h;
							}
						} elseif ($height > $img_h) {
							$image_h = 	$img_h-2;
							$image_w = floor($image_h / $height * $width);
							if ($image_w > $pix_border_w) {
								$buff_w = 	$pix_border_w-2;
								$image_h = floor($buff_w / $image_w * $image_h);
								$image_w = $buff_w;
							}
						} else {
							$image_h = $height;
							$image_w = $width;
						}
						if ($align=="L")
							$posx = $this->x+2;
						elseif ($align=="C")
							$posx = $this->x+($pix_border_w/2)-($image_w/2);
						else
							$posx = $this->x+$pix_border_w-2-$image_w;
						$posy = $this->y+1;//-($f_one_line?$text_h:0);
//						echo "1..x:y-".$this->x.":".$this->y." w:h-$width:$height, $image_w:$image_h |".$this->merge_rows_h[$iii]." (w=$pix_border_w:h=$h) (posx:$posx, posy:$posy)<br>";
						$this->Image($imgfile, $posx, $posy, $image_w, $image_h);
//						$this->Image($imgfile, $posx, $posy, 5, 5);
						$this->x = $save_x;  
						$this->y += $image_h+2;
//						$this->y = $save_y + $h;
//						echo "1..image x=".$this->x.",y=".$this->y."<br>";
					} else { // else if (file_exists($imgfile))
						$this->x = $save_x;  
						$this->y += $h;
//						echo "2..image x=".$this->x.",y=".$this->y."<br>";
					}
					
					if (strlen(trim($arr_buff[1])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[1], $border, 1, $align, $f_fill);
					}
					if ($next_line==1) {
						$this->x = $this->lMargin;  
//						echo "1. x:y=".$this->x.":".$this->y."<br>";
					} else {
						$this->x = $save_x+$w;  
						$this->y = $save_y;
//						echo "2. x:y=".$this->x.":".$this->y."<br>";
					}
			} // end function print_column_image

			function close_tab($tail_text, $border="close", $at_end_up=10)
			{
				if ($this->f_table==1) {
					// �ӡ���ҡ��鹻Դ ��� border ��˹�
					if (strtolower($border)=="close") {	// ����觤�� $border ���� "close" �о���� ��鹻Դ��ͨҡ���˹��ش����
//						echo "close 1<br>";
						$sum_w = 0;
						for($jjj = 0; $jjj < count($this->arr_column_map); $jjj++) {
							if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { $sum_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; }
						}
						$line_start_y = $this->y;		$line_end_y = $this->y;
						$line_start_x = $this->lMargin;		$line_end_x = $line_start_x+$sum_w;
//						echo "��鹹͹�Դ-->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y)<br>";
						$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹹͹�Դ
					} else if (strtolower($border)!="no") {	// ����觤�� $border ���� "no" ��������� ��鹵��ҧ��ͨ��ش˹��
//						echo "close 2<br>";
						$have_l = strpos($border, "L");
						$have_r = strpos($border, "R");	
						$have_h = strpos($border, "H");

						$cnt_column_head = count($this->arr_tab_head);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //�մ�
						if ($have_l !== false) {	
							$line_start_y = $this->y;		$line_end_y = $this->h - $at_end_up;
							$line_start_x = $this->lMargin;		$line_end_x = $line_start_x;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�á
						}
						for($i=0; $i<$cnt_column_head; $i++) {
							if ($this->arr_column_sel[$this->arr_column_map[$i]]==1) {
//								$line_start_x += (float)$this->arr_head_width[$i];  $line_end_x += (float)$this->arr_head_width[$i];
								$line_start_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];  $line_end_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];
								if ($have_h !== false && $i != ($cnt_column_head-1))
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�������͵ç��ҧ
								elseif ($have_r !== false && $i == ($cnt_column_head-1)) 
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�ش����
							} // end if ($this->arr_column_sel[$i]==1)
						}	// end for $i
					}
					// ������ҡ��鹻Դ ��� border ��˹�
					$this->f_table = 0;
					$this->arr_func_aggreg = (array) null;	
					$this->arr_tab_head = (array) null;
					$this->arr_head_width = (array) null;
					$this->head_fill_color = "";
					$this->head_font_name = "";
					$this->head_font_size = "";
					$this->head_font_style = "";
					$this->head_font_color = "";
					$this->head_border = "";
					$this->arr_head_align = (array) null;
					$this->head_line_height = "";
					$this->tabdata_fill_color = "";
					$this->tabdata_font_name = "";
					$this->tabdata_font_size = "";
					$this->tabdata_font_style = "";
					$this->tabdata_rotate_angle = "";
					$this->tabdata_font_color = "";
					$this->arr_tabdata_font_name = (array) null;
					$this->arr_tabdata_font_style = (array) null;
					$this->arr_tabdata_font_size = (array) null;
					$this->arr_tabdata_font_color = (array) null;
					$this->arr_tabdata_border = (array) null;
					$this->arr_tabdata_fill_color = (array) null;
					$this->merge_rows = "";
					$this->rows_image = "";
					$this->merge_rows_h = (array) null;
					$this->arr_column_map = (array) null;
					$this->arr_column_sel = (array) null;
					$this->arr_column_width = (array) null;
					$this->arr_column_align = (array) null;
				} // end if ($this->f_table==1)
			}	// end function create_tab

			function line_MultiCellThaiCut($w,$h,$txt,$border=0,$align='J',$fill=0)
			{
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$b=0;
				if($border)
				{
					if($border==1)
					{
						$border='LTRB';
						$b='LRT';
						$b2='LR';
					}
					else
					{
						$b2='';
						if(strpos($border,'L')!==false)
							$b2.='L';
						if(strpos($border,'R')!==false)
							$b2.='R';
						$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
					}
				}
				$s = $this->thaiCutLinePDF($s, $w, "\n");
//				echo "***$s<br>";
				$sub_s = explode("\n", $s);
				for($ii = 0; $ii < count($sub_s); $ii++) {
//					echo "$ii-$sub_s[$ii]|<br>";
					if($border && $ii==1)
						$b=$b2;
					if($border && $ii == (count($sub_s)-1) && strpos($border,'B')!==false)
						$b.='B';
//					$this->Cell($w,$h,trim($sub_s[$ii]),$b,2,$align,$fill);
					$str_w = $this->GetStringWidth($sub_s[$ii]);
//					echo "LinethaiCut sub_s [$ii]=".$sub_s[$ii]." ($w::$str_w) $h,$txt,$border,$align,$fill<br>";
				}
//				$this->x=$this->lMargin;
/*
				//Output text with automatic or explicit line breaks
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$s = $this->thaiCutLinePDF($s, $w, "\n");
				$sub_s = explode("\n", $s);
//				echo "***sub_s:".implode("|",$sub_s)."<br>";
				for($i=0; $i < count($sub_s); $i++) {
					$str_w = $this->GetStringWidth($sub_s[$i]);
					echo "$i-".$sub_s[$i]." ($str_w == $w)<br>";
				}
*/				
				return $sub_s;
			} // end function

			function tab_pagebreak($border, $at_end_up=10, $nohead=false, $pos_last_y=0) {
				$ret = false;
				if ($this->f_table==1) {
//					echo " h (".$this->h.") - y (".$this->y.") - at_end_up ($at_end_up) < at_end_up ($at_end_up) [$border]<br>";
					if(($this->h - $this->y - $at_end_up) < $at_end_up){ 
//						echo "new head<br>";
						if ($pos_last_y) $this->y = $pos_last_y;
						$have_l = strpos(strtolower($border), "l");
						$have_r = strpos(strtolower($border), "r");	
						$have_h = strpos(strtolower($border), "h");
						$have_b = strpos(strtolower($border), "b");

						$cnt_column_head = count($this->arr_tab_head);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //�մ�
						if ($have_l !== false) {	
							$line_start_y = $this->y;		$line_end_y = $this->h - $at_end_up;
							$line_start_x = $this->lMargin;		$line_end_x = $line_start_x;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�á
						}
						for($i=0; $i<$cnt_column_head; $i++) {
							if ($this->arr_column_sel[$this->arr_column_map[$i]]==1) {
//								$line_start_x += (float)$this->arr_head_width[$i];  $line_end_x += (float)$this->arr_head_width[$i];
								$line_start_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];  $line_end_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];
								if ($have_h !== false && $i != ($cnt_column_head-1))
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�������͵ç��ҧ
								elseif ($have_r !== false && $i == ($cnt_column_head-1)) 
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�ش����
							} // end if ($this->arr_column_sel[$i]==1)
						}	// end for $i
						// ��鹹͹�Դ
						if ($have_b !== false) {
							$line_start_y = $this->h - $at_end_up;		$line_end_y = $line_start_y;
							$line_start_x = $this->lMargin;		//	$line_end_x = $line_end_x;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�á
						}
						$this->AddPage();
						$max_y = $this->y;
						if (!$nohead)
							$this->print_tab_header();
//						echo "print_tab_header $line_start_x,$line_start_y-->$line_end_x,$line_end_y<br>";
						$ret = true;
					} // end if1
//					$this->x = $start_x;		//	$this->y = $max_y;
				} // end if ($this->f_table==1) 
				
				return $ret;
			} // end function tab_pagebreak

			function print_tab_line() {
				$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //�մ�
				$sum_w = 0;
				for($i=0; $i<count($this->arr_column_map); $i++) {
						if ($this->arr_column_sel[$this->arr_column_map[$i]]==1) {
							$sum_w += (float)$this->arr_column_width[$this->arr_column_map[$i]];
						}
				}
				$line_start_y = $this->y;		$line_end_y = $this->y;
				$line_start_x = $this->lMargin;		$line_end_x = $line_start_x + $sum_w;
				$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ��鹢�ҧ�á
//				echo "this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y)<br>";
			}


			function do_aggregate($arr_data) {
					// ��˹� agregate function
//					echo "data>".implode(",",$arr_data).", func>".implode(",",$this->arr_func_aggreg).", sel>".implode(",",$this->arr_column_sel).", map>".implode(",",$this->arr_column_map)."<br>";
					$arr_column_aggreg = (array) null;
					
					$ret = $this->do_aggregate_argu($arr_data, $this->arr_func_aggreg, $this->arr_column_sel, $this->arr_column_map);
//					echo "agregate=$ret<br>";
					
					return $ret;
			} // end function

			function do_aggregate_argu($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map) {
					// ��˹� agregate function with parameter for calling outsite open_tab


					$arr_column_aggreg = (array) null;
//					echo "1..data>".implode(",",$arr_data)."<br>";

					$arr_grp = (array) null;
					$arr_text = (array) null;
					$arr_ff = (array) null;
					$arr_sum = (array) null;
					
					/**************************************************************************/
					/**** �¡ ���ʡ���� �����ŵ���Ţ �����ŵ���ѡ�� �͡�ҡ�ѹ ŧ� array ****/
					/**************************************************************************/
					for($i=0; $i < count($arr_data); $i++) {
						$arr_ff[] = "";
						$arr_sum[] = 0;
						$chk_merge = strpos($arr_data[$i],"<**");
						if ($chk_merge!==false) {
							$c = strpos($arr_data[$i],"**>");
							if ($c!==false) {
								$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
								$buff = str_replace($mgrp,"",$arr_data[$i]);
//								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//								else { $arr_data[$i] = "$buff"; $arr_grp[] = $mgrp; }
//								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; $arr_text[] = ""; }
								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp;  $arr_text[] = $buff; }
							} else {
								$arr_grp[] = ""; $arr_text[] = "";
							}
						} else {	// no group
							$arr_grp[] = ""; $arr_text[] = "";
						}
					}
					/**************************************************************************/
					/**** �¡ ���ʡ���� �����ŵ���Ţ �����ŵ���ѡ�� �͡�ҡ�ѹ ŧ� array ****/
					/***************************** End Block *********************************/
//					echo "0..data>".implode(",",$arr_data)." | ".implode(",",$arr_grp)." | ".implode(",",$arr_text)."<br>";
					$arr_column_aggreg = (array) null;
//					echo ">map>".implode(",",$arr_column_map)."<br>";

					if (count($arr_func_aggreg) > 0) {
						/**************************************************************************/
						/****** compute argregate function $arr_data is numeric only *********/
						/**************************************************************************/
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = �ʴ� column ���
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"SUM-");
									if ($c !== false) {
										$arr_ff[$kk] = "S";
										$buff = substr($a_sub_func[$ii],4);
										$a = explode("-",$buff);
										$sum = 0;
										$havedata = false;
										$pgrp = "";
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = �ʴ� column ��� �������͡��ҷ� sum �ҡ link �鹩�Ѻ ����ͧ map ��� map �мԴ
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
//												echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
												if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
													$sum += (float)$ndata;
													$pgrp = $arr_grp[$a[$kkk]];
												}
											}
										}
										if ($havedata)	$lastval = (string)$sum;	else		$lastval = chr(96);	 // chr(96) ᷹��ͧ��ҧ
//										if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"PERC");
										if ($c !== false) {
											$arr_ff[$kk] = "P";
											$c1=strpos($a_sub_func[$ii],"-");
											if ($c1==4) $forcol=-1;
											else $forcol=substr($a_sub_func[$ii],4,$c1-4);
											$buff = substr($a_sub_func[$ii],$c1+1);
											$a = explode("-",$buff);
											$thisdata = -1;
											$sum = 0;
											$cnt = 0;
											$havedata = false;
											$pgrp = "";
											for($kkk=0; $kkk < count($a); $kkk++) {
												if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = �ʴ� column ��� �������͡��ҷ� sum �ҡ link �鹩�Ѻ ����ͧ map ��� map �мԴ
													$ndata = trim($arr_data[$a[$kkk]]);
													if ($ndata!="") $havedata = true;
//													echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
													if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
													if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
														$sum += (float)$ndata;
														$pgrp = $arr_grp[$a[$kkk]];
													}
													$cnt++;
												}
											}
											if ($havedata)	
												if ($sum > 0) {
													if ($thisdata == -1) $thisdata = $sum;	// �óշ�� column ����˹� function ��� �������Ҫԡ㹡������������� ��ж������� column ��ػ���
													$lastval = (string)($thisdata / $sum * 100);	// ���� percent
	//												echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
												} else $lastval="0";
											else $lastval = chr(96);	 // chr(96) ᷹��ͧ��ҧ
										} else {
											$c = strpos($a_sub_func[$ii],"AVG-");
											if ($c !== false) {
												$buff = substr($a_sub_func[$ii],4);
												$arr_ff[$kk] = "A";
												$a = explode("-",$buff);
												$thisdata = 0;
												$sum = 0;
												$cnt = 0;
												$havedata = false;
												$pgrp = "";
												for($kkk=0; $kkk < count($a); $kkk++) {
													if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = �ʴ� column ���
														$ndata = trim($arr_data[$a[$kkk]]);
														if ($ndata!="") $havedata = true;
														if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
															$sum += (float)$ndata;
															$pgrp = $arr_grp[$a[$kkk]];
														}
														$cnt++;
													}
												}
												if ($havedata)
													$lastval = (string)($sum / $cnt);	// ���� ��������
												else 	$lastval = chr(96);	 // chr(96) ᷹��ͧ��ҧ
//												echo "function aggregate avg-".($sum / $cnt)."<br>";
											} else {
												$c = strpos($a_sub_func[$ii],"FORM");
												if ($c !== false) {
													$buff = substr($a_sub_func[$ii],4);
													$arr_ff[$kk] = "F";
//													echo "buff=$buff, len=".strlen($buff)."<br>";
													preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//													echo "data=".implode(",",$arr_data)."<br>";
													$para = (array) null;
													foreach($matches[0] as $key1 => $val1) {
//														echo "......key1=$key1 => ".$matches[0][$key1][0]."";
														if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
														else if ((int)$matches[0][$key1][1] > 0) $str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//														echo ">>".$matches[0][$key1][1]."=>$str<br>";
														if (strpos($str,"@")!==false) { 
//															if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//															else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
															if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
															else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//															echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
															$para[str_replace("@","",$str)] = $ndata;
														}
														if (((int)$matches[0][$key1+1][1] == 0)) {
															$str = substr($buff, $matches[0][$key1][1]+1);
//															echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
															if (strpos($str,"@")!==false) { 
//																if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//																else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
																if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
																else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//																echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
																$para[str_replace("@","",$str)] = $ndata;
															}
														}
													}
//													echo "buff=$buff<br>";
//													print_r($para);
													$val = compute_formula($buff, $para);
//													echo "<br>compute=$val<br>";
													$lastval = (string)$val;	// ���� ��������
//													echo "function aggregate formula-".($sum / $cnt)."<br>";
												} else {	// �������դ������
													if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
													else $ndata = (string)$arr_data[$arr_column_map[$kk]];
													$lastval = $ndata;
		//											echo "no function-".$lastval.", ndata=$ndata<br>";
												} // end if FORMULA
											} // end if AVG
										} // end if PERC
									} // end if SUM
//									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
								} // end for loop $ii
							} else { // else if ($arr_column_sel[$iii]==1)
								$arr_column_aggreg[$arr_column_map[$kk]] = "";
							} // end if ($arr_column_sel[$iii]==1)
						} // end loop for $kk
						/**************************************************************************/
						/****** compute argregate function $arr_data is numeric only *********/
						/****************************** End Block ********************************/

//						echo "1..aggreg=".implode(",",$arr_column_aggreg)." | func=".implode(",",$arr_func_aggreg)." | grp=".implode(",",$arr_grp)." | text=".implode(",",$arr_text)." | ff=".implode(",",$arr_ff)."<br>";
						
						/**************************************************************************/
						/****** compute argregate function ����Ѻ��� merge ��ҵ���Ţ *********/
						/**************************************************************************/
						if (count($arr_column_aggreg) > 0) {
							$mergesum = 0; $grp = "";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
								if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
									if ($arr_grp[$i] != $grp) {
//										echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
										if ($mergesum)
											$arr_sum[$last_i] = $mergesum;
										$grp = $arr_grp[$i];
										$mergesum = $arr_column_aggreg[$i];
										$last_i = $i;
									}  else {
										$mergesum += $arr_column_aggreg[$i];
//										echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									}
								} else {
//									echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									if ($mergesum) {
										$arr_sum[$last_i] = $mergesum;
										$last_i = -1;
									}
									$mergesum = 0;
									$grp = "";
								}
							} // end loop for
//							echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum)
								$arr_sum[$last_i] = $mergesum;
								
							$grp = ""; $ss = 0;
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
								if ($arr_sum[$i])
									if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
										$ss = $arr_sum[$i];
										$arr_column_aggreg[$i] = $ss;
										$grp = $arr_grp[$i];	
//										echo "$i .1. set ss=$ss<br>";
									} else {
										if ($arr_grp[$i]) {
											$arr_column_aggreg[$i] = $ss;
//											echo "$i .2. loop ss=$ss<br>";										
										}
									}
//								echo "5..$i..".$arr_column_aggreg[$i].".. arr_text..".$arr_text[$i]."<br>";
								if ($arr_column_aggreg[$i]=="" || ($arr_column_aggreg[$i]==chr(96) && $arr_text[$i])) $arr_column_aggreg[$i] = $arr_text[$i];
//								echo "6..$i..".$arr_column_aggreg[$i].".. arr_text..".$arr_text[$i]."<br>";
							} // end loop for
						}
						/**************************************************************************/
						/****** compute argregate function ����Ѻ��� merge ��ҵ���Ţ *********/
						/****************************** End Block ********************************/

//						echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

						/***************************************************************************/
						/******************* �ͺ�ͧ�ŧ format  TNUM , ENUM *****************/
						/***************************************************************************/
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = �ʴ� column ���
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"TNUM");
									if ($c !== false) {
										$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//										echo "TNUM - $ii - ".$a_sub_func[$ii]." , [$ndata]<br>";
										if ($ndata==chr(96)) {	 // chr(96) ᷹��ͧ��ҧ
											$lastval = " ";
//											echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
										} else {
//											echo "TNUM $ndata<br>";
											$zero = (string)substr($a_sub_func[$ii], 4, 1);
//											echo "zero=$zero, TNUM $ndata<br>";
											if ($zero=="0")  $c1=$c+6;
											else {
//												if (!$ndata) echo "null data [".trim($ndata)."] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
//												if ((is_numeric($ndata) && (float)$ndata==0) || (!$ndata)) $ndata="-";
												if ((is_numeric($ndata) && (float)$ndata==0)) $ndata="-";
//												else if ($ndata=="")
												$c1=$c+5;
											}
											$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//											echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
											if ($nocomma) 
												$dig = substr($a_sub_func[$ii], $c1+1);
											else
												$dig = substr($a_sub_func[$ii], $c1);
											if (!is_numeric($ndata))  {
												if (!$ndata && $zero=="0") $ndata = 0;
//												echo "1..ndata=$ndata<br>";
												$lastval = convert2thaidigit($ndata);
											} else if ((int)$dig > 0) {
//												echo "2..ndata=$ndata<br>";
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$dig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$dig));
											} else {
//												echo "3..ndata=$ndata<br>";
												if ($dig == "z") {
													$numd = explode(".",$ndata);
													$digstr = $numd[1];
													$ndig = 0;
													for($i=strlen($digstr)-1; $i >= 0; $i--) {
														if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
													}
//													echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
													if ($ndig == 0) 
														if ($nocomma)
															$lastval = convert2thaidigit($ndata);
														else
															$lastval = convert2thaidigit(number_format($ndata));
													else
														if ($nocomma)
															$lastval = convert2thaidigit($ndata,$ndig);
														else
															$lastval = convert2thaidigit(number_format($ndata,$ndig));
												} else 
													if ($nocomma)
														$lastval = convert2thaidigit($ndata);
													else
														$lastval = convert2thaidigit(number_format($ndata));
											}
										}
										$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//										echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-lastval=".$lastval.", ndata=$ndata, dig=$dig<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"ENUM");
										if ($c !== false) {
											$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//											echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
											if ($ndata==chr(96)) {  // chr(96) ᷹��ͧ��ҧ
												$lastval = " ";
											} else {
												$zero = (string)substr($a_sub_func[$ii], 4, 1);
												if ($zero=="0")  $c1=$c+6;
												else {
//													if ((is_numeric($ndata) && (float)$ndata==0) || (!$ndata)) $ndata="-";
													if ((is_numeric($ndata) && (float)$ndata==0)) $ndata="-";
													$c1=$c+5;
												}
												$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//												echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
												if ($nocomma) 
													$dig = substr($a_sub_func[$ii], $c1+1);
												else
													$dig = substr($a_sub_func[$ii], $c1);
												if (!is_numeric($ndata))  {
													if (!$ndata && $zero=="0") $ndata = 0;
													$lastval = $ndata;
												} else if ((int)$dig > 0) {
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$dig);
												} else {
													if ($dig == "z") {
														$numd = explode(".",$ndata);
														$digstr = $numd[1];
														$ndig = 0;
														for($i=strlen($digstr)-1; $i >= 0; $i--) {
															if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
														}
//														echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
														if ($ndig == 0) 
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata);
														else
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata,$ndig);
													} else 
														if ($nocomma)
															$lastval = $ndata;
														else
															$lastval = number_format($ndata);
												}
											}
											$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//											echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//											echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-lastval=".$lastval.", ndata=$ndata, dig=$dig<br>";
										} // end if ENUM
									} // end if TNUM
								} // end for loop $ii
							} // end if ($this->arr_column_sel[$iii]==1)
						} // end loop for $kk
						/***************************************************************************/
						/******************* �ͺ�ͧ�ŧ format  TNUM , ENUM *****************/
						/******************************* End Block ********************************/

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					
					} // end if if (count($arr_func_aggreg) > 0)

					/***************************************************************************/
					/************ �ӡ�� Merge column ��� Write �͡ WorkSheet ************/
					/***************************************************************************/
					$cntcolumn = count($arr_column_aggreg);
					
					if ($cntcolumn > 0) {
						$cntshow = 0;
						for($i=0; $i < $cntcolumn; $i++) {
							if ($arr_column_aggreg[$i])
								$arr_column_aggreg[$i] = $arr_grp[$i].str_replace("*Enter*","",$arr_column_aggreg[$i]);
							else $arr_column_aggreg[$i] = $arr_grp[$i];
							if ($arr_column_sel[$arr_column_map[$i]]==1) $cntshow++;
						}
					}
					// ����á�˹� agregate function
					
					return (count($arr_column_aggreg) > 0 ? implode("|", $arr_column_aggreg) : "");
			} // end function

			function do_aggregate_argu_old($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map) {
					// ��˹� agregate function with parameter for calling outsite open_tab
//					echo "data>".implode(",",$arr_data)."<br>";
					$arr_grp = (array) null;
					$arr_ff = (array) null;
					$arr_sum = (array) null;
					for($i=0; $i < count($arr_data); $i++) {
						$arr_ff[] = "";
						$arr_sum[] = 0;
						$chk_merge = strpos($arr_data[$i],"<**");
						if ($chk_merge!==false) {
							$c = strpos($arr_data[$i],"**>");
							if ($c!==false) {
								$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
								$buff = str_replace($mgrp,"",$arr_data[$i]);
								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
							} else {
								$arr_grp[] = "";
							}
						} else {	// no group
							$arr_grp[] = "";
						}
					}
//					echo "data>".implode(",",$arr_data)."<br>";
					$arr_column_aggreg = (array) null;
//					echo ">map>".implode(",",$arr_column_map)."<br>";
					if (count($arr_func_aggreg) > 0) {
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = �ʴ� column ���
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"SUM-");
									if ($c !== false) {
										$arr_ff[$kk] = "S";
										$buff = substr($a_sub_func[$ii],4);
										$a = explode("-",$buff);
										$sum = 0;
										$havedata = false;
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = �ʴ� column ��� �������͡��ҷ� sum �ҡ link �鹩�Ѻ ����ͧ map ��� map �мԴ
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
//												echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
												$sum += (float)$ndata;
											}
										}
										if ($havedata)	$lastval = (string)$sum;	else		$lastval = chr(96);	 // chr(96) ᷹��ͧ��ҧ
//										if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"PERC");
										if ($c !== false) {
											$arr_ff[$kk] = "P";
											$c1=strpos($a_sub_func[$ii],"-");
											if ($c1==4) $forcol=-1;
											else $forcol=substr($a_sub_func[$ii],4,$c1-4);
											$buff = substr($a_sub_func[$ii],$c1+1);
											$a = explode("-",$buff);
											$thisdata = -1;
											$sum = 0;
											$cnt = 0;
											$havedata = false;
											for($kkk=0; $kkk < count($a); $kkk++) {
												if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = �ʴ� column ��� �������͡��ҷ� sum �ҡ link �鹩�Ѻ ����ͧ map ��� map �мԴ
													$ndata = trim($arr_data[$a[$kkk]]);
													if ($ndata!="") $havedata = true;
//													echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
													if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
													$sum += (float)$ndata;
													$cnt++;
												}
											}
											if ($havedata)	
												if ($sum > 0) {
													if ($thisdata == -1) $thisdata = $sum;	// �óշ�� column ����˹� function ��� �������Ҫԡ㹡������������� ��ж������� column ��ػ���
													$lastval = (string)($thisdata / $sum * 100);	// ���� percent
	//												echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
												} else $lastval="0";
											else $lastval = chr(96);	 // chr(96) ᷹��ͧ��ҧ
										} else {
											$c = strpos($a_sub_func[$ii],"AVG-");
											if ($c !== false) {
												$buff = substr($a_sub_func[$ii],4);
												$arr_ff[$kk] = "A";
												$a = explode("-",$buff);
												$thisdata = 0;
												$sum = 0;
												$cnt = 0;
												$havedata = false;
												for($kkk=0; $kkk < count($a); $kkk++) {
													if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = �ʴ� column ���
														$ndata = trim($arr_data[$a[$kkk]]);
														if ($ndata!="") $havedata = true;
														$sum += (float)$ndata;
														$cnt++;
													}
												}
												if ($havedata)
													$lastval = (string)($sum / $cnt);	// ���� ��������
												else 	$lastval = chr(96);	 // chr(96) ᷹��ͧ��ҧ
//												echo "function aggregate avg-".($sum / $cnt)."<br>";
											} else {
												$c = strpos($a_sub_func[$ii],"FORM");
												if ($c !== false) {
													$buff = substr($a_sub_func[$ii],4);
													$arr_ff[$kk] = "F";
//													echo "buff=$buff, len=".strlen($buff)."<br>";
													preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//													echo "data=".implode(",",$arr_data)."<br>";
													$para = (array) null;
													foreach($matches[0] as $key1 => $val1) {
//														echo "......key1=$key1 => ".$matches[0][$key1][0]."";
														if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
														else if ((int)$matches[0][$key1][1] > 0) $str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//														echo ">>".$matches[0][$key1][1]."=>$str<br>";
														if (strpos($str,"@")!==false) { 
//															if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//															else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
															if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
															else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//															echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
															$para[$str] = $ndata;
														}
														if (((int)$matches[0][$key1+1][1] == 0)) {
															$str = substr($buff, $matches[0][$key1][1]+1);
//															echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
															if (strpos($str,"@")!==false) { 
//																if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//																else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
																if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
																else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//																echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
																$para[$str] = $ndata;
															}
														}
													}
//													print_r($para);
													$val = compute_formula($buff, $para);
//													echo "<br>compute=$val<br>";
													$lastval = (string)$val;	// ���� ��������
//													echo "function aggregate formula-".($sum / $cnt)."<br>";
												} else {	// �������դ������
													if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
													else $ndata = (string)$arr_data[$arr_column_map[$kk]];
													$lastval = $ndata;
		//											echo "no function-".$lastval.", ndata=$ndata<br>";
												} // end if FORMULA
											} // end if AVG
										} // end if PERC
									} // end if SUM
//									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
								} // end for loop $ii
							} else { // else if ($arr_column_sel[$iii]==1)
								$arr_column_aggreg[$arr_column_map[$kk]] = "";
							} // end if ($arr_column_sel[$iii]==1)
						} // end loop for $kk

//						echo "1..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , grp=".implode(",",$arr_grp)." , ff=".implode(",",$arr_ff)."<br>";
						if (count($arr_column_aggreg) > 0) {
							$mergesum = 0; $grp = "";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
								if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
									if ($arr_grp[$i] != $grp) {
//										echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
										if ($mergesum)
											$arr_sum[$last_i] = $mergesum;
										$grp = $arr_grp[$i];
										$mergesum = $arr_column_aggreg[$i];
										$last_i = $i;
									}  else {
										$mergesum += $arr_column_aggreg[$i];
//										echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									}
								} else {
//									echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									if ($mergesum) {
										$arr_sum[$last_i] = $mergesum;
										$last_i = -1;
									}
									$mergesum = 0;
									$grp = "";
								}
							} // end loop for
//							echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum)
								$arr_sum[$last_i] = $mergesum;
								
							$grp = ""; $ss = 0;
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
								if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
									$ss = $arr_sum[$i];
									$arr_column_aggreg[$i] = $ss;
									$grp = $arr_grp[$i];									
//									echo "$i .1. set ss=$ss<br>";
								} else {
									if ($arr_grp[$i]) {
										$arr_column_aggreg[$i] = $ss;
//										echo "$i .2. loop ss=$ss<br>";										
									}
								}
							} // end loop for
						}
//						echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

						// �ͺ�ͧ�ŧ format  TNUM , ENUM
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = �ʴ� column ���
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"TNUM");
									if ($c !== false) {
										$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//										echo "TNUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
										if ($ndata==chr(96)) {	 // chr(96) ᷹��ͧ��ҧ
											$lastval = " ";
//											echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
										} else {
//											echo "TNUM $ndata<br>";
											$zero = (string)substr($a_sub_func[$ii], 4, 1);
//											echo "zero=$zero TNUM $ndata<br>";
											if ($zero=="0")  $c1=$c+6;
											else {
//												if (!$ndata) echo "null data [$ndata] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
												if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
//												else if ($ndata=="")
												$c1=$c+5;
											}
											$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//											echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
											if ($nocomma) 
												$dig = substr($a_sub_func[$ii], $c1+1);
											else
												$dig = substr($a_sub_func[$ii], $c1);
											if (!is_numeric($ndata)) 
												$lastval = convert2thaidigit($ndata);
											else if ((int)$dig > 0)
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$dig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$dig));
											else {
												if ($dig == "z") {
													$numd = explode(".",$ndata);
													$digstr = $numd[1];
													$ndig = 0;
													for($i=strlen($digstr)-1; $i >= 0; $i--) {
														if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
													}
//													echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
													if ($ndig == 0) 
														if ($nocomma)
															$lastval = convert2thaidigit($ndata);
														else
															$lastval = convert2thaidigit(number_format($ndata));
													else
														if ($nocomma)
															$lastval = convert2thaidigit($ndata,$ndig);
														else
															$lastval = convert2thaidigit(number_format($ndata,$ndig));
												} else 
													if ($nocomma)
														$lastval = convert2thaidigit($ndata);
													else
														$lastval = convert2thaidigit(number_format($ndata));
											}
										}
										$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//										echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"ENUM");
										if ($c !== false) {
											$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//											echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
											if ($ndata==chr(96)) {  // chr(96) ᷹��ͧ��ҧ
												$lastval = " ";
											} else {
												$zero = (string)substr($a_sub_func[$ii], 4, 1);
												if ($zero=="0")  $c1=$c+6;
												else {
													if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
													$c1=$c+5;
												}
												$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//												echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
												if ($nocomma) 
													$dig = substr($a_sub_func[$ii], $c1+1);
												else
													$dig = substr($a_sub_func[$ii], $c1);
												if (!is_numeric($ndata)) 
													$lastval = $ndata;
												else if ((int)$dig > 0)
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$dig);
												else {
													if ($dig == "z") {
														$numd = explode(".",$ndata);
														$digstr = $numd[1];
														$ndig = 0;
														for($i=strlen($digstr)-1; $i >= 0; $i--) {
															if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
														}
//														echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
														if ($ndig == 0) 
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata);
														else
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata,$ndig);
													} else 
														if ($nocomma)
															$lastval = $ndata;
														else
															$lastval = number_format($ndata);
												}
											}
											$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//											echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//											echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
										} // end if ENUM
									} // end if TNUM
								} // end for loop $ii
							} // end if ($this->arr_column_sel[$iii]==1)
						} // end loop for $kk

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					
					} // end if if (count($arr_func_aggreg) > 0)

					if (count($arr_column_aggreg) > 0) {
						for($i=0; $i < count($arr_column_aggreg); $i++) {
							if ($arr_column_aggreg[$i])
								$arr_column_aggreg[$i] = $arr_grp[$i].str_replace("*Enter*","",$arr_column_aggreg[$i]);
							else $arr_column_aggreg[$i] = $arr_grp[$i];
						}
					}
//					echo "4..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					// ����á�˹� agregate function
					return (count($arr_column_aggreg) > 0 ? implode("|", $arr_column_aggreg) : "");
			} // end function

			function add_lineFreeTab($line_h, $arr_data, $arr_align, $arr_border, $arr_font, $arr_width, $arr_func_aggreg) {
				// parameter
				//		$line_h				: �����٧�ͧ��÷Ѵ
				//		$arr_data			: array �ͧ��ͤ������о����
				//		$arr_align			: array �ͧ��èѴ ��ͤ���  L �Դ����  C �ҧ��ҧ  R �Դ���
				//		$arr_border		: array �ͧ�ͺ T �ͺ�� L �ͺ���� B �ͺ��ҧ R �ͺ���
				//		$arr_font			: array �ͧ�ٻẺ����ѡ��  ���� font|b=˹� U=�����|font size|XXXXXX=�յ���ѡ��|ZZZZZZ=�վ��
				//		$arr_width			: array �ͧ�������ҧ�ͧ cell �������ը� gen ����ѵ��ѵ� �ҡ�������ҧ������ 
				//									  ��ҵ�ͧ������ �ͧ cell ����͡ѹ �����Ҵ������Ǣͧ text ����˹���Ҥ������ҧ���������á ���ǡ�˹��������ҧ ��ǶѴ�Ҵ��� '-'
				//		$arr_func_aggreg 	: array �ͧ function aggregate ��� ��˹� ��Դ����ʴ�����Ţ TNUM ENUM �繵�
				
				global $NUMBER_DISPLAY;

				$arr_column_sel = (array) null;
				$arr_column_map = (array) null;
				if (!$arr_func_aggreg) {
					$arr_func_aggreg = (array) null;
					for($i=0; $i < count($arr_data); $i++) { 
						$arr_column_sel[] = 1; $arr_column_map[] = $i; 
						$arr_func_aggreg[] = ($NUMBER_DISPLAY==2)?"TNUM":"ENUM";
					}
				} else {
					for($i=0; $i < count($arr_data); $i++) { 
						$arr_column_sel[] = 1; $arr_column_map[] = $i; 
					}
				}
				$buff_data = $this->do_aggregate_argu($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map);
				$arr_text = explode("|",$buff_data);

				$page_w = $this->w;
				if  ($page_w > 280 && $page_w < 300) $page_w = 290;
				else if  ($page_w > 190 && $page_w < 215) $page_w = 200;
				else $page_w = 200;
				
				$setw = 0;
				$cntw = count($arr_width);	// �ó��ա�á�˹���� �������ҧ�ͧ���� column
				if ($cntw > 0) {	
					$page_w1 = 0; $cntnone = 0;
					for($i = 0; $i < $cntw; $i++) {		// loop ������ total page width
						if ($arr_width[$i]=="-") {	// ��˹�����ա���觤������ҧ���Ѻ��Ҥ������ҧ��ǡ�͹˹�ҷ������
							$page_w1 += 0;			// ����������� ���� ��Ҥ������ҧ - ���������Ѻ w ��͹˹��
						} else if ($arr_width[$i]) {
							$page_w1 += $arr_width[$i];
						} else $cntnone++;
					}
					if ($cntnone > 0) {	// ��� �� ��Ƿ������˹���� �С�˹�����ѵ��ѵ�
						$rest = $page_w - $page_w1;
						$setw = floor($rest / $cntnone);
						for($i = 0; $i < $cntw; $i++) {		// loop ���� ������ �������ҧ������������� ������ ˹�� (�������ҧ)
							if (!$arr_width[$i])
								$arr_width[$i] = $setw;
						}
					}
					$breakW = 0;  $firstIdx = -1; $aw = 0; $breakA = ""; $sumw = 0;
//					echo "bf w-".implode(",",$arr_width)."<br>";
					for($i = 0; $i < $cntw; $i++) {		// loop ���������Ҥ������ҧ � column ���������ҧ��Ẻ�� (-) �Ѻ���˹��
//						if ($arr_width[$i]=="-" && $arr_text[$i]) {
						if ($arr_width[$i]=="-") {
							$font_set = explode("|",$arr_font[$i]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$aw = $this->GetStringWidth( $arr_text[$i] ); 
							$arr_width[$i] = $aw;
							$sumw += $aw;
//							echo "w='-'..i=$i .. (".$arr_text[$i].")..getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
							$arr_align[$i] = $breakA;	// ��Ƿ���� - ��� align �����ҡѺ����á�ش
						} else if ($arr_width[$i]) {
							if ($sumw > 0) {
								if ($breakA=="L") {	// �Դ���·�駪ش
									$font_set = explode("|",$arr_font[$firstIdx]);
									$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
									$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// �������ҧ੾�� text �ͧ����á�ش
									$sumw += $aw;	// ����������ҧ੾�� text  �ͧ��駪ش
									$rest = $arr_width[$firstIdx] - $sumw;	// �����ҧ�������� = �������ҧ��˹� �ѡ �������ҧ੾�� text
//									echo "w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$i].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
									$arr_width[$i-1] += $rest;	// �����ҧ���������������� �ش�ش���� ���ͼ�ѡ����駪ش价ҧ����
									$arr_width[$firstIdx] = $aw;	// �������ҧ��駵� �������͡��ҧ੾�� text ��駵�
								} else if ($breakA=="C") {
									$font_set = explode("|",$arr_font[$firstIdx]);
									$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
									$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// �������ҧ੾�� text �ͧ����á�ش
									$sumw += $aw;	// ����������ҧ੾�� text  �ͧ��駪ش
									$rest = $arr_width[$firstIdx] - $sumw;	// �����ҧ�������� = �������ҧ��˹� �ѡ �������ҧ੾�� text
//									echo "w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$i].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
									$arr_width[$i-1] += ($rest /2);	// ����˹�觢ͧ�����ҧ���������������� �ش�ش���� ���ͼ�ѡ����駪ش�����ҧ
									$arr_align[$i-1] = "L";	// column �ش���� ���Դ����
									$arr_width[$firstIdx] = ($rest /2)+$aw;	// �������ҧ��駵� = �ա����˹�觢ͧ�����ҧ�������� �ǡ �Ѻ�������ҧ��駵� ੾�� text ��駵� �����������ç��ҧ
									$arr_align[$firstIdx] = "R";	// column �á�ش ���Դ���
								} else {	 // Align == 'R' �Դ���
///									echo "w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$i].")..firstIdx=$firstIdx,  w_firstidx=".$arr_width[$firstIdx]." , sumw=$sumw (breakA=$breakA)<br>";
									$arr_width[$firstIdx] -= $sumw;	// �������ҧ��駵� = ��ҵ�駵��ѡ ������Ѻ��Ҥ������ҧ੾�� text �������ͷ�����
//									echo "breakA = R .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
								}
							} // end if sumw > 0
//							echo "width (".$arr_width[$i].") ..  (getw=$aw), i=$i , sumw=$sumw , breakA=$breakA<br>";
							$breakW = (float)$arr_width[$i]; // �纤�� w ��� �ͧ col �� �ͧ col ��Сͺ�������ҧ����͹
							$breakA = $arr_align[$i];
							$sumw = 0;
							$firstIdx = $i;
						}
					} // end loop for $i
					if ($sumw > 0) {
						if ($breakA=="L") {	// �Դ���·�駪ش
//							echo "Last breakA = L .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
							$font_set = explode("|",$arr_font[$firstIdx]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// �������ҧ੾�� text �ͧ����á�ش
							$sumw += $aw;	// ����������ҧ੾�� text  �ͧ��駪ش
							$rest = $arr_width[$firstIdx] - $sumw;	// �����ҧ�������� = �������ҧ��˹� �ѡ �������ҧ੾�� text
//							echo "last w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$firstIdx].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
							$arr_width[$i-1] += $rest;	// �����ҧ���������������� �ش�ش���� ���ͼ�ѡ����駪ش价ҧ����
							$arr_width[$firstIdx] = $aw;	// �������ҧ��駵� �������͡��ҧ੾�� text ��駵�
						} else if ($breakA=="C") {
//							echo "Last breakA = C .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
							$font_set = explode("|",$arr_font[$firstIdx]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// �������ҧ੾�� text �ͧ����á�ش
							$sumw += $aw;	// ����������ҧ੾�� text  �ͧ��駪ش
							$rest = $arr_width[$firstIdx] - $sumw;	// �����ҧ�������� = �������ҧ��˹� �ѡ �������ҧ੾�� text
//							echo "last w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$firstIdx].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
							$arr_width[$i-1] += ($rest /2);	// ����˹�觢ͧ�����ҧ���������������� �ش�ش���� ���ͼ�ѡ����駪ش�����ҧ
							$arr_align[$i-1] = "L";	// column �ش���� ���Դ����
							$arr_width[$firstIdx] = ($rest /2)+$aw;	// �������ҧ��駵� = �ա����˹�觢ͧ�����ҧ�������� �ǡ �Ѻ�������ҧ��駵� ੾�� text ��駵� �����������ç��ҧ
							$arr_align[$firstIdx] = "R";	// column �á�ش ���Դ���
						} else {	 // Align == 'R' �Դ���
//							echo "last w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$firstIdx].")..firstIdx=$firstIdx,  w_firstidx=".$arr_width[$firstIdx]." , sumw=$sumw (breakA=$breakA)<br>";
//							echo "Last breakA = R .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
							$arr_width[$firstIdx] -= $sumw;	// �������ҧ��駵� = ��ҵ�駵��ѡ ������Ѻ��Ҥ������ҧ੾�� text �������ͷ�����
						}
//						echo "Last width (".$arr_width[$i-1].") ..  i=".($i-1)." , sumw=$sumw , breakA=$breakA<br>";
					} // end if sumw > 0
//					echo "af w-".implode(",",$arr_width)." text=(".implode(",",$arr_text).")<br>";
				} else {	// ���������˹��������ҧ�� �ӹǳ�Ҥ�� �»���ҳ�ͧ text �ش�������դ�� ��� = ""
					$cnt = count($arr_text);
					$cnt_zero = 0; $sw = 0;
					for ($i=0; $i < $cnt; $i++) {
						if ($arr_text[$i]) {
							$font_set = explode("|",$arr_font[$i]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$sw += $this->GetStringWidth( $arr_text[$i] ); 
						} else $cnt_zero++;
					}
					$rest = $page_w - $sw;
					$setw = floor($rest / $cnt_zero);	// ��������仡�˹���Ҥ������ҧ �ó� text column � � �� ""
				}
//				echo "page_w=$page_w<br>";
				if (!$line_h) $line_h = 7;
				$cnt = count($arr_text);
//				echo "$cnt :: ".count($arr_border)."==".count($arr_font)."<br>";
//				echo "$cnt :: text=".implode(",",$arr_text)."  width=".implode(",",$arr_width)."<br>";
				if ($cnt == count($arr_border) && $cnt == count($arr_font)) {
					for ($i=0; $i < $cnt; $i++) {
//						echo "$i--text:".$arr_text[$i]." font:".$arr_font[$i]."<br>";
						$font_set = explode("|",$arr_font[$i]);
						$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
						if (!$font_set[3]) $font_set[3]="000000";
						$this->SetTextColor( hexdec(substr($font_set[3],0,2)),hexdec(substr($font_set[3],2,2)),hexdec(substr($font_set[3],4,2)) );
//						if (!$font_set[4]) $font_set[4]="FFFFFF";
						$f_fill = 0;
						if ($font_set[4] && strlen($font_set[4])==6)  {
							$this->SetFillColor( hexdec(substr($font_set[4],0,2)),hexdec(substr($font_set[4],2,2)),hexdec(substr($font_set[4],4,2)) );
//							echo "setFill_=".$font_set[4]."<br>";
							$f_fill = 1;
						}
						if ($font_set[5])  { // rotate angle
							$this->Rotate((int)$font_set[5]); 
						}
//						$astr = (($NUMBER_DISPLAY==2)?convert2thaidigit($arr_text[$i]):$arr_text[$i]);
						$astr = $arr_text[$i];
						if ($arr_width[$i]) {
							$aw = $arr_width[$i];
						} else if ($arr_text[$i]) {
							$aw = $this->GetStringWidth( $astr ); 
						} else $aw = $setw;	// 	�óշ�� text ����դ�� ��� = ""
//						$rest = $page_w - $aw;
//						echo "w=$aw  text=$astr   align=".$arr_align[$i]."<br>";
						if ($aw)	$this->Cell($aw,$line_h,$astr,$arr_border[$i],($i==($cnt-1)?1:0),$arr_align[$i], $f_fill);
					} // end for loop
					return true;
				} else {
					return false;
				}
			} // end function add_lineFreeTab
			
			function Rotate($angle,$x=-1,$y=-1) { 
				if($x==-1) 
					$x=$this->x; 
				if($y==-1) 
					$y=$this->y; 
				if($this->angle!=0) 
					$this->_out('Q'); 
				$this->angle=$angle; 
				if($angle!=0) { 
					$angle*=M_PI/180; 
					$c=cos($angle); 
					$s=sin($angle); 
					$cx=$x*$this->k; 
					$cy=($this->h-$y)*$this->k; 
             
					$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
				} 
    		}  // end function Rotate

		}
		// end class
?>