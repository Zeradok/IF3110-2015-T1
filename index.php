<!DOCTYPE HTML>
<html>
<head>
<link rel = "stylesheet" type = "text/css" href = "style.css">
<title>Simple StackExchange</title>
<?php
$db = new mysqli('localhost', 'root', '', 'stackexchange');

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$topic = mysqli_real_escape_string($db, $_POST['topic']);
	$question = mysqli_real_escape_string($db, $_POST['question']);
	if ($_POST['askid'] != 0)
		$db->query("UPDATE questions SET Topic = '$topic', Question = '$question', Name = '$name', Email = '$email', Datetime = NOW() WHERE QuestionID = '$_POST[askid]'");
	else
		$db->query("INSERT INTO questions (Votes, Answers, Topic, Question, Name, Email, Datetime) VALUES (0, 0, '$topic', '$question', '$name', '$email', NOW())");
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$db->query("DELETE FROM questions WHERE QuestionID = $id");
	}
}
?>
</head>
<body>
<div class = "maindiv">
<h1><a href="index.php" id = "title">Simple StackExchange</a></h1>
<div class = "searchdiv"><form action="index.php" method="GET"><input type = "text" name = "searchq" autofocus> <input type = "submit" value = "Search"></form></div>
Cannot find what you are looking for? <a href = "ask.php" class = "yellow">Ask here</a><br><br>
<h2 class = "left">Recently Asked Questions</h2>
<table>
<?php
if (isset($_GET['searchq'])) {
	$searchq = $_GET['searchq'];
	$query = "SELECT * FROM questions WHERE Topic LIKE '%$searchq%' OR Question LIKE '%$searchq%'";
}
else {
	$query = "SELECT * FROM questions";
}

if (!$result = $db -> query($query)) {
	die ('Error query	 [' . $db->error . ']');
}
while ($row = $result->fetch_assoc()) {
	if (strlen($row['Question']) > 50) {
		$qcontent = substr($row['Question'], 0, 50) . '...';
	}
	else {
		$qcontent = $row['Question'];
	}
	echo '<tr>';
	echo '<td class = "votes">' . $row['Votes'] . '<br>Votes</td>';
	echo '<td class = "answers">' . $row['Answers'] . '<br>Answers</td>';
	echo '<td class = "content"><a href = "answer.php?id=' . $row['QuestionID'] . '">' . $row['Topic'] . '</a><br>' . $qcontent . '</td>';
	echo '<td class = "asked">asked by <a class = "blue">' . $row['Name'] . '</a> at <a class = "blue">' . $row['Datetime'] . '</a> | <a href = "ask.php?id=' . $row['QuestionID'] . '" class = "yellow">edit</a> | <a class = "red" href = "index.php?id=' . $row['QuestionID'] . '">delete</a></td>';
	echo '</tr>';
}
echo '<tr></tr>';
$db->close();
?>
</table>
</div>
</body>
</html>
