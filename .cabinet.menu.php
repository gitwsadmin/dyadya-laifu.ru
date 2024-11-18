<?
$aMenuLinks = Array(
	Array(
		"Мои данные", 
		"/cabinet/index.php", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Подписки", 
		"/cabinet/subscribe/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Материалы", 
		"/cabinet/news/", 
		Array(), 
		Array(), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Написать директору", 
		"/cabinet/form/", 
		Array(), 
		Array(), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Выйти", 
		"?logout=yes&login=yes", 
		Array(), 
		Array("class"=>"exit", "BLOCK"=>'<svg width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.5 4.5H10.5M10.5 4.5L8.5 2.5M10.5 4.5L8.5 6.5" stroke="#333333" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 0.5H2.5C1.39543 0.5 0.5 1.39543 0.5 2.5V6.5C0.5 7.60457 1.39543 8.5 2.5 8.5H5.5" stroke="#333333" stroke-linecap="round" stroke-linejoin="round"/></svg>'), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	)
);
?>