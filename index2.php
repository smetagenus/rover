<?php
/**
 * Mars Rover 
 * 
 * Реализация программы-марсохода
 *    
 * @author Belyaev Mihail
*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// Класс Полигон
class Polygon
{
  public static $sizeX = "10";
  public static $sizeY = "10";
  
  public static function setSize($x,$y) // функция для задания размеров полигона
    {
        self::$sizeX = $x;
        self::$sizeY = $y;
    }
  public static function getSizeX() // функция для получения размеров полигона
    {
        return self::$sizeX;
    }  
  public static function getSizeY() // функция для получения размеров полигона
    {
        return self::$sizeY;
    }

}

// Класс Ровер
class Rover
{
  public $posX = "0";
  public $posY = "0";
  public $side = "N";
  public $startPosX = "0";
  public $startPosY = "0";
  public $startSide = "N";
}

// Класс Позиция
class Position
{
  public static $posX = "0";
  public static $posY = "0";

  public static function setPos($x,$y) // функция для задания координат позиции
    {
        self::$posX = $x;
        self::$posY = $y;
    }
  public static function getPosX() 
    {
        return self::$posX;
    }  
  public static function getPosY() 
    {
        return self::$posY;
    }
    
  /**
   * Проверка, находится ли ровер в пределах полигона
   *
   * @param int $sizeX размер полигона по оси X
   * @param int $sizeY размер полигона по оси Y
   * @param int $posX позиция ровера по оси X
   * @param int $posY позиция ровера по оси Y      
   * @return bool      
   **/           
  public static function checkPos($sizeX,$sizeY,$posX,$posY)
    {
        if ($posX > $sizeX or $posX < 0 or $posY > $sizeY or $posY < 0)
          return false;
        else
          return true;
    } 
}

// Класс Ориентация
class Orientation
{
  public static $side = "N";
  
  public static function checkSide($s) // функция для проверки правильности направления
    {
        if ($s == "N" || $s == "S" || $s == "E" || $s == "W") 
          return true; 
        else 
          return false;
    }
  public static function setSide($s) // функция для задания направления
    {
        if (self::checkSide($s))
          self::$side = $s;
    }
  public static function getSide() 
    {
        return self::$side;
    }   
}

/**
 * Класс Движение
 * 
 *   
*/ 

class Moving
{
  public static function checkMove($x)  // функция проверки команд
  {
    if ($x == "L" || $x == "R" || $x == "M")
      return true;
    else
      return false;
  }
  
  public static function moveTo($x)  // функция движения: определяет команду, и вызывает соответствующий обработчик
  {
    if ($x == "L") self::goLeft();
    if ($x == "R") self::goRight();
    if ($x == "M") self::goForward();
  }

  public function goLeft()
  {
    $side = Orientation::getSide();
    if ($side == "N") $newSide = "W";
    if ($side == "W") $newSide = "S";
    if ($side == "S") $newSide = "E";
    if ($side == "E") $newSide = "N";
    Orientation::setSide($newSide);
  }
  
  public function goRight()
  {
    $side = Orientation::getSide();
    if ($side == "N") Orientation::setSide("E");
    if ($side == "W") Orientation::setSide("N");
    if ($side == "S") Orientation::setSide("W");
    if ($side == "E") Orientation::setSide("S"); 
  }
  
  public function goForward()
  {
    $side = Orientation::getSide();
    $x = $newX = Position::getPosX();
    $y = $newY = Position::getPosY();
    if ($side == "N") $newY = $y + 1;
    if ($side == "S") $newY = $y - 1;
    if ($side == "E") $newX = $x + 1;
    if ($side == "W") $newX = $x - 1;
    Position::setPos($newX,$newY);
  }
}


// Класс Парсер
class Parser
{
  public static $roverCnt = 0;
  public static $linesArray = array();
  
  public static function parsInput($s) // Парсер входных параметров
  {
    $error = "Неверный ввод данных!<br>";
    $success = true; // По умолчанию надеемся, что ф-ция отбработает успешно 
    self::$linesArray = $lines = explode("\n", $s); // Получаем массив строк
    if (count($lines) % 2 == 1 && count($lines) > 2)  // Проверяем нечетность
    {
      self::$roverCnt = (count($lines)-1)/2; // Количество роверов
    } 
    else 
    {
      echo $error;
      $success = false;
    }
    return $success;
  }

