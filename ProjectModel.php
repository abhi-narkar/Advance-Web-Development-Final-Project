<?php

require_once("DemoDB.php");

/** Access to the person table.
 * Put here the methods like getBySomeCriteriaSEarch */
class ProjectModel {
	/** Get member data by member's email or member's last name
     * @param string $login is member's email or last name
	 * @param string $password is member's password
     * @return associative_array table row
     */
    public static function getByLoginPassword($login, $password) {
        $db = DemoDB::getConnection();
        $sql = "SELECT id, first_name, last_name
            FROM member
            WHERE (last_name = :name or email = :email)
			AND password = :password";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":name", $login);
		$stmt->bindValue(":email", $login);
        $stmt->bindValue(":password", $password);
        $ok = $stmt->execute();
        if ($ok) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }


	/** Create a new member
     * @param string $email is member's email
	 * @param string $password is member's password
	 * @param string $first is member's first name
	 * @param string $last is member's last name
     * @return the id of the new member
     */
	public static function addMember($email,$password,$first,$last){
		$db = DemoDB::getConnection();
        $sql = "INSERT INTO member(email,password,first_name,last_name) VALUE(:email,:password,:first,:last)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":email", $login);
        $stmt->bindValue(":password", $password);
		$stmt->bindValue(":first", $first);
        $stmt->bindValue(":last", $last);
        $ok = $stmt->execute();
        if ($ok) {
            $id = $db->lastInsertId();
			return $id;
        }
		else{
			return $stmt->errorInfo();
		}

	}

	/** Get member's id by member's first name and last name
     * @param string $first is member's first name
	 * @param string $last is member's last name
     * @return the id of the member
     */
	public static function getMemberId($first,$last){
		$db = DemoDB::getConnection();
        $sql = "SELECT id
            FROM member
            WHERE first_name = :first
			AND last_name = :last";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":first", $first);
        $stmt->bindValue(":last", $last);
        $ok = $stmt->execute();
        if ($ok) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
	}

	/** Get member's id by member's first name and last name
     * @param string $id is member's id
     * @return the first name and last name of the member
     */
	public static function getMemberName($id){
		$db = DemoDB::getConnection();
        $sql = "SELECT first_name,last_name
            FROM member
            WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $ok = $stmt->execute();
        if ($ok) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
	}

	/** Get all the project role names
     * @return the name list of project role
     */
	public static function getProjectRole(){
		$db = DemoDB::getConnection();
        $sql = "SELECT name
            FROM project_role
			WHERE name != 'Owner'";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute();
        if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
	}

	/** Get project role id
     * @param string $name is project role's name
     * @return id of the project role
     */
	public static function getProjectRoleId($name){
		$db = DemoDB::getConnection();
        $sql = "SELECT id
            FROM project_role
			WHERE name = :name";
        $stmt = $db->prepare($sql);
		$stmt->bindValue(":name", $name);
        $ok = $stmt->execute();
        if ($ok) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
	}


	/** Add member to project
     * @param string $member_id is member's id
	 * @param string $project_id is project's id
	 * @param string $role_id is project role's id
     * @return errors message if errors happen
     */
	public static function addProjectMember($member_id,$project_id,$role_id){
		$db = DemoDB::getConnection();
        $sql = "INSERT INTO project_member(member_id,project_id,role_id,added_at) VALUE(:member,:project,:role,:date)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":member", $member_id);
        $stmt->bindValue(":project", $project_id);
		$stmt->bindValue(":role", $role_id);
        $stmt->bindValue(":date",date('Y-m-d H:i:s'));
        $ok = $stmt->execute();
        if (!$ok) {

			return $stmt->errorInfo();
		}
	}

	/** Create a new project
     * @param string $name is project's name
	 * @param string $descript is project's description
	 * @param string $admin_id is project's administrator id
     * @return the id of the new project
     */
	public static function createProject($name,$descript,$admin_id){
		$db = DemoDB::getConnection();
        $sql = "INSERT INTO project(name,description,administrator_id,created_at) VALUE(:name,:descript,:id,:date)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":descript", $descript);
		$stmt->bindValue(":id", $admin_id);
        $stmt->bindValue(":date",date('Y-m-d H:i:s'));
        $ok = $stmt->execute();
        if ($ok) {
            $id = $db->lastInsertId();

			$addOwner = self::addProjectMember($admin_id,$id,1);
			if($addOwner == null){
				return $id;
			}
			else{
				return $addOwner;
			}
        }
		else{
			return $stmt->errorInfo();
		}
	}

	/** Create a new iteration
     * @param string $deadline is project's deadline
	 * @param string $project_id is project's id
     * @return the id of the new iteration
     */
	public static function createIteration($deadline,$project_id){
		$db = DemoDB::getConnection();
        $sql = "INSERT INTO iteration(deadline,project_id) VALUE(:deadline,:id)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":deadline", $deadline);
		$stmt->bindValue(":id", $project_id);
        $ok = $stmt->execute();
        if ($ok) {
            $id = $db->lastInsertId();
			return $id;
        }
		else{
			return $stmt->errorInfo();
		}
	}

	/**Create the current_iteration view for database
	* @param database $db is the database stores the view
	*/
	private static function createViewCurrentIteration($db){
        $sql = "DROP VIEW IF EXISTS CURRENT_ITERATION;

				CREATE VIEW CURRENT_ITERATION AS
				select id, project_id, min(deadline)as deadline
					from iteration
					where deadline > now()
				group by project_id;
				";
		$stmt = $db->prepare($sql);
		$ok = $stmt->execute();
	}

