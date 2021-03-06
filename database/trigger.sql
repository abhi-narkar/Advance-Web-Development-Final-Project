delimiter $$
-- 2
drop trigger if exists project_before_insert$$
create trigger project_before_insert
before insert on project
for each row
begin
	set new.name := trim(new.name);
    if new.name = '' then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Empty name', MYSQL_ERRNO=3001;
	end if;
end$$

-- 2
drop trigger if exists project_before_update$$
create trigger project_before_update
before update on project
for each row
begin
	set new.name := trim(new.name);
    if new.name = '' then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Empty name', MYSQL_ERRNO=3001;
	end if;
end$$

-- 1
drop trigger if exists member_before_insert$$
create trigger member_before_insert
before  insert on member
for each row
begin
  set  new.first_name = trim(CONCAT(UPPER(LEFT(new.first_name, 1)), SUBSTRING(new.first_name, 2)));
  set  new.last_name = trim(CONCAT(UPPER(LEFT(new.last_name, 1)), SUBSTRING(new.last_name, 2)));
  set  new.email = trim(LCASE(new.email));
  
 if new.first_name  = '' then
   SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='first name is mandotary', MYSQL_ERRNO=3001;
   end if;
end$$


-- 1
drop trigger if exists member_before_update$$
create trigger member_before_update
before  update on member
for each row
begin
  set  new.first_name = trim(CONCAT(UPPER(LEFT(new.first_name, 1)), SUBSTRING(new.first_name, 2)));
  set  new.last_name = trim(CONCAT(UPPER(LEFT(new.last_name, 1)), SUBSTRING(new.last_name, 2)));
  set  new.email = trim(LCASE(new.email));
  
 if new.first_name  = '' then
   SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='first name is mandotary', MYSQL_ERRNO=3001;
   end if;
end$$





-- 3
drop trigger if exists project_member_before_insert$$
create trigger project_member_before_insert
before insert on project_member
for each row
begin
	declare v_owner_nb INT;
    declare v_coach_nb INT;
	if new.role_id = 1 then -- project owner
		select count(*) into v_owner_nb
        from project_member
        where project_id = new.project_id and role_id = new.role_id;
        if v_owner_nb <> 0 then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT=' more than one product owner', MYSQL_ERRNO=3010;
        end if;
	end if;
    if new.role_id = 2 then -- project coach
		select count(*) into v_coach_nb
        from project_member
        where project_id = new.project_id and role_id = new.role_id;
        if v_coach_nb <> 0 then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='more than one coach', MYSQL_ERRNO=3011;
        end if;
	end if;
end$$     
 
-- 3b
drop trigger if exists project_member_before_update$$
create trigger project_member_before_update
before update on project_member
for each row
begin
	declare v_owner_nb INT;
    declare v_coach_nb INT;
	if new.role_id = 1 then -- project owner
		select count(*) into v_owner_nb
        from project_member
        where project_id = new.project_id and role_id = new.role_id;
        if v_owner_nb <> 0 then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT=' more than one product owner', MYSQL_ERRNO=3010;
        end if;
	end if;
    if new.role_id = 2 then -- project coach
		select count(*) into v_coach_nb
        from project_member
        where project_id = new.project_id and role_id = new.role_id;
        if v_coach_nb <> 0 then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='more than one coach', MYSQL_ERRNO=3011;
        end if;
	end if;
end$$  

-- 6
drop trigger if exists task_before_update$$
create trigger task_before_update
before update on task
for each row
begin
if (new.owner_id is null && status_id!=1) then	
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='owner not defined', MYSQL_ERRNO=3020;
   end if;
end$$

 
-- 4
drop trigger if exists iteration_after_insert$$
/*create trigger iteration_after_insert
after insert on iteration
for each row
begin
insert into acceptance_test_status
select acceptance_test_id,is_satisfied
from acceptance_test_status 
where is_satisfied =false;


end$$*/


-- 5
drop trigger if exists acceptance_test_status_after_update$$
create trigger acceptance_test_status_after_update
before update on acceptance_test_status
for each row
begin
insert into acceptance_test
select task_id as bug_id
from task 
where acceptance_test_status.is_satisfied = false;


end$$





 
 


 





  


 