  public static function parsPolygon($s) // Парсер размеров полигона
  {
    $error = "Неверно заданы размеры полигона!<br>";
    $success = true; // По умолчанию надеемся, что ф-ция отбработает успешно 
    $s = trim($s);
    $size = explode(" ", $s); // получаем массив параметров полигона
    if (count($size) == 2)
    {
      $x = $size[0];
      $y = $size[1];
      if (is_numeric($x) && is_numeric($y))
        {
          Polygon::setSize($x,$y);
        }
      else
        { 
          echo $error;
          $success = false;
        }  
    } 
    else
    { 
      echo $error;
      $success = false;
    }
    return $success;
  }
  
  public static function parsPosition($s,$n) // Парсер координат ровера
  {
    $error = "Неверно заданы координаты ровера №".$n."!<br>";
    $success = true; // По умолчанию надеемся, что ф-ция отбработает успешно 
    $s = strtoupper(trim($s));
    $pos = explode(" ", $s); // получаем массив параметров ровера 
    if (count($pos) == 3)
    {
      $posX = $pos[0];
      $posY = $pos[1];
      $side = $pos[2];
      if (is_numeric($posX) && is_numeric($posY) && Orientation::checkSide($side))
      {
        Position::setPos($posX,$posY);
        Orientation::setSide($side);
      }
      else
      { 
        echo $error;
        $success = false;
      }
    }
    else
    { 
      echo $error;
      $success = false;
    }
    return $success;  
  }
  
  public static function parsMoving($s,$n) // Парсер команд движения ровера
  {
    $error = "Неверно заданы команды для ровера №".$n."!<br>";
    $success = true; // По умолчанию надеемся, что ф-ция отбработает успешно 
    $s = strtoupper(trim($s));
    $commands = str_split($s); // получаем массив команд 
    foreach ($commands as $value) {
      if (Moving::checkMove($value))
        {
          Moving::moveTo($value);
          if (Position::checkPos(Polygon::getSizeX(),Polygon::getSizeY(),Position::getPosX(),Position::getPosY()) == false)
            {
              echo "Ровер ".$n." выехал за пределы полигона! (". Position::getPosX()  . " " . Position::getPosY() . " " . Orientation::getSide() .")<br>";
              break;
            }
          //echo "-Измененная позиция ровера: ". Position::getPosX()  . " " . Position::getPosY() . " " . Orientation::getSide() ."<br>";
        }
      else 
        {
          echo $error;
          //echo $value . "<br>";
          $success = false;
          break;
        }
    }
    return $success;
  }
}




// ------------------------

// Входные параметры
$_GET['textarea']="5 5
1 2 N
LMLMLMLMM
4 3 E
MMRMMRMRRM";

// Сама программа
if (Parser::parsInput($_GET['textarea'])) // парсим входные данные и исполняем команды
{
  $lines = Parser::$linesArray;
  if (Parser::parsPolygon($lines[0]))
  {
     $rover[] = new Rover;
     for ($n = 1; $n <= Parser::$roverCnt; $n++) {
        
            if (Parser::parsPosition($lines[$n*2-1],$n))   // Координаты n-го ровера
              {
                $rover[$n]->startPosX = Position::getPosX();
                $rover[$n]->startPosY = Position::getPosY();
                $rover[$n]->startSide = Orientation::getSide();
              } else break;
            if (Parser::parsMoving($lines[$n*2],$n))     // Команды для n-го ровера
              {
                $rover[$n]->posX = Position::getPosX();
                $rover[$n]->posY = Position::getPosY();
                $rover[$n]->side = Orientation::getSide();
              } else break;
        echo "Начальная позиция ровера №".$n.": ". $rover[$n]->startPosX  . " " . $rover[$n]->startPosY . " " . $rover[$n]->startSide ."<br>";
        echo "Измененная позиция ровера №".$n.": ". $rover[$n]->posX  . " " . $rover[$n]->posY . " " . $rover[$n]->side ."<br>"; 
    } 
  }  

}

?>