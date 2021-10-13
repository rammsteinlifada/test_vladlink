all:
	php importCategories.php
	php exportCategories.php
	xdg-open http://localhost:8000/list_menu.php
	php -S localhost:8000

create-table:
	php createTable.php