	/** Get all the required details of a project
	* @param string $id is the project's id
	* @return all the details
	*/
	public static function getProjectDetails($id){
		$db = DemoDB::getConnection();
		ProjectModel::createViewCurrentIteration($db);
        $sql = "SELECT project.name as project_name, description, project.created_at, first_name, last_name, project_role.name as role_name, deadline, current_iteration.id as iteration_id
				FROM project
                INNER JOIN project_member
					ON project.id = project_member.project_id
                INNER JOIN member
					ON member_id = member.id
                INNER JOIN project_role
					ON role_id = project_role.id
				LEFT OUTER JOIN current_iteration
					ON project.id = current_iteration.project_id
				WHERE project.id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id);
		$ok = $stmt->execute();
		if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
	}

	/** Get all the required details of a project
	* @param string $owner is the project's administrator id
	* @return the details
	*/
	public static function getProjectSmallDetail($owner){
		$db = DemoDB::getConnection();
		$sql = "SELECT id, name, description, created_at
				FROM project
				WHERE administrator_id = :id";
		$stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $owner);
		$ok = $stmt->execute();
		if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
	}

	/** Get all the required details of the current iteration's features
	* @param string $project_id is the project's id
	* @return all the details
	*/
	public static function getCurrentIterationFeaturesDetails($project_id){
		$db = DemoDB::getConnection();
		ProjectModel::createViewCurrentIteration($db);
		$sql = "SELECT feature.id as feature_id, title,functionality,benefit,user_role.name as user_role_name
				FROM feature
				INNER JOIN current_iteration
					ON feature.project_id = current_iteration.project_id
					AND feature.iteration_id = current_iteration.id
				INNER JOIN user_role
					ON user_role_id = user_role.id
				WHERE feature.project_id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $project_id);
		$ok = $stmt->execute();
		if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
	}

	/** Create a new feature
     * @param string $title is feature's title
	 * @param string $func is feature's functionality
	 * @param string $benefit is feature's benefit
	 * @param string $iteration_id is feature's iteration id
	 * @param string $project_id is feature's project id
     * @return the id of the new feature
     */
	public static function createFeature($title,$func,$benefit,$iteration_id,$project_id,$user_role_id){
		$db = DemoDB::getConnection();
		$sql = "INSERT INTO feature(title,functionality,benefit,iteration_id,project_id,user_role_id) VALUE(:title,:func,:ben,:iteration,:project,:user)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":title", $title);
		$stmt->bindValue(":func", $func);
		$stmt->bindValue(":ben", $benefit);
		$stmt->bindValue(":iteration", $iteration_id);
		$stmt->bindValue(":project", $project_id);
		$stmt->bindValue(":user", $user_role_id);
        $ok = $stmt->execute();
        if ($ok) {
            $id = $db->lastInsertId();
			return $id;
        }
		else{
			return $stmt->errorInfo();
		}
	}

	/** Get the feature's details
	 * @param string $id is the feature's id
     * @return the details
     */
	public static function getFeatureById($id){
		$db = DemoDB::getConnection();
		$sql = "SELECT feature.id,title,functionality,benefit, user_role.name as user_role_name
				FROM feature
				INNER JOIN user_role
					ON user_role_id = user_role.id
				WHERE feature.id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id);
		$ok = $stmt->execute();
		if ($ok) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
	}

	/** Get all the user role details
     * @return the list of user role
     */
	public static function getUserRole(){
		$db = DemoDB::getConnection();
        $sql = "SELECT *
            FROM user_role";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute();
        if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
	}
  /** Get all the required details of a features
  * @param string $id is the feature's id
  * @return all the details
  */
  public static function getFeatureDetails($id){
    $db = DemoDB::getConnection();
    ProjectModel::createViewCurrentIteration($db);
        $sql = "select title, functionality,benefit,priority,user_role.name as user_name, iteration_id
                from feature
                INNER Join user_role on feature.user_role_id = user_role.id
                where feature.id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id", $id);
              $ok = $stmt->execute();
    if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
  }

  /** Get all the required details of a features
  * @param string $id is the feature's id
  * @return all the details
  */
  public static function getAcceptanceTest($id){
    $db = DemoDB::getConnection();
    ProjectModel::createViewCurrentIteration($db);
        $sql= "select description,is_satisfied
                from feature
                INNER Join acceptance_test on feature.id = acceptance_test.feature_id
                INNER join acceptance_test_status on acceptance_test.id = acceptance_test_status.acceptance_test_id
                where feature.id=:id;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id", $id);
              $ok = $stmt->execute();
    if ($ok) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
  }
  /** insert an acceptance_test for a feature
  * @param string $id is the feature's id
  * @param string $description is the test description
  * @return new id of the new acceptance test
  */
  public static function addTest($id,$description,$iteration_id,$satisfied){
    $db = DemoDB::getConnection();
        $sql="Insert Into acceptance_test(description,feature_id) values(:desc,:id)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":desc", $description);
      $ok = $stmt->execute();
      if ($ok) {
          $test_id = $db->lastInsertId();
          return addTestStatus($iteration_id,$test_id,$satisfied);
      }
      else{
      return $stmt->errorInfo();
      }

  }

  private function addTestStatus($iteration_id,$test_id,$satisfied){
    $db = DemoDB::getConnection();
        $sql="Insert Into acceptance_test_status(iteration_id,acceptance_test_id,is_satisfied) values(:Id,:test,:satisfied)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $iteration_id);
        $stmt->bindValue(":test", $test_id);
        $stmt->bindValue(":satisfied", $satisfied);
      $ok = $stmt->execute();
      if ($ok) {

        return null;
      }
      else{
      return $stmt->errorInfo();
      }


  }

}

?>
