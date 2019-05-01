### Todoist - Habit Chain

## What is it
* Break the chain habit tracking for todoist

## Prerequisites
* This script uses the php library found here: https://github.com/FabianBeiner/Todoist-PHP-API-Library
  * Easiest to install via composer: `composer require fabian-beiner/todoist-php-api-library`

## How to use
* Obtain your API key from https://todoist.com (Under Settings > Integrations)
* Enter Your Todoist API key into `todoist-habit.php`,
* Host the `todoist-habit.php` on a server
* Use IFTTT or similar site/script to trigger the php page to run every morning at 12:15 AM

### Inspired By: https://github.com/amitness/habitist
