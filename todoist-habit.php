<?php

  require __DIR__ . '/vendor/autoload.php';

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  date_default_timezone_set('US/Eastern');

  #############################################################################
  //ENTER API KEY HERE
  $Todoist = new FabianBeiner\Todoist\TodoistClient('');
  #############################################################################


  $allTasks = $Todoist->getAllTasks();

  #Loop through tasks
  foreach ($allTasks as $task) {
    $taskID = $task->id;
    $taskName = $task->content;
    $taskDueDate = $task->due->date;
    $taskDateString = $task->due->string;
    #Determine if task is of type streak
    if (isTaskType($taskName)) {
      #echo "<pre>"; var_dump($task); echo "</pre>";
      #Get Current Streak
      $currentStreak = getStreakString($taskName);

      #Determine if task is due yesterday or today
      if (dueYesterday($taskDueDate)) {

        #Verbose
        echo "
        <b>Task ID</b> $taskID
        <br />
        <b>Task Due Yesterday: </b> $taskName
        <br />
        <b>Task Due Date: </b> $taskDueDate
        <br />
        <b>Task Date String: </b> $taskDateString
        <br />
        ";

        #Save Streak to Comments
        $Todoist->createCommentForTask($taskID, "Last Streak: ".$currentStreak);

        #Reset Streak
        $taskNewName = resetStreak($taskName);
        $content["priority"] = 1;
        $content["due_string"] = "$taskDateString starting tod";
        $Todoist->updateTask($taskID, $taskNewName, $content);

      } else if (dueToday($taskDueDate)) {

        #Verbose
        echo "
        <b>Task ID</b> $taskID
        <br />
        <b>Task Due Today: </b> $taskName
        <br />
        <b>Task Due Date: </b> $taskDueDate
        <br />
        <b>Task Date String: </b> $taskDateString
        <br />
        ";

        #Increment Streak and Retitle Tasks
        $taskNewStreak = incrementStreak($currentStreak);
        $taskNameOnly = getTaskOnly($taskName);
        $taskNewName = $taskNameOnly." ".$taskNewStreak;
        $Todoist->updateTask($taskID, $taskNewName);
      }
    }

  }

  function isTaskType($task) {
    if (strpos($task, '[streak') != false) {
      return true;
    }
    return false;
  }

  function dueToday($dueDate) {
    $Today = date("Y-m-d");
    #echo "Today: ".$Today."<br />";
    if ($Today == $dueDate) {
      return true;
    }
    return false;
  }

  function dueYesterday($dueDate) {
    $Yesterday = date("Y-m-d", strtotime( '-1 days' ));
    #echo "Yesterday: ".$Yesterday."<br />";
    if ($Yesterday == $dueDate) {
      return true;
    }
    return false;
  }

  function resetStreak($taskName) {
    echo "<br / /> <b style=color:red>😭 Resetting Streak 😭</b>";
    $taskShortName = preg_replace('/\[streak \d+\]/', '', $taskName);
    $taskNewName = trim($taskShortName)." [streak 0]";
    return $taskNewName;
  }

  function getStreakString($taskName) {
    preg_match('/streak [0-9]+/', $taskName, $taskShortName);
    return $taskShortName[0];
  }

  function getTaskOnly($taskName) {
    $taskShortName = preg_replace('/\[streak \d+\]/', '', $taskName);
    $taskNewName = trim($taskShortName);
    return $taskNewName;
  }

  function incrementStreak($currentStreak) {
    preg_match('/[0-9]+/', $currentStreak, $currentStreakValue);
    $newStreak = (int)$currentStreakValue[0] + 1;
    return "[streak $newStreak]";
  }
 ?>
