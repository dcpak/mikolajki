<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Losowator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Eater&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: bisque;
      color: #111;
      width: 360px;
      padding: 20px;
      box-sizing: border-box;
    }

    body * {
      box-sizing: border-box;
      font-family: 'Eater', cursive;
      font-size: 20px;
    }

    body > div {
      text-align: center;
    }

    body > div span {
      font-family: Arial, serif;
      font-size: 5em;
    }

    input {
      font-family: Arial, serif;
      width: 100%;
      height: 40px;
      padding: 4px 8px;
      border: 1px solid #111111;
    }

    input:focus {
      outline: none;
      border-color: darkviolet;
    }

    section {
      float: left;
      display: inline-block;
      width: 100%;
      margin-bottom: 15px;
    }

    section:last-child {
      text-align: center;
    }

    section div {
      float: left;
      display: inline-block;
      width: 60%;
    }

    section div:first-child {
      width: 40%;
      text-align: right;
      padding: 5px 20px 0 0;
    }

    section div:first-child::after {
      content: ':';
    }

    button {
      padding: 4px 16px;
      border-radius: 5px;
      border-width: 0;
      background-color: #f60;
      color: #fff;
      cursor: pointer;
    }

  </style>
</head>
<body>
<?php
const NUMBER_OF_PEOPLE = 3;
const FILE_NUMBERS = 'numbers_taken.txt';
const ALERT_ERROR_WRITE = '<div>Nie udało się zapisać wylosowanego numerka, rocketchatuj do administratora.</div>';
const ALERT_ERROR_NUMBER = '<div>Numer spoza zakresu.</div>';
const ALERT_ERROR_PASSWORD = '<div>Numer dobry, tylko hasło nie bardzo.<div>';
const TPL_INFO_NUMBER = '<div>Twój szczęśliwy numerek:<br /><span><number/></span></div>';

if(@$_POST['number'] != '' && @$_POST['pswd'] != '') {
  $numberUser = intval($_POST['number']);
  if($numberUser > 0 && $numberUser <= NUMBER_OF_PEOPLE) {
    $numberExists = false;
    $numberShow = false;
    $numbersTaken = array();
    $numbersMatches = array();
    if(file_exists(FILE_NUMBERS)) {
      $lines = file(FILE_NUMBERS, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
      foreach($lines as $line) {
        $line = explode(' ', $line);
        $numbersTaken[] = $line[2];
        $numbersMatches[$line[2]] = $line[0];
        if(password_verify($_POST['number'], $line[0])) {
          $numberExists = true;
          if(password_verify($_POST['pswd'], $line[1]))
            echo str_replace('<number/>', $line[2], TPL_INFO_NUMBER);
          else
            echo ALERT_ERROR_PASSWORD;
        }
      }
    }
    if(!$numberExists) {
      $numbersAvailable = array();
      for($i = 1; $i <= NUMBER_OF_PEOPLE; $i++) {
        if(!in_array($i, $numbersTaken)
          && $i != $numberUser
          && !(
            isset($numbersMatches[$numberUser])
            && password_verify($i, $numbersMatches[$numberUser])
          )
        )
          $numbersAvailable[] = $i;
      }
      $numberTaken = $numbersAvailable[rand(0, count($numbersAvailable) - 1)];
      $hashes = array();
      $hashes[] = password_hash($_POST['number'], PASSWORD_DEFAULT);
      $hashes[] = password_hash($_POST['pswd'], PASSWORD_DEFAULT);
      if($hashes[0] && $hashes[1]) {
        $fp = @fopen(FILE_NUMBERS, 'a');
        if($fp === false) {
          echo ALERT_ERROR_WRITE;
        } else {
          if(@fwrite($fp, $hashes[0].' '.$hashes[1].' '.$numberTaken."\n") === false)
            echo ALERT_ERROR_WRITE;
          else
            echo str_replace('<number/>', $numberTaken, TPL_INFO_NUMBER);
          @fclose($fp);
        }
      } else {
        echo ALERT_ERROR_WRITE;
      }
    }
  } else {
    echo ALERT_ERROR_NUMBER;
  }
} else {
  ?>
<form name="formSelect" method="post" action="index.php">
  <section>
    <div>numer</div>
    <div><input type="text" name="number" placeholder="Twój z listy" /></div>
  </section>
  <section>
    <div>hasło</div>
    <div><input type="password" name="pswd" placeholder="Hasełeczko" /></div>
  </section>
  <section>
    <button type="submit">DZWONIĄ DZWONKI SAŃ</button>
  </section>

</form>
<?php
}
?>
</body>
</html>