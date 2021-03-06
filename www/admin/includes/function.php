<?php



class Tools {		


	public static function checkLogin() {
			if(!isset($_SESSION['user_id'])) {
				static::redirect("index.php", "");
			}
		}


	public static function doAdminRegister($dbconn, $input){

			$hash = password_hash($input['password'], PASSWORD_BCRYPT);

	 		$stmt = $dbconn->prepare("INSERT INTO blog_member(username,hash,email) VALUES (:fn,:ln,:e)");

	 		$stmt->bindParam(":fn", $input['username']);
			$stmt->bindParam(":ln", $input['password']);
			$stmt->bindParam(":e", $input['email']);
	 		$stmt->execute(); 	
	 		return true;


	}



	public static function doAdminLogin($dbconn, $input){
 			$result = [];

	 		//INSERT DATA INTO TABLE
	 		$stmt = $dbconn->prepare("SELECT * FROM  blog_member WHERE email = :e  ");

	 		//bind params

	 		$stmt->bindParam(":e", $input['email']);
	 		$stmt->execute();
	 			
	 		
	 		$row = $stmt->fetch(PDO::FETCH_BOTH);

	 	if( ($stmt->rowCount() != 1) || !password_verify($input['password'], $row['hash']) ) {
			static::redirect('index.php?message=Invalid details');
			exit();
		}else{

				$result[] = true;
				$result[] = $row['memberID'];					
				$result[] = $row['username'];
		}


		return $result;
	}
															



	public static function redirect($loc) {header("Location: ".$loc);}
		


	public static function doesEmailExist($dbconn, $email){
			$result = false;

			$stmt = $dbconn->prepare("SELECT email FROM admin WHERE  ");

			#bind parameter
			$stmt->bindParam(":e", $email);
			$stmt->execute();

			#get number of rolls returned
			$count = $stmt->rowCount();

			if($count > 0){
				$result = true;
			}

			return $result;	
		}


	


			




	public static function addCategory($dbconn,$input){


			$stmt = $dbconn->prepare("INSERT INTO category(cat_name) VALUES (:c)");

	 		//bind params
			$stmt->bindParam(":c", $input['cat_name']);
			$stmt->execute();
			return true;
  		//header("Location:category.php?success=$success");


	}


