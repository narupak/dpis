<?php 

	function create_paging($allpage,$page,$search_ldap,$baselink) 
	{
		
		if($page != 1) 
			$first = '<a href="'.$baselink.'?search_ldap='.$search_ldap.'&page=1">First</a>&nbsp;'; 
		if($page != 1) 
			$prev = '<a href="'.$baselink.'?search_ldap='.$search_ldap.'&page='.($page-1).'">Prev</a>&nbsp;'; 
		if($page != $allpage) 
			$last = '<a href="'.$baselink.'?search_ldap='.$search_ldap.'&page='.$allpage.'">Last</a>'; 
		if($page != $allpage) 
			$next = '<a href="'.$baselink.'?search_ldap='.$search_ldap.'&page='.($allpage-1).'">Next</a>&nbsp;'; 

		for($i=1; $i<=$allpage; $i++) 
			$linkpage .= '<a href="'.$baselink.'?search_ldap='.$search_ldap.'&page='.$i.'">'.$i.'</a>&nbsp;'; 
		
		return "<div id='paging'>$first $prev $linkpage $next $last</div>";
	}

?>