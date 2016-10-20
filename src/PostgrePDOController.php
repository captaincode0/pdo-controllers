<?php
	/*
		                                 __                __                                      __           
		                                /  |              /  |                                    /  |          
		  _______   ______    ______   _$$ |_     ______  $$/  _______    _______   ______    ____$$ |  ______  
		 /       | /      \  /      \ / $$   |   /      \ /  |/       \  /       | /      \  /    $$ | /      \ 
		/$$$$$$$/  $$$$$$  |/$$$$$$  |$$$$$$/    $$$$$$  |$$ |$$$$$$$  |/$$$$$$$/ /$$$$$$  |/$$$$$$$ |/$$$$$$  |
		$$ |       /    $$ |$$ |  $$ |  $$ | __  /    $$ |$$ |$$ |  $$ |$$ |      $$ |  $$ |$$ |  $$ |$$    $$ |
		$$ \_____ /$$$$$$$ |$$ |__$$ |  $$ |/  |/$$$$$$$ |$$ |$$ |  $$ |$$ \_____ $$ \__$$ |$$ \__$$ |$$$$$$$$/ 
		$$       |$$    $$ |$$    $$/   $$  $$/ $$    $$ |$$ |$$ |  $$ |$$       |$$    $$/ $$    $$ |$$       |
		 $$$$$$$/  $$$$$$$/ $$$$$$$/     $$$$/   $$$$$$$/ $$/ $$/   $$/  $$$$$$$/  $$$$$$/   $$$$$$$/  $$$$$$$/ 
		                    $$ |                                                                                
		                    $$ |                                                                                
		                    $$/                                                                                 
	 */
	class PostgrePDOController extends PDOController{
		/**
		 * [__construct invokes the parent and pass the kind of connection with postgre sql]
		 * @param [array] $config [array of congigutions for PDO Controllers]
		 */
		public function __construct($config){
			parent::__construct("pgsql", $config);
		}
	} 
?>