	public static function showCategory($dbconn){
				$stmt = $dbconn->prepare("SELECT * FROM category ");
				$stmt->execute();
				$result = "";

	 		    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	 			$cat_id = $row['cat_id'];
	 			$cat_name = $row['cat_name'];
	 			
	 			$result .= "<tr>";
	 			$result .= "<td>" .$cat_id.  "</td>";
	 			$result .= "<td>" .$cat_name.  "</td>";

	 			$result .=   "<td><a href='category.php?action=edit&cat_id=$cat_id&cat_name=$cat_name'>edit</a></td>";
				$result .=	 "<td><a href='category.php?act=delete&cat_id=$cat_id'>delete</a></td> ";
	 			$result .= "</tr>";


	 		}
	  return $result;

	}

	public static function editCategory($dbconn,$input){

		$stmt = $dbconn->prepare("UPDATE  category SET cat_name = :cn 	WHERE cat_id = :i ");

		$stmt->bindParam(":cn", $input['cat_name']);
		$stmt->bindParam(":i", $input['cat_id']);
		 $stmt->execute();
		 	$success = "category edited!";
  		header("Location:category.php?success=$success");





	}

	public static function deleteCat($dbconn, $input){


		$stmt = $dbconn->prepare("DELETE FROM  category WHERE cat_id = :i ");

		$stmt->bindParam(":i", $input);
		 $stmt->execute();
		 return true;

	}
	

	public static function getCategory($dbconn)
	{
			$stmt = $dbconn->prepare("SELECT * FROM category ");
				 $stmt->execute();
				 $result = "";

	 		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	 			$cat_id = $row['cat_id'];
	 			$cat_name = $row['cat_name'];

	 			$result .= "<option value=$cat_id>"  .$cat_name ."</option>";

	 		}
	 		return $result;
	}






	public static function UploadFiles($file, $name, $uploadDir) 
	{

		$data = [];
		$rnd = rand (0000000000,9999999999);

		$strip_name = str_replace (" ","_",$file[$name]['name']);

		$filename = $rnd.$strip_name;
		$destination = $uploadDir .$filename;

		if (!move_uploaded_file($file[$name]['tmp_name'], $destination)){
			$data[] = false;
		} else {
			$data[] = true;
			$data[] = $destination;
		}

		return $data;
		}


	public static function addPost($dbconn,$input){

				  $date = date('Y-m-d H:i:s');

				 
                  $dateF = "";
				  

                  $stmt = $dbconn->prepare("INSERT INTO blog_posts(cat_id,postTitle,postCont,postDate,admin_id,image_path,flag) 
                  	VALUES (:t,:a,:c,:p,:y,:im,:sl)");

	 					

	 			$data = [
	 					':t' => $input['cat'],
	 					':a' => $input['title'],
	 					':sl' => $dateF,
	 					':c' => $input['post'],
	 					':p' => $date,
	 					':y' => $input['admin_id'],
	 					':im' => $input['loc']

	 					

	 					];

	 			if($stmt->execute($data)){

                  $success = "Post Added";
                  header("Location:add_post.php?message=$success");

                 }

             else
                 
                {        
                		 $success = "Error";
                  header("Location:add_post.php?message=$success");


               }
                 

		}

	



		
	


		



	 	

	public static function deletePost($dbconn, $input){


		$stmt = $dbconn->prepare("DELETE FROM  blog_posts WHERE postID = :i ");

		$stmt->bindParam(":i", $input);
		 $stmt->execute();
		 $success = "Post deleted!";
  		header("Location:dashboard.php?success=$success");

}







	public static function editProduct($dbconn,$input,$destination){




                  $stmt = $dbconn->prepare("UPDATE book  
                  	SET title =:t,
                  		author = :a,
                  		cat_id = :c,
                  		price = :p,
                  		year = :y,
                  		isbn =:i,
                  		image_path =:im 



                  	WHERE book_id = :id");

	 		//bind params

	 			$data = [
	 					':t' => $input['title'],
	 					':a' => $input['author'],
	 					':c' => $input['cat'],
	 					':p' => $input['price'],
	 					':y' => $input['year'],
	 					':i' => $input['isbn'],
	 					':id' => $input['book_id'],
	 					':im' => $destination,

	 					];


	 			if($stmt->execute($data)){;

                  $success = "Product Edited";
                  header("Location:product.php?success=$success");

                 }

             else
                 
                {        
                		 $success = "Product Edit failed";
                  header("Location:product.php?success=$success");


               }

		


			}




	public static	function newCat($dbconn,$id){
				 $stmt = $dbconn->prepare("SELECT * FROM category WHERE cat_id = :id ");
				 $stmt->bindParam(":id", $id);
				 $stmt->execute();
				

	 			$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 			return $row;

	 		}


	

	public static	function rowCount($dbconn,$place){

		$stmt = $dbconn->prepare("SELECT count(*) FROM $place ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $rowCount = $row[0];
         return $rowCount;


	}
	public static function rowCount1($dbconn){

		$stmt = $dbconn->prepare("SELECT count(*) FROM users WHERE status = 'online' ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $rowCount = $row[0];
         return $rowCount;
     }




	public static  function curNav($page){

     	$curPage = basename($_SERVER['SCRIPT_FILENAME']);

     	if($curPage == $page){
     		echo "class = 'selected'";
     	}

     }


	public static function fetchCat($dbconn, $callback)
	{
			$stmt = $dbconn->prepare("SELECT * FROM category ");
				 $stmt->execute();
				#$row = $stmt->fetch(PDO::FETCH_ASSOC);


				$callback($stmt);

     }
     public static function displayError($show,$input){

			if(isset($show[$input])){


				echo '<span class="form-error">'.$show[$input]. '</span>' ;
				//return true;
        }


    }

    public static function achieveNow($dbconn,$id,$date){

    		  $stmt = $dbconn->prepare("INSERT INTO archive(postID,date) 
                  	VALUES (:t,:a)");

    		  $stmt->bindParam(":t", $id);
    		  $stmt->bindParam(":a", $date);
    		   if($stmt->execute()){
    		   	self::updatePost($dbconn,$id);


    		   }


    }


    public static function updatePost($dbconn,$id){

    			  $flag = "archive";
                  $stmt = $dbconn->prepare("UPDATE blog_posts 
                  	SET flag = :t  	WHERE postID = :id");

	 		//bind params

	 			$data = [
	 					':t' => $flag,
	 					':id' => $id

	 					];


	 			if($stmt->execute($data)){;

                  $success = "Product Edited";
                  header("Location:dashboard.php?success=$success");

                 }

             else
                 
                {        
                		 $success = "Flag not updated";
                  header("Location:dashboard.php?success=$success");


               }
           }

           public static function fetchArchive($dbconn){
           	$result = "";
           	$stmt = $dbconn->prepare("SELECT DISTINCT date FROM archive ");
           	$stmt->execute();
           	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
           		  $date = $row['date'];
           		  $convert_date = strtotime($date);
        		  $month = date('F',$convert_date);
        		  $year = date('Y',$convert_date);
                  $dateF = $month . " " . $year;

           		
           		$result .= "<li><a href='archive.php?date=$dateF'> $dateF</a></li>";

           	}
           	return $result;
           }




           public static function archiveView($dbconn, $id)
	{
		$stmt = $dbconn->prepare("SELECT * FROM blog_posts WHERE formatDate = :d ORDER BY postID DESC");
		$stmt->bindParam(":d", $id);
		$stmt->execute();
	
		if($stmt->rowCount()>0)
		{
			while($row=$stmt->fetch(PDO::FETCH_ASSOC))
			{

//cat_id,postTitle,postCont,postDate,admin_id,image_path


				$post_id = $row['postID'];
	 			$title = $row['postTitle'];
	 			$admin_id = $row['admin_id'];
	 			$cat_id = $row['cat_id'];
	 			$post = $row['postCont'];
	 			$date = $row['postDate'];
	 			$image_path = $row['image_path'];

	 			$get = self::getAdmin($dbconn,$admin_id);
	 			$admin = $get['username'];

	 			$get = self::getCat($dbconn,$cat_id);
	 			$cat = $get['cat_name'];
				?>


				

				 <div class="blog-post">
            <h2 class="blog-post-title"><?php echo $title ?></h2>
            <p class="blog-post-meta"><?php echo 'Posted on '.date('jS M Y H:i:s', strtotime($date)).''?>
             By <a href="#"><?php echo $admin."<br/>" ?></a> Category:<?php echo $cat ?> </p>
            
             <?php echo  "<img src='admin/". $image_path."' height='200px' width='200px' style='float:left'/>"; echo substr($post, 0, 300) . '<strong><a href="viewpost.php?id='.$row['postID'].'">Read More</a></strong>';	 ?>
           </p>
          </div>


                <?php
			}





		}
		else
		{
			?>
            <tr>
            <td>No product posted yet</td>
            </tr>
            <?php
		}
		
	}




	public static function getAdmin($dbconn,$id){
				 $stmt = $dbconn->prepare("SELECT * FROM blog_member WHERE memberID = :id ");
				 $stmt->bindParam(":id", $id);
				 $stmt->execute();
				

	 			$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 			return $row;


	 		}

	 public static function getCat($dbconn, $id){
				 $stmt = $dbconn->prepare("SELECT * FROM category WHERE cat_id = :id ");
				 $stmt->bindParam(":id", $id);
				 $stmt->execute();
				

	 			$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 			return $row;


	 		}	

}

 

