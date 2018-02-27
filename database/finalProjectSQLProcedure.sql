	DELIMITER §
    DROP PROCEDURE IF EXISTS data_reset §

	CREATE PROCEDURE data_reset()
	BEGIN
	  -- Disable foreign key constraint checks
	  SET FOREIGN_KEY_CHECKS = 0;
	  -- Empty tables and set their auto-incrément to 1
	  TRUNCATE TABLE member;
	  TRUNCATE TABLE project;
	  TRUNCATE TABLE project_member;
	  TRUNCATE TABLE project_role;
	  TRUNCATE TABLE feature;
	  TRUNCATE TABLE task;
	  TRUNCATE TABLE task_status;
	  TRUNCATE TABLE iteration;
	  TRUNCATE TABLE acceptance_test;
	  TRUNCATE TABLE acceptance_test_status;
	  TRUNCATE TABLE user_role;
	  -- Enable again foreign key constraint checks
	  SET FOREIGN_KEY_CHECKS = 1;

	  BEGIN
		-- Catch clause
		DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
		  -- Rollback the transaction
		  ROLLBACK;
				-- Display the cause
		  SHOW ERRORS;
		END;  
		START TRANSACTION;
		INSERT INTO member (email, password, first_name, last_name) VALUES
		('haddock@moulinsart.be', 'capitaine', 'Haddock', 'Archibald'),
		('bianca.castafiore@scala.it', 'ahjeris', 'Castafiore', 'Bianca'),
		('tournesol@moulinsart.be', 'unpeuplus', 'Tournesol', 'Tryphon'),
		('lampion@mondass.fr', 'Signez', 'Lampion', 'Séraphin'),
		('nestor@moulinsart.be', 'LochLomond', 'Tintin', 'Nestor');

		INSERT INTO project(id,name,description,administrator_id,created_at) VALUES
		(1,'Auction', 'This is the auction project',2,'2017-11-19 00:00:00'),
		(2,'Le temple du luna', 'This is the 2nd test project',1,'2017-12-18 00:00:00');
		
		INSERT INTO project_role(id,name) VALUES
		(1, 'Owner'),
		(2, 'Coach'),
		(3, 'Developer'),
		(4, 'Tester');

		INSERT INTO project_member(member_id, project_id, role_id, added_at) VALUES
		(1,1,1,'2017-11-19 00:00:00'),
		(2,1,2,'2017-11-20 00:00:00'),
		(3,1,3,'2017-11-21 00:00:00'),
		(4,1,3,'2017-11-22 00:00:00'),
		(5,1,4,'2017-11-23 00:00:00'),
		(1,2,2,'2017-11-24 00:00:00'),
		(2,2,3,'2017-11-25 00:00:00'),
		(3,2,3,'2017-11-25 18:00:00'),
		(4,2,4,'2017-11-26 00:00:00'),
		(5,2,1,'2017-11-27 23:00:00');
		
		INSERT INTO iteration(id,deadline,project_id) VALUES
		(1,'2018-01-12',1),
		(2,'2018-01-20',1),
		(3,'2017-06-16',2),
		(4,'2017-06-23',2);
		
		INSERT INTO user_role(id,name,description) VALUES
		(1,'visitor auction','visitor for the auction'),
		(2,'registered user','registered user for the auction'),
		(3,'visitor video','visitor for the video'),
		(4,'subscriber','subscriber for the video');

		INSERT INTO feature(id,title,functionality,benefit,priority,iteration_id,project_id,user_role_id) VALUES
		(1,'Find products','Find on the homepage of the site the products on which the last auctions took place','users are aware of the current activity',5,null,1,1),
		(2,'Search products','Search products from any page of the site',' placing a bid is the most immediate possible',4,2,1,1),
		(3,'View products','View the page of each product','users have the maximum of information',1,1,1,1),
		(4,'Log in','users can log in any page','it is easy for user to access',2,1,1,2),
		(5,'Bid','users bid on the product viewed','this is the purpose of the site',3,1,1,2);
        
        INSERT INTO task_status(id,name) VALUES 
        (1,'to do'),
        (2,'ongoing'),
        (3,'done'),
        (4,'blocking');
        
        INSERT INTO task(id,title,feature_id,status_id) VALUES
        (1,'Create the HTML',3,3),
        (2,'Connect with products database',3,3),
        (3,'Create the PHP code for log n',4,3),
        (4,'Connect PHP log in code with HTML',4,3),
        (5,'Create the PHP code for playing bid',5,3),
        (6,'Create a HTML page for playing bid',5,3),
        (7,'Connect PHP code with HTML and database for playing bid',5,3),
        (8,'Create PHP code to select the products on which the last auctions took place ',1,1),
        (9,'Create HTML page for find products',1,1);
        
        INSERT INTO acceptance_test(id,description,test_result,feature_id) VALUES
        (1,'test viewing product details','done',3),
        (2,'test view product details again','done',3),
        (3,'test log in with correct user name and password','done',4),
        (4,'test log in with wrong user name and password','done',4),
        (5,'test going to bid page on a viewed product','done',5),
        (6,'test playing correct bid','done',5),
        (7,'test playing wrong bid','error',5),
        (8,'re-test playing wrong bid','done',5),
        (9,'try to search for products which exist','done',2),
        (10,'try to search for products which don\'t exist','done',2),
        (11,'try to find the products which belong the last auction','on-going',1),
        (12,'try to find the products which don\'t belong to the last auction','on-going',1)
        ;
        
        INSERT INTO acceptance_test_status(iteration_id,acceptance_test_id,is_satisfied) VALUES
        (1,1,true),
        (1,2,true),
        (1,3,true),
        (1,4,true),
        (1,5,true),
        (1,6,true),
        (1,7,false),
        (2,8,true),
        (2,9,true),
        (2,10,true);
        
		COMMIT;
	  END;
	END §

	CALL data_reset();