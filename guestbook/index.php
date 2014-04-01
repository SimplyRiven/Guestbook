<!DOCTYPE html>

<html>

	<head>
	
		<meta charset="UTF-8"/>
		<title>Guestbook</title>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
	
	</head>
	
	<body>
	
		<?php
		
			$host = "localhost";
			$dbname = "guestbook";
			$username = "guestbook";
			$password = "123pyah";
			
			$dsn = "mysql:host=$host;dbname=$dbname";
			$attr = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
			
			$pdo = new PDO($dsn, $username, $password, $attr);
			
			//Man skall kunna lägga till en användare
			//man skall inte kunna posta tomma inlägg
			//man skall kunna söka efter nyckelord, posts och användare
			//information about information, antal posts och users
			
			if($pdo)
			{
				if(!empty($_POST))
				{
					if(isset($_POST["user_id"]))
					{
						$user_id = filter_input(INPUT_POST, 'user_id');
						$post = filter_input(INPUT_POST, 'post', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW);
						$statement = $pdo->prepare("INSERT INTO posts (user_id, post, date) VALUES (:user_id, :post, NOW())");
						$statement->bindParam(":user_id", $user_id);
						$statement->bindParam(":post", $post);
						$_POST = null;
						if(!empty($post))
						{
							$statement->execute();
						}
						else
						{
							echo '<script> alert("You have to type something in the textfield before you post, noob."); </script>';
						}
					}
					elseif(isset($_POST["name"]))
					{
						$_POST = null;
						$name = filter_input(INPUT_POST, 'name');
						$statement = $pdo->prepare("INSERT INTO users (name) VALUES (:name)");
						$statement->bindParam(":name", $name);
						$statement->execute();
					}
				}

				echo "<ul>";
				echo "<li><a href=\"index.php\">all users</a></li>";
					foreach ($pdo -> query("SELECT * FROM users ORDER BY name") as $row)
					{
						echo "<li><a href=\"?user_id={$row['id']}\">{$row['name']}</a></li>";
					}
				echo "</ul>";
			}
				if(!empty($_GET))
				{
					$_GET = null;
					$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
					$statement = $pdo->prepare("SELECT posts.*,users.name FROM posts JOIN users ON users.id=posts.user_id WHERE user_id=:user_id ORDER BY date");
					$statement->bindParam(":user_id", $user_id);
					
					if($statement->execute())
					{
						while($row = $statement->fetch())
						{
							echo "<p>{$row['date']} by {$row['name']} <br /> {$row['post']}</p>";
						}
					}
					else
					{
						print_r($statement->errorInfo());
					}
				}
				else
				{
					foreach ($pdo -> query("SELECT posts.*,users.name AS user_name FROM posts JOIN users ON users.id=posts.user_id ORDER BY date") as $row)
						{
							echo "<p>{$row['date']} by {$row['user_name']} <br /> {$row['post']}</p>";
						}
				}
		
		?>
		
		<form action="index.php" method="POST">
		
			<p>
				<label for="user_id"> User: </label>
				<select name="user_id">
				<?php
					foreach ($pdo -> query("SELECT * FROM users ORDER BY name") as $row)
					{
						echo "<option value={$row['id']}>{$row['name']}</option>";
					}
				?>
				
				</select>
			</br>
				<label for="post"> Post: </label>
				<input type="text" name="post" />
				<input type="submit" value="Post" />
			</p>
		
		</form>
		
		<form action="index.php" method="POST">
			<p>
				<label for="name"> Add user: </label>
				<input type="text" name="name" />
				<input type="submit" value="Add" />
			</p>
		</form>
		
		<?php
		
		?>
	
	</body>
	
</